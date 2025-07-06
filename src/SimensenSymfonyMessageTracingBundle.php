<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle;

use Simensen\MessageTracing\Adapter\DefaultTraceStack;
use Simensen\MessageTracing\TracedContainerManager;
use Simensen\MessageTracing\TraceGenerator;
use Simensen\MessageTracing\TraceIdentityGenerator;
use Simensen\MessageTracing\TraceStack;
use Simensen\SymfonyMessageTracingBundle\Middleware\CausationMiddleware;
use Simensen\SymfonyMessageTracingBundle\Middleware\CorrelationMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CausationTracedEnvelopeManager;
use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CorrelationTracedEnvelopeManager;
use Simensen\SymfonyMessenger\MessageTracing\Stamp\MessageTracingStampGenerator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Uid\Uuid;

class SimensenSymfonyMessageTracingBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('trace_generator')->defaultValue(MessageTracingStampGenerator::class)->end()
                ->scalarNode('trace_stack')->defaultValue(DefaultTraceStack::class)->end()
                ->arrayNode('trace_identity')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue(Uuid::class)->end()
                        ->scalarNode('generator')->end()
                    ->end()
                ->end()
                ->arrayNode('messenger')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('middleware')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('causation')->defaultValue('simensen_message_tracing.messenger.middleware.causation')->end()
                                ->scalarNode('correlation')->defaultValue('simensen_message_tracing.messenger.middleware.correlation')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $makeService = function (
            string $property,
            string $interface,
            ?string $implementation = null,
            bool $shouldSetAlias = true,
        ) use ($config, $builder) {
            $serviceId = 'simensen_message_tracing.'.$property;
            $implementation ??= $config[$property];

            $definition = (new Definition($implementation))
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $builder->setDefinition($serviceId, $definition);

            if ($shouldSetAlias) {
                $builder->addAliases([$interface => $serviceId]);
            }
        };

        $makeService('trace_stack', TraceStack::class);
        $makeService('trace_generator', TraceGenerator::class);

        $traceIdentityType = $config['trace_identity']['type'];
        $traceIdentityGenerator = $config['trace_identity']['generator'] ?? MessageTracingStampGenerator::class;

        $makeService(
            'identity.generator',
            TraceIdentityGenerator::class,
            $config['trace_identity']['generator']
        );

        $makeService(
            'messenger.middleware.causation',
            TracedContainerManager::class,
            CausationTracedEnvelopeManager::class,
            false
        );

        $makeService(
            'messenger.middleware.correlation',
            TracedContainerManager::class,
            CorrelationTracedEnvelopeManager::class,
            false
        );

        // Register actual middleware services
        $causationMiddlewareDefinition = (new Definition(CausationMiddleware::class))
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addArgument(new Reference('simensen_message_tracing.messenger.middleware.causation'))
            ->addTag('messenger.middleware', ['priority' => 100]);

        $builder->setDefinition('simensen_message_tracing.middleware.causation', $causationMiddlewareDefinition);

        $correlationMiddlewareDefinition = (new Definition(CorrelationMiddleware::class))
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addArgument(new Reference('simensen_message_tracing.messenger.middleware.correlation'))
            ->addTag('messenger.middleware', ['priority' => 100]);

        $builder->setDefinition('simensen_message_tracing.middleware.correlation', $correlationMiddlewareDefinition);
    }
}

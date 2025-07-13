<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle;

use Simensen\MessageTracing\Trace\TraceGenerator;
use Simensen\MessageTracing\TracedContainerManager\TracedContainerManager;
use Simensen\MessageTracing\TraceIdentity\TraceIdentityGenerator;
use Simensen\MessageTracing\TraceStack\Adapter\DefaultTraceStack;
use Simensen\MessageTracing\TraceStack\TraceStack;
use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CausationTracedEnvelopeManager;
use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CorrelationTracedEnvelopeManager;
use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CausationTracingMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CorrelationTracingMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\Stamp\SymfonyUidMessageTracingStampGenerator;
use Simensen\SymfonyMessenger\MessageTracing\TraceIdentity\UlidTraceIdentityGenerator;
use Simensen\SymfonyMessenger\MessageTracing\TraceIdentity\UuidTraceIdentityGenerator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SimensenSymfonyMessageTracingBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('trace_generator')->defaultValue(SymfonyUidMessageTracingStampGenerator::class)->end()
                ->scalarNode('trace_stack')->defaultValue(DefaultTraceStack::class)->end()
                ->scalarNode('trace_identity_generator')->defaultValue('uuid')->end()
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

        // Auto-set trace_generator based on trace_identity_generator type
        $traceGenerator = match ($config['trace_identity_generator']) {
            'uuid' => 'Simensen\SymfonyMessenger\MessageTracing\Stamp\UuidMessageTracingStampGenerator',
            'ulid' => 'Simensen\SymfonyMessenger\MessageTracing\Stamp\UlidMessageTracingStampGenerator',
            default => $config['trace_generator'], // Use configured trace_generator for other types
        };

        $makeService('trace_generator', TraceGenerator::class, $traceGenerator);

        // Resolve trace identity generator based on configuration
        $traceIdentityGenerator = match ($config['trace_identity_generator']) {
            'uuid' => UuidTraceIdentityGenerator::class,
            'ulid' => UlidTraceIdentityGenerator::class,
            default => $config['trace_identity_generator'], // Allow custom FQCN
        };

        $makeService(
            'identity.generator',
            TraceIdentityGenerator::class,
            $traceIdentityGenerator
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
        $causationMiddlewareDefinition = (new Definition(CausationTracingMiddleware::class))
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addArgument(new Reference('simensen_message_tracing.messenger.middleware.causation'))
            ->addTag('messenger.middleware', ['priority' => 100]);

        $builder->setDefinition('simensen_message_tracing.middleware.causation', $causationMiddlewareDefinition);

        $correlationMiddlewareDefinition = (new Definition(CorrelationTracingMiddleware::class))
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addArgument(new Reference('simensen_message_tracing.messenger.middleware.correlation'))
            ->addTag('messenger.middleware', ['priority' => 100]);

        $builder->setDefinition('simensen_message_tracing.middleware.correlation', $correlationMiddlewareDefinition);
    }
}

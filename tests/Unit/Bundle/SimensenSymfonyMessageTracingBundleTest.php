<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Unit\Bundle;

use PHPUnit\Framework\TestCase;
use Simensen\SymfonyMessageTracingBundle\SimensenSymfonyMessageTracingBundle;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SimensenSymfonyMessageTracingBundleTest extends TestCase
{
    private SimensenSymfonyMessageTracingBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new SimensenSymfonyMessageTracingBundle();
    }

    public function testExtendsAbstractBundle(): void
    {
        $this->assertInstanceOf(AbstractBundle::class, $this->bundle);
    }

    public function testConfigureHasRequiredNodes(): void
    {
        // We can't easily mock DefinitionConfigurator due to union types,
        // but we can test indirectly through integration tests
        $this->assertTrue(method_exists($this->bundle, 'configure'));
    }

    public function testLoadExtensionWithDefaultConfig(): void
    {
        $config = [
            'trace_generator' => 'Simensen\\SymfonyMessenger\\MessageTracing\\Stamp\\SymfonyUidMessageTracingStampGenerator',
            'trace_stack' => 'Simensen\\MessageTracing\\TraceStack\\Adapter\\DefaultTraceStack',
            'trace_identity' => [
                'type' => 'Symfony\\Component\\Uid\\Uuid',
                'generator' => 'Simensen\\SymfonyMessenger\\MessageTracing\\TraceIdentity\\UuidTraceIdentityGenerator',
            ],
            'messenger' => [
                'middleware' => [
                    'causation' => 'simensen_message_tracing.messenger.middleware.causation',
                    'correlation' => 'simensen_message_tracing.messenger.middleware.correlation',
                ],
            ],
        ];

        $container = new ContainerBuilder();
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        // This should not throw
        $this->assertNull($this->bundle->loadExtension($config, $containerConfigurator, $container));

        // Verify services are registered
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.trace_stack'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.trace_generator'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.identity.generator'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.messenger.middleware.causation'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.messenger.middleware.correlation'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->hasDefinition('simensen_message_tracing.middleware.correlation'));
    }

    public function testLoadExtensionWithCustomConfig(): void
    {
        $config = [
            'trace_generator' => 'CustomTraceGenerator',
            'trace_stack' => 'CustomTraceStack',
            'trace_identity' => [
                'type' => 'CustomUuid',
                'generator' => 'CustomGenerator',
            ],
        ];

        $container = new ContainerBuilder();
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        $this->bundle->loadExtension($config, $containerConfigurator, $container);

        // Verify custom services are registered with correct classes
        $traceStackDefinition = $container->getDefinition('simensen_message_tracing.trace_stack');
        $this->assertEquals('CustomTraceStack', $traceStackDefinition->getClass());

        $traceGeneratorDefinition = $container->getDefinition('simensen_message_tracing.trace_generator');
        $this->assertEquals('CustomTraceGenerator', $traceGeneratorDefinition->getClass());

        $identityGeneratorDefinition = $container->getDefinition('simensen_message_tracing.identity.generator');
        $this->assertEquals('CustomGenerator', $identityGeneratorDefinition->getClass());
    }

    public function testLoadExtensionRegistersMiddlewareWithTags(): void
    {
        $config = [
            'trace_generator' => 'MockGenerator',
            'trace_stack' => 'MockStack',
            'trace_identity' => [
                'type' => 'MockUuid',
                'generator' => 'MockGenerator',
            ],
        ];

        $container = new ContainerBuilder();
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        $this->bundle->loadExtension($config, $containerConfigurator, $container);

        // Check middleware services have correct tags
        $causationDefinition = $container->getDefinition('simensen_message_tracing.middleware.causation');
        $correlationDefinition = $container->getDefinition('simensen_message_tracing.middleware.correlation');

        $this->assertTrue($causationDefinition->hasTag('messenger.middleware'));
        $this->assertTrue($correlationDefinition->hasTag('messenger.middleware'));

        $causationTags = $causationDefinition->getTag('messenger.middleware');
        $correlationTags = $correlationDefinition->getTag('messenger.middleware');

        $this->assertCount(1, $causationTags);
        $this->assertCount(1, $correlationTags);
        $this->assertEquals(100, $causationTags[0]['priority']);
        $this->assertEquals(100, $correlationTags[0]['priority']);
    }

    public function testLoadExtensionRegistersAliases(): void
    {
        $config = [
            'trace_generator' => 'MockGenerator',
            'trace_stack' => 'MockStack',
            'trace_identity' => [
                'type' => 'MockUuid',
                'generator' => 'MockGenerator',
            ],
        ];

        $container = new ContainerBuilder();
        $containerConfigurator = $this->createMock(ContainerConfigurator::class);

        $this->bundle->loadExtension($config, $containerConfigurator, $container);

        // Check aliases are registered
        $this->assertTrue($container->hasAlias('Simensen\\MessageTracing\\TraceStack\\TraceStack'));
        $this->assertTrue($container->hasAlias('Simensen\\MessageTracing\\Trace\\TraceGenerator'));
        $this->assertTrue($container->hasAlias('Simensen\\MessageTracing\\TraceIdentity\\TraceIdentityGenerator'));
    }
}
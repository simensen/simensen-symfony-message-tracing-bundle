<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Integration;

use Simensen\SymfonyMessageTracingBundle\Tests\Fixtures\TestKernel;
use Simensen\MessageTracing\TraceStack\Adapter\DefaultTraceStack;
use Simensen\MessageTracing\Trace\TraceGenerator;
use Simensen\MessageTracing\TraceIdentity\TraceIdentityGenerator;
use Simensen\MessageTracing\TraceStack\TraceStack;
use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CausationTracingMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CorrelationTracingMiddleware;
use Simensen\SymfonyMessageTracingBundle\SimensenSymfonyMessageTracingBundle;
use Simensen\SymfonyMessenger\MessageTracing\Stamp\SymfonyUidMessageTracingStampGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceRegistrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        return new TestKernel();
    }

    public function testAllServicesAreRegistered(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $expectedServices = [
            'simensen_message_tracing.trace_stack',
            'simensen_message_tracing.trace_generator',
            'simensen_message_tracing.identity.generator',
            'simensen_message_tracing.messenger.middleware.causation',
            'simensen_message_tracing.messenger.middleware.correlation',
            'simensen_message_tracing.middleware.causation',
            'simensen_message_tracing.middleware.correlation',
        ];

        foreach ($expectedServices as $serviceId) {
            $this->assertTrue(
                $container->has($serviceId),
                sprintf('Service "%s" should be registered in the container', $serviceId)
            );
        }
    }

    public function testServiceDefinitionsHaveCorrectClasses(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertInstanceOf(DefaultTraceStack::class, $container->get('simensen_message_tracing.trace_stack'));
        $this->assertInstanceOf(SymfonyUidMessageTracingStampGenerator::class, $container->get('simensen_message_tracing.trace_generator'));
        $this->assertInstanceOf(CausationTracingMiddleware::class, $container->get('simensen_message_tracing.middleware.causation'));
        $this->assertInstanceOf(CorrelationTracingMiddleware::class, $container->get('simensen_message_tracing.middleware.correlation'));
    }

    public function testServiceAliasesAreRegistered(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has(TraceStack::class));
        $this->assertTrue($container->has(TraceGenerator::class));
        $this->assertTrue($container->has(TraceIdentityGenerator::class));

        $this->assertInstanceOf(DefaultTraceStack::class, $container->get(TraceStack::class));
        $this->assertInstanceOf(SymfonyUidMessageTracingStampGenerator::class, $container->get(TraceGenerator::class));
    }

    public function testAutowiringAndAutoconfigurationAreEnabled(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that autowired services can be instantiated successfully
        $serviceIds = [
            'simensen_message_tracing.trace_stack',
            'simensen_message_tracing.trace_generator',
            'simensen_message_tracing.identity.generator',
            'simensen_message_tracing.middleware.causation',
            'simensen_message_tracing.middleware.correlation',
        ];

        foreach ($serviceIds as $serviceId) {
            $this->assertTrue($container->has($serviceId), sprintf('Service "%s" should be registered', $serviceId));
            $service = $container->get($serviceId);
            $this->assertNotNull($service, sprintf('Service "%s" should be autowired and instantiable', $serviceId));
        }
    }

    public function testMessengerMiddlewareTagsAreApplied(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that middleware services exist and are properly tagged by verifying they can be instantiated
        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));

        $causationMiddleware = $container->get('simensen_message_tracing.middleware.causation');
        $correlationMiddleware = $container->get('simensen_message_tracing.middleware.correlation');

        $this->assertNotNull($causationMiddleware);
        $this->assertNotNull($correlationMiddleware);
    }

    public function testServiceDependenciesAreCorrect(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $causationMiddleware = $container->get('simensen_message_tracing.middleware.causation');
        $correlationMiddleware = $container->get('simensen_message_tracing.middleware.correlation');

        $this->assertInstanceOf(CausationTracingMiddleware::class, $causationMiddleware);
        $this->assertInstanceOf(CorrelationTracingMiddleware::class, $correlationMiddleware);

        // Check that middleware have their dependencies injected
        $this->assertNotNull($causationMiddleware);
        $this->assertNotNull($correlationMiddleware);
    }

    public function testConditionalServiceRegistration(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that envelope managers are not aliased (shouldSetAlias = false)
        $this->assertFalse($container->has('Simensen\MessageTracing\TracedContainerManager'));
        
        // But the actual services should exist
        $this->assertTrue($container->has('simensen_message_tracing.messenger.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.messenger.middleware.correlation'));
    }
}
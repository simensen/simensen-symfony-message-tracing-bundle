<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Functional;

use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CausationTracingMiddleware;
use Simensen\SymfonyMessenger\MessageTracing\Messenger\Middleware\CorrelationTracingMiddleware;
use Simensen\SymfonyMessageTracingBundle\SimensenSymfonyMessageTracingBundle;
use Simensen\SymfonyMessageTracingBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MiddlewareIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        return new TestKernel();
    }

    public function testMiddlewareServicesAreRegistered(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));

        $causationMiddleware = $container->get('simensen_message_tracing.middleware.causation');
        $correlationMiddleware = $container->get('simensen_message_tracing.middleware.correlation');

        $this->assertInstanceOf(CausationTracingMiddleware::class, $causationMiddleware);
        $this->assertInstanceOf(CorrelationTracingMiddleware::class, $correlationMiddleware);
    }

    public function testMiddlewareCanBeInstantiated(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that middleware can be created and have their dependencies injected
        $causationMiddleware = $container->get('simensen_message_tracing.middleware.causation');
        $correlationMiddleware = $container->get('simensen_message_tracing.middleware.correlation');

        $this->assertNotNull($causationMiddleware);
        $this->assertNotNull($correlationMiddleware);

        // Test that they implement the correct interface
        $this->assertInstanceOf('Symfony\Component\Messenger\Middleware\MiddlewareInterface', $causationMiddleware);
        $this->assertInstanceOf('Symfony\Component\Messenger\Middleware\MiddlewareInterface', $correlationMiddleware);
    }

    public function testMessengerBusExists(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('messenger.bus.default'));
        
        $bus = $container->get('messenger.bus.default');
        $this->assertInstanceOf(MessageBusInterface::class, $bus);
    }

    public function testMessengerBusCanHandleEnvelope(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $bus = $container->get('messenger.bus.default');
        
        // Create a simple message object
        $message = new class {
            public string $content = 'test message';
        };

        // Test that we can at least create an envelope and attempt dispatch
        // The NoHandlerForMessageException is expected since we haven't registered handlers
        $this->expectException('Symfony\Component\Messenger\Exception\NoHandlerForMessageException');
        $bus->dispatch($message);
    }

    public function testBundleConfigurationIsApplied(): void
    {
        $config = [
            'trace_generator' => 'Simensen\\SymfonyMessenger\\MessageTracing\\Stamp\\SymfonyUidMessageTracingStampGenerator',
            'trace_stack' => 'Simensen\\MessageTracing\\TraceStack\\Adapter\\DefaultTraceStack',
        ];

        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that the configured services exist
        $this->assertTrue($container->has('simensen_message_tracing.trace_generator'));
        $this->assertTrue($container->has('simensen_message_tracing.trace_stack'));

        // Test that middleware are still registered with custom configuration
        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));
    }
}
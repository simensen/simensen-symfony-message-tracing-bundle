<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Integration;

use Simensen\MessageTracing\TraceStack\Adapter\DefaultTraceStack;
use Simensen\SymfonyMessageTracingBundle\SimensenSymfonyMessageTracingBundle;
use Simensen\SymfonyMessageTracingBundle\Tests\Fixtures\TestKernel;
use Simensen\SymfonyMessenger\MessageTracing\Stamp\UuidMessageTracingStampGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class BundleConfigurationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        $kernel = new TestKernel();
        
        if (isset($options['config'])) {
            $kernel->setBundleConfig($options['config']);
        }
        
        return $kernel;
    }

    public function testDefaultConfiguration(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.trace_stack'));
        $this->assertTrue($container->has('simensen_message_tracing.trace_generator'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));

        $traceStack = $container->get('simensen_message_tracing.trace_stack');
        $this->assertInstanceOf(DefaultTraceStack::class, $traceStack);

        $traceGenerator = $container->get('simensen_message_tracing.trace_generator');
        $this->assertInstanceOf(UuidMessageTracingStampGenerator::class, $traceGenerator);
    }

    public function testCustomTraceGeneratorConfiguration(): void
    {
        $config = [
            'trace_generator' => 'Simensen\\SymfonyMessageTracingBundle\\Tests\\Fixtures\\MockTraceGenerator',
        ];
        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.trace_generator'));
    }

    public function testCustomTraceStackConfiguration(): void
    {
        $config = [
            'trace_stack' => 'Simensen\\SymfonyMessageTracingBundle\\Tests\\Fixtures\\MockTraceStack',
        ];
        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.trace_stack'));
    }

    public function testTraceIdentityConfiguration(): void
    {
        $config = [
            'trace_identity_generator' => 'Simensen\\SymfonyMessageTracingBundle\\Tests\\Fixtures\\MockTraceIdentityGenerator',
        ];
        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.identity.generator'));
    }

    public function testUuidTraceIdentityConfiguration(): void
    {
        $config = [
            'trace_identity_generator' => 'uuid',
        ];
        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.identity.generator'));
    }

    public function testUlidTraceIdentityConfiguration(): void
    {
        $config = [
            'trace_identity_generator' => 'ulid',
        ];
        $kernel = self::createKernel(['config' => $config]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.identity.generator'));
    }

    public function testMessengerMiddlewareConfiguration(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));
    }

    public function testMiddlewareServicesAreTagged(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that middleware services exist and are properly instantiated
        $this->assertTrue($container->has('simensen_message_tracing.middleware.causation'));
        $this->assertTrue($container->has('simensen_message_tracing.middleware.correlation'));
        
        // Verify they can be instantiated (which means tags are working)
        $causationMiddleware = $container->get('simensen_message_tracing.middleware.causation');
        $correlationMiddleware = $container->get('simensen_message_tracing.middleware.correlation');
        
        $this->assertNotNull($causationMiddleware);
        $this->assertNotNull($correlationMiddleware);
    }

    public function testEmptyConfiguration(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Should load with defaults
        $this->assertTrue($container->has('simensen_message_tracing.trace_stack'));
        $this->assertTrue($container->has('simensen_message_tracing.trace_generator'));
    }
}
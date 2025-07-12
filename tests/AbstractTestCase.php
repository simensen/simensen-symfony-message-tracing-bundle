<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Helper method to create a mock trace stack for testing.
     */
    protected function createMockTraceStack(): object
    {
        return $this->createMock('Simensen\MessageTracing\TraceStackInterface');
    }

    /**
     * Helper method to create a mock trace generator for testing.
     */
    protected function createMockTraceGenerator(): object
    {
        return $this->createMock('Simensen\SymfonyMessengerMessageTracing\MessageTracingStampGeneratorInterface');
    }

    /**
     * Helper method to create a mock trace identity generator for testing.
     */
    protected function createMockTraceIdentityGenerator(): object
    {
        return $this->createMock('Symfony\Component\Uid\Factory\UuidFactory');
    }

    /**
     * Helper method to assert that a service definition exists in the container.
     */
    protected function assertServiceExists(string $serviceId, array $services): void
    {
        $this->assertArrayHasKey($serviceId, $services, sprintf('Service "%s" should be registered in the container', $serviceId));
    }

    /**
     * Helper method to assert that a service has the expected class.
     */
    protected function assertServiceClass(string $expectedClass, object $serviceDefinition): void
    {
        $this->assertEquals($expectedClass, $serviceDefinition->getClass(), 'Service should have the expected class');
    }

    /**
     * Helper method to assert that a service has the expected tag.
     */
    protected function assertServiceHasTag(string $tagName, object $serviceDefinition): void
    {
        $tags = $serviceDefinition->getTags();
        $this->assertArrayHasKey($tagName, $tags, sprintf('Service should have tag "%s"', $tagName));
    }
}
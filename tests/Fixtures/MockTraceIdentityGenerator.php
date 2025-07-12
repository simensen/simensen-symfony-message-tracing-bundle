<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Fixtures;

use Simensen\MessageTracing\TraceIdentity\TraceIdentityGenerator;

class MockTraceIdentityGenerator implements TraceIdentityGenerator
{
    public function generate(): string
    {
        return 'mock-trace-identity-' . uniqid();
    }
}
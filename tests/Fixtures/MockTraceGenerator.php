<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Fixtures;

use Simensen\MessageTracing\Trace\TraceGenerator;
use Simensen\MessageTracing\Trace\Trace;
use Simensen\MessageTracing\TraceIdentity\TraceIdentityGenerator;

class MockTraceGenerator implements TraceGenerator
{
    public function generateTrace(TraceIdentityGenerator $traceIdentityGenerator): Trace
    {
        return new MockTrace();
    }
}
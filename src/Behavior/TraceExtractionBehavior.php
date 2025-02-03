<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Behavior;

use Simensen\SymfonyMessageTracingBundle\Messenger\Stamp\MessageTracingStamp;
use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

trait TraceExtractionBehavior
{
    protected function extractTraceFromEnvelope(Envelope $envelope): ?Trace
    {
        return $envelope->last(MessageTracingStamp::class)?->trace;
    }
}

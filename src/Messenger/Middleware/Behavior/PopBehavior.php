<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior;

use Simensen\SymfonyMessageTracingBundle\Behavior\TraceExtractionBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Stamp\MessageTracingStamp;
use Symfony\Component\Messenger\Envelope;

trait PopBehavior
{
    use TraceExtractionBehavior;

    public function pop(Envelope $envelope): Envelope
    {
        if (!$trace = $this->extractTraceFromEnvelope($envelope)) {
            return $envelope;
        }

        if ($this->messageTracingStack->isNotTail($trace)) {
            return $envelope;
        }

        return $envelope->withoutStampsOfType(MessageTracingStamp::class)
            ->with(new MessageTracingStamp(
                $this->messageTracingStack->pop($trace)
            ));
    }
}

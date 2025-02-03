<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior;

use Simensen\SymfonyMessageTracingBundle\Audit\Behavior\AuditLoggingToMessageTracingStackBehavior;
use Simensen\SymfonyMessageTracingBundle\Behavior\TraceExtractionBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Stamp\MessageTracingStamp;
use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

trait PushCorrelationBehavior
{
    use TraceExtractionBehavior;
    use AuditLoggingToMessageTracingStackBehavior;

    public function push(Envelope $envelope): Envelope
    {
        $trace = $this->extractTraceFromEnvelope($envelope);

        if (!$trace && $this->messageTracingStack->isNotEmpty()) {
            $trace = $this->messageTracingStack->next();

            $this->logForAudit($trace, $envelope);
        } else {
            $trace ??= Trace::start();

            // @TODO Only push if stack is empty?
            // @TODO Only push if $trace isn't already at the top of the stack?
            $this->messageTracingStack->push($trace);

            $this->logForAudit($trace, $envelope);
        }

        return $envelope->withoutStampsOfType(MessageTracingStamp::class)
            ->with(new MessageTracingStamp($trace));
    }
}

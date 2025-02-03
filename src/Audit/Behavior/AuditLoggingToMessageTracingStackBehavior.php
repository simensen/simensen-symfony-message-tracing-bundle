<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Audit\Behavior;

use Simensen\SymfonyMessageTracingBundle\Audit\AuditLogging;
use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

trait AuditLoggingToMessageTracingStackBehavior
{
    protected function logForAudit(Trace $trace, Envelope $envelope): void
    {
        if (!$this->messageTracingStack instanceof AuditLogging) {
            return;
        }

        $this->messageTracingStack->logForAudit($trace, $envelope);
    }
}

<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Audit\Behavior;

use Simensen\SymfonyMessageTracingBundle\Audit\AuditLog;
use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

trait AuditLoggingMessageTracingStackBehavior
{
    public function logForAudit(Trace $trace, Envelope $envelope): void
    {
        ($this->auditLog ??= new AuditLog())->logForAudit($trace, $envelope);
    }
}

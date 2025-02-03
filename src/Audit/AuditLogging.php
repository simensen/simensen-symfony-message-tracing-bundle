<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Audit;

use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

interface AuditLogging
{
    public function logForAudit(Trace $trace, Envelope $envelope): void;
}

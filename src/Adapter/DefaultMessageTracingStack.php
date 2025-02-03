<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Adapter;

use Simensen\SymfonyMessageTracingBundle\Audit\AuditLog;
use Simensen\SymfonyMessageTracingBundle\Audit\AuditLogging;
use Simensen\SymfonyMessageTracingBundle\Audit\Behavior\AuditLoggingMessageTracingStackBehavior;
use Simensen\SymfonyMessageTracingBundle\Behavior\DefaultMessageTracingStackBehavior;
use Simensen\SymfonyMessageTracingBundle\MessageTracingStack;

class DefaultMessageTracingStack implements MessageTracingStack, AuditLogging
{
    use DefaultMessageTracingStackBehavior;
    use AuditLoggingMessageTracingStackBehavior;

    public function __construct(protected ?AuditLog $auditLog = null)
    {
    }
}

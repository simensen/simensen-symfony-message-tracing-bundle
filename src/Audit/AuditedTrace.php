<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Audit;

use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

final readonly class AuditedTrace
{
    public function __construct(
        public Trace $trace,
        public Envelope $envelope,
    ) {
    }
}

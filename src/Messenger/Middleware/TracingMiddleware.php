<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;

interface TracingMiddleware
{
    public function push(Envelope $envelope): Envelope;

    public function pop(Envelope $envelope): Envelope;
}

<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Middleware;

use Simensen\SymfonyMessenger\MessageTracing\EnvelopeManager\CausationTracedEnvelopeManager;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CausationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly CausationTracedEnvelopeManager $envelopeManager,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Push trace onto stack and get updated envelope with causation tracing
        $envelope = $this->envelopeManager->push($envelope);

        // Continue processing through the middleware stack
        $envelope = $stack->next()->handle($envelope, $stack);

        // Pop trace from stack and get final envelope
        return $this->envelopeManager->pop($envelope);
    }
}

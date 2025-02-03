<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

trait HandleBehavior
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $this->pop(
            $stack->next()->handle(
                $this->push($envelope),
                $stack
            )
        );
    }
}

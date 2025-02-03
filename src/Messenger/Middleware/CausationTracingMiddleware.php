<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware;

use Simensen\SymfonyMessageTracingBundle\MessageTracingStack;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\HandleBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\PopBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\PushCausationBehavior;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

class CausationTracingMiddleware implements MiddlewareInterface, TracingMiddleware
{
    use PushCausationBehavior;
    use PopBehavior;
    use HandleBehavior;

    public function __construct(private readonly MessageTracingStack $messageTracingStack)
    {
    }
}

<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Middleware;

use Simensen\SymfonyMessageTracingBundle\MessageTracingStack;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\HandleBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\PopBehavior;
use Simensen\SymfonyMessageTracingBundle\Messenger\Middleware\Behavior\PushCorrelationBehavior;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

class CorrelationTracingMiddleware implements MiddlewareInterface, TracingMiddleware
{
    use PushCorrelationBehavior;
    use PopBehavior;
    use HandleBehavior;

    public function __construct(private readonly MessageTracingStack $messageTracingStack)
    {
    }
}

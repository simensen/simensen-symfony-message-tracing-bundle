<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Behavior;

use Simensen\SymfonyMessageTracingBundle\MessageTracingStack;
use Simensen\SymfonyMessageTracingBundle\Trace;

/**
 * Default behavior for MessageTracingStack.
 *
 * @see MessageTracingStack
 */
trait DefaultMessageTracingStackBehavior
{
    /**
     * @var Trace[]
     */
    private array $stack = [];

    public function push(Trace $trace): void
    {
        $this->stack[] = $trace;
    }

    public function pop(Trace $trace): Trace
    {
        if ($this->stack && $this->isTail($trace)) {
            array_pop($this->stack);
        }

        // @TODO We should maybe consider raising errors (string?) if
        //       pop is called but the stack is empty or the stack
        //       has a different Trace at the tail.

        return $trace;
    }

    public function next(): Trace
    {
        // @TODO Do we want to throw an exception if stack the
        //       stack is empty?
        return $this->stack ? end($this->stack)->next() : Trace::start();
    }

    public function isEmpty(): bool
    {
        return count($this->stack) === 0;
    }

    public function isNotEmpty(): bool
    {
        return count($this->stack) > 0;
    }

    public function isTail(Trace $trace): bool
    {
        return $this->stack ? end($this->stack)->equals($trace) : false;
    }

    public function isNotTail(Trace $trace): bool
    {
        return !$this->isTail($trace);
    }
}

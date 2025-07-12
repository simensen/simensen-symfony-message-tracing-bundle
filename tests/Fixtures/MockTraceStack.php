<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Fixtures;

use Simensen\MessageTracing\TraceStack\TraceStack;
use Simensen\MessageTracing\Trace\Trace;

class MockTraceStack implements TraceStack
{
    private array $stack = [];

    public function push(Trace $trace): void
    {
        $this->stack[] = $trace;
    }

    public function pop(Trace $trace): Trace
    {
        return array_pop($this->stack) ?? $trace;
    }

    public function start(): Trace
    {
        return new MockTrace();
    }

    public function next(): Trace
    {
        return new MockTrace();
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function isTail(Trace $trace): bool
    {
        return empty($this->stack) || end($this->stack) === $trace;
    }

    public function isNotTail(Trace $trace): bool
    {
        return !$this->isTail($trace);
    }
}

class MockTrace implements Trace
{
    public function causationId(): string
    {
        return 'mock-causation-' . uniqid();
    }

    public function correlationId(): string
    {
        return 'mock-correlation-' . uniqid();
    }

    public function identity(): string
    {
        return 'mock-identity-' . uniqid();
    }
}
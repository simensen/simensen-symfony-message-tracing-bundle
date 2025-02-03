<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle;

interface MessageTracingStack
{
    public function push(Trace $trace): void;

    public function pop(Trace $trace): Trace;

    public function next(): Trace;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    public function isTail(Trace $trace): bool;

    public function isNotTail(Trace $trace): bool;
}

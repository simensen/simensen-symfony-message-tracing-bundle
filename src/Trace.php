<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle;

use Symfony\Component\Uid\Uuid;

final readonly class Trace
{
    public function __construct(
        public Uuid $id,
        public Uuid $causationId,
        public Uuid $correlationId,
    ) {
    }

    public static function start(): self
    {
        $id = Uuid::v7();

        return new self(
            $id,
            clone $id,
            clone $id,
        );
    }

    public function next(): self
    {
        return new self(
            Uuid::v7(),
            $this->id,
            $this->correlationId
        );
    }

    public function equals(mixed $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return
            $this->id === $other->id
            && $this->causationId === $other->causationId
            && $this->correlationId === $other->correlationId;
    }

    public function isRoot(): bool
    {
        return $this->id->compare($this->causationId) === 0 && $this->id->compare($this->correlationId) === 0;
    }

    public function correlatesWith(Trace $other): bool
    {
        return $this->correlationId->compare($other->correlationId) === 0;
    }

    public function causedBy(Trace $other): bool
    {
        return $this->causationId->compare($other->id) === 0 && !($this->causationId->compare($this->id) === 0);
    }
}

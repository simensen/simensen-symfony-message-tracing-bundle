<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Messenger\Stamp;

use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Stamp\StampInterface;

class MessageTracingStamp implements StampInterface
{
    public function __construct(public readonly Trace $trace)
    {
    }
}

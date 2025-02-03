<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Audit;

use Simensen\SymfonyMessageTracingBundle\Trace;
use Symfony\Component\Messenger\Envelope;

class AuditLog implements AuditLogging
{
    /**
     * @var AuditedTrace[]
     */
    private array $auditedTraces = [];

    public function logForAudit(Trace $trace, Envelope $envelope): void
    {
        $this->auditedTraces[] = new AuditedTrace($trace, $envelope);
    }

    private function render(AuditedTrace $root, int $depth = 1): array
    {
        if ($depth > 10) {
            dd('WAT');
        }

        $row = [
            'auditedTrace' => $root,
            'causes' => [],
        ];

        // $prefix = str_repeat('   | ', $depth);

        // printf(
        //     "%s %s   %s   %s\n",
        //     $prefix,
        //     $root->trace->correlationId,
        //     $root->trace->causationId,
        //     $root->trace->id
        // );

        // printf(
        //     "%s - %s: %s\n",
        //     $prefix,
        //     get_class($root->envelope->getMessage()),
        //     json_encode($root->envelope->getMessage())
        // );

        $caused = array_filter($this->auditedTraces, fn ($auditedTrace) => $auditedTrace->trace->causedBy($root->trace));

        if ($caused) {
            // printf("%s - Causes:\n", $prefix);

            foreach ($caused as $child) {
                $row['causes'][] = $this->render($child, $depth + 1);
            }
        }

        return $row;
    }

    public function toRawTree(): array
    {
        $tree = [];

        $roots = array_filter($this->auditedTraces, fn ($auditedTrace) => $auditedTrace->trace->isRoot());

        foreach ($roots as $root) {
            $tree[] = $this->render($root);
        }

        return $tree;
    }

    public static function toFriendlyText(array $rawNode): array
    {
        $row = [
            'id' => (string) $rawNode['auditedTrace']->trace->id,
            'type' => get_class($rawNode['auditedTrace']->envelope->getMessage()),
            'content' => json_encode($rawNode['auditedTrace']->envelope->getMessage()),
        ];

        if ($rawNode['causes']) {
            $row['causes'] = array_map(fn ($rawNode) => self::toFriendlyText($rawNode), $rawNode['causes']);
        }

        return $row;
    }

    public function toFriendlyTextTree(): array
    {
        return array_map(fn ($rawNode) => self::toFriendlyText($rawNode), $this->toRawTree());
    }
}

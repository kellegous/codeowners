<?php

namespace Kellegous\CodeOwners\AutomataMatcher;

use Kellegous\CodeOwners\AutomataMatcher;

final class DotRenderer
{
    /**
     * @param AutomataMatcher $matcher
     * @return string
     */
    public static function render(AutomataMatcher $matcher): string
    {
        ['nodes' => $nodes, 'edges' => $edges] = $matcher->getDebugInfo();

        $start = array_key_first($nodes);

        $lines = [
            'node [shape = circle; label = "";];',
            "{$start} [peripheries = 2;];",
        ];
        foreach ($nodes as $id => $priority) {
            $label = $priority === -1 ? '' : $priority;
            $lines[] = "{$id} [label=\"{$label}\";];";
        }

        foreach ($edges as ['from' => $from, 'to' => $to, 'label' => $label]) {
            $lines[] = "{$from} -> {$to} [label=\"{$label}\"];";
        }

        return implode(
            PHP_EOL,
            [
                'digraph G {',
                ...$lines,
                '}',
            ]
        );
    }
}
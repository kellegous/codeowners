<?php

namespace Kellegous\CodeOwners\AutomataMatcher;

use Kellegous\CodeOwners\AutomataMatcher;

/**
 * Intended for debugging, this class will emit a graphviz dot file to visualize
 * the state machine used to match patterns in an AutomataMatcher.
 */
final class DotRenderer
{
    /**
     * Generate a graphviz dot file from the given AutomataMatcher. This can be
     * rendered using the dot command.
     *
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
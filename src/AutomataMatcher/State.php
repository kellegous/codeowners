<?php

namespace Kellegous\CodeOwners\AutomataMatcher;

use InvalidArgumentException;

final class State
{
    /**
     * @var int
     */
    private int $priority = -1;

    /**
     * @var array<string, State>
     */
    private array $edges = [];

    /**
     * @var bool
     */
    private bool $isRecursive;

    /**
     * @param bool $isRecursive
     */
    public function __construct(bool $isRecursive = false)
    {
        $this->isRecursive = $isRecursive;
    }

    /**
     * @param Token[] $tokens
     * @param int $priority
     * @return void
     */
    public function addTokens(
        array $tokens,
        int $priority
    ): void {
        if (empty($tokens)) {
            throw new InvalidArgumentException('Tokens cannot be empty');
        }

        $token = array_shift($tokens);
        $pattern = $token->getPattern();

        if ($pattern === '**' || $pattern == '***') {
            $state = $this->edges['**'] ?? new State(true);
            $this->edges['**'] = $state;
        } else {
            $regex = $token->getRegex();
            $state = $this->edges[$regex] ?? new State();
            $this->edges[$regex] = $state;
        }

        if (empty($tokens)) {
            $state->priority = max($priority, $state->priority);
            if ($pattern === '***') {
                $this->priority = max($priority, $this->priority);
            }
        } else {
            $state->addTokens($tokens, $priority);
        }
    }

    /**
     * @param string[] $path
     * @return int
     */
    public function match(array $path): int
    {
        if (empty($path)) {
            return $this->priority;
        }

        $priority = -1;

        $all = $this->edges['**'] ?? null;
        if ($all !== null) {
            $priority = max($all->match($path), $priority);
        }

        $local = array_shift($path);
        foreach ($this->edges as $regex => $state) {
            if (preg_match($regex, $local)) {
                $priority = max($state->match($path), $priority);
            }
        }

        return $this->isRecursive
            ? max($this->match($path), $priority)
            : $priority;
    }

    /**
     * @param array<string, int> $nodes
     * @param array{from:int, to: int, label:string}[] $edges
     * @return void
     *
     * @internal
     */
    public function getDebugInfo(
        array &$nodes,
        array &$edges
    ): void {
        $nodes[spl_object_id($this)] = $this->priority;
        foreach ($this->edges as $regex => $state) {
            $edges[] = [
                'from' => spl_object_id($this),
                'to' => spl_object_id($state),
                'label' => $regex,
            ];
            $state->getDebugInfo($nodes, $edges);
        }
        if ($this->isRecursive) {
            $edges[] = [
                'from' => spl_object_id($this),
                'to' => spl_object_id($this),
                'label' => '**',
            ];
        }
    }
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners\AutomataMatcher;

use InvalidArgumentException;

/**
 * Represents a state in the NFA.
 * @Internal
 */
final class State
{
    /**
     * The index of the rule. We call this priority because we choose the highest
     * priority state (the last rule) to choose the best match.
     *
     * @var int
     */
    private int $priority = -1;

    /**
     * The key is a regex pattern for the segment and the value is the next state.
     *
     * @var array<string, State>
     */
    private array $edges = [];

    /**
     * A recursive state is a special ** state which is one that has a self-referencing
     * epsilon state.
     *
     * @var bool
     */
    private bool $isRecursive;

    /**
     * Create a new state. If the state is a terminating ** state, then it will
     * be considered recursive.
     *
     * @param bool $isRecursive
     */
    public function __construct(bool $isRecursive = false)
    {
        $this->isRecursive = $isRecursive;
    }

    /**
     * Add the states associated with the tokens of a parsed pattern.
     *
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
     * Find the highest priority match for the given path. Note that a return value
     * of -1 indicates that no match was found.
     *
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
     * Used to return an internal representation of the automata for debugging purposes.
     *
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

    /**
     * @return array{priority: int, edges: array<string, State>, isRecursive: bool}
     */
    public function __serialize(): array
    {
        return [
            'priority' => $this->priority,
            'edges' => $this->edges,
            'isRecursive' => $this->isRecursive,
        ];
    }

    /**
     * @param array{priority: int, edges: array<string, State>, isRecursive: bool} $data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->priority = $data['priority'];
        $this->edges = $data['edges'];
        $this->isRecursive = $data['isRecursive'];
    }
}

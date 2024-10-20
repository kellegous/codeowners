<?php

namespace Kellegous\CodeOwners\AutomataMatcher;

use http\Exception\InvalidArgumentException;
use JsonSerializable;

final class State implements JsonSerializable
{
    private int $priority = -1;

    /**
     * @var array<string, State>
     */
    private array $edges = [];

    /**
     * @var bool
     */
    private bool $isRecursive = false;

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

        if ($token->getPattern() === '**') {
            $this->isRecursive = true;
            $state = $this;
        } else {
            $regex = $token->getRegex();
            $state = $this->edges[$regex] ?? new State();
            $this->edges[$regex] = $state;
        }

        if (empty($tokens)) {
            $state->priority = max($priority, $state->priority);
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
     * @return array{priority: int, edges: array<string, State>, "**": bool}
     */
    public function jsonSerialize(): array
    {
        return [
            'priority' => $this->priority,
            'edges' => $this->edges,
            '**' => $this->isRecursive,
        ];
    }
}
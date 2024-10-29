<?php

namespace Kellegous\CodeOwners;

use Kellegous\CodeOwners\AutomataMatcher\State;
use Kellegous\CodeOwners\AutomataMatcher\Token;

/**
 * A RuleMatcher that combines all the patterns into a single automata.
 */
final class AutomataMatcher implements RuleMatcher
{
    /**
     * This is the start state for the automata.
     *
     * @var State
     */
    private State $start;

    /**
     * The full collection of rules from the Owners instance. The NFA keeps the index of
     * rules instead of a reference to the rules themselves since the index provides a straight-forward
     * way to honor the last-match-wins rule (the largest of the matching indexes wins).
     *
     * @var Rule[]
     */
    private array $rules;

    /**
     * @param Rule[] $rules
     * @param State $start
     */
    private function __construct(
        array $rules,
        State $start
    ) {
        $this->rules = $rules;
        $this->start = $start;
    }

    /**
     * Builds a new AutomataMatcher from the given rules.
     *
     * @param iterable<Rule> $rules
     * @return self
     */
    public static function build(iterable $rules): self
    {
        $start = new State();
        $as_array = [];
        foreach ($rules as $index => $rule) {
            $start->addTokens(
                self::parsePattern($rule->getPattern()),
                $index
            );
            $as_array[] = $rule;
        }
        return new self($as_array, $start);
    }

    /**
     * @param Pattern $pattern
     * @return Token[]
     */
    private static function parsePattern(Pattern $pattern): array
    {
        $tokens = [];
        $pattern = $pattern->toString();

        if (!self::isAbsolute($pattern) && !str_starts_with($pattern, '**/')) {
            $pattern = '**/' . $pattern;
        }

        if (str_ends_with($pattern, '/')) {
            $pattern .= '**';
        } elseif (!str_ends_with($pattern, '/**') && !str_ends_with($pattern, '/*')) {
            $pattern .= '/***';
        }

        $segments = explode('/', trim($pattern, '/'));

        foreach ($segments as $i => $segment) {
            if ($segment === '*' || $segment === '**') {
                $tokens[] = new Token($segment, '#\A.*\Z#');
                continue;
            }

            $tokens[] = self::parseToken($segment);
        }
        return $tokens;
    }

    /**
     * Strangely, a pattern is absolute not only if it starts with a slash,
     * but also if it contains a wildcard.
     *
     * @param string $pattern
     *
     * @return bool
     */
    private static function isAbsolute(string $pattern): bool
    {
        $ix = strpos($pattern, '/');
        if ($ix === false) {
            return false;
        }

        if ($ix === 0) {
            return true;
        }

        for ($i = $ix, $n = strlen($pattern); $i < $n; $i++) {
            if ($pattern[$i] === '*' || $pattern[$i] === '?') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $segment
     * @return Token
     */
    private static function parseToken(
        string $segment
    ): Token {
        $buffer = '\A';
        $escape = false;
        for ($i = 0, $n = strlen($segment); $i < $n; $i++) {
            if ($escape) {
                $escape = false;
                $buffer .= preg_quote($segment[$i], '#');
                continue;
            }

            switch ($segment[$i]) {
                case '\\':
                    $escape = true;
                    break;
                case '*':
                    $buffer .= '.*';
                    break;
                case '?':
                    $buffer .= '.';
                    break;
                default:
                    $buffer .= preg_quote($segment[$i], '#');
                    break;
            }
        }
        $buffer .= '\Z';
        return new Token($segment, "#$buffer#");
    }

    /**
     * @inerhitDoc
     * @Override
     * @param string $path
     * @return Rule|null
     */
    public function match(string $path): ?Rule
    {
        if (str_starts_with($path, '/') || str_ends_with($path, '/')) {
            throw new \InvalidArgumentException(
                "path should be a relative path to a file, thus it cannot start or end with a /"
            );
        }

        $path = explode('/', $path);
        $index = $this->start->match($path);
        return $index >= 0
            ? $this->rules[$index]
            : null;
    }

    /**
     * Used to return an internal representation of the automata for debugging purposes.
     *
     * @return array{nodes: array<string, int>, edges: array{from: string, to: string, label: string}[]}
     *
     * @internal
     */
    public function getDebugInfo(): array
    {
        $patterns = [];
        foreach ($this->rules as $rule) {
            foreach (self::parsePattern($rule->getPattern()) as $token) {
                $patterns[$token->getRegex()] = $token->getPattern();
            }
        }

        $nodes = [];
        $edges = [];
        $this->start->getDebugInfo($nodes, $edges);
        $edges = array_map(
            function (array $edge) use ($patterns): array {
                ['from' => $from, 'to' => $to, 'label' => $label] = $edge;
                if ($label !== '**') {
                    $label = $patterns[$label] ?? '??';
                }
                return ['from' => $from, 'to' => $to, 'label' => $label];
            },
            $edges
        );
        return ['nodes' => $nodes, 'edges' => $edges];
    }
}
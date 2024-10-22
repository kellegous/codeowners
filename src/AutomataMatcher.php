<?php

namespace Kellegous\CodeOwners;

use Kellegous\CodeOwners\AutomataMatcher\State;
use Kellegous\CodeOwners\AutomataMatcher\Token;

final class AutomataMatcher implements RuleMatcher
{
    private State $start;

    /**
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
     * @param Rule[] $rules
     * @return self
     */
    public static function build(array $rules): self
    {
        $start = new State();
        foreach ($rules as $index => $rule) {
            $start->addTokens(
                self::parsePattern($rule->getPattern()),
                $index
            );
        }
        return new self($rules, $start);
    }

    /**
     * @param Pattern $pattern
     * @return Token[]
     */
    private static function parsePattern(Pattern $pattern): array
    {
        $tokens = [];
        $pattern = $pattern->toString();

        if (!str_starts_with($pattern, '/')) {
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
     * @param string $path
     * @return Rule|null
     */
    public function match(string $path): ?Rule
    {
        $path = explode('/', $path);
        $index = $this->start->match($path);
        return $index >= 0
            ? $this->rules[$index]
            : null;
    }

    /**
     * @return array{priority: int, edges: array<string, State>, "**": bool}
     */
    public function asJson(): array
    {
        return $this->start->jsonSerialize();
    }

    /**
     * @return array{nodes: array<string, int>, edges: array{from: string, to: string, label: string}[]}
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
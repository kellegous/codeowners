<?php

namespace Kellegous\CodeOwners;

use Closure;

final class SimpleMatcher implements RuleMatcher
{
    /**
     * @var array{Closure(string):bool, Rule}[]
     */
    private array $rules;

    /**
     * @param iterable<Rule> $rules
     */
    public function __construct(iterable $rules)
    {
        $matchers = [];
        foreach ($rules as $rule) {
            $matchers[] = [$rule->getPattern()->getMatcher(), $rule];
        }
        $this->rules = array_reverse($matchers);
    }

    /**
     * @param string $path
     * @return Rule|null
     */
    public function match(string $path): ?Rule
    {
        foreach ($this->rules as [$matcher, $rule]) {
            if ($matcher($path)) {
                return $rule;
            }
        }
        return null;
    }
}
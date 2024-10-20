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
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        $matchers = array_map(
            fn(Rule $rule) => [$rule->getPattern()->getMatcher(), $rule],
            $rules
        );
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
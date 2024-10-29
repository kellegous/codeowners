<?php

namespace Kellegous\CodeOwners;

use Closure;

/**
 * SimpleMatcher does a linear search of the rules present in the code owners
 * file. This is a simpler matcher that uses less memory but is slower than an
 * AutomatonMatcher..
 */
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
     * @inerhitDoc
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

        foreach ($this->rules as [$matcher, $rule]) {
            if ($matcher($path)) {
                return $rule;
            }
        }
        return null;
    }
}
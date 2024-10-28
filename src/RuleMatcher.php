<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

/**
 * A rule matcher is responsible for matching a path to a rule.
 */
interface RuleMatcher
{
    /**
     * Given the relative path to a file, this method returns the rule that matches the path, if such a rule exists.
     * * Note that path is a relative path from the root of the repository. $path should not begin with a slash nor should
     * * it end with a slash.
     *
     * @param string $path
     * @return Rule|null
     */
    public function match(string $path): ?Rule;
}
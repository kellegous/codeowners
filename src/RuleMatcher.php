<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

interface RuleMatcher
{
    public function match(string $path): ?Rule;
}
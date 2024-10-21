<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners\AutomataMatcher;

final class Token
{
    private string $pattern;

    private string $regex;

    public function __construct(
        string $pattern,
        string $regex
    ) {
        $this->pattern = $pattern;
        $this->regex = $regex;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners\AutomataMatcher;

final class Token
{
    private string $pattern;

    private string $regex;

    private bool $isLiteral;

    public function __construct(
        string $pattern,
        string $regex,
        bool $isLiteral
    ) {
        $this->pattern = $pattern;
        $this->regex = $regex;
        $this->isLiteral = $isLiteral;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function isLiteral(): bool
    {
        return $this->isLiteral;
    }
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners\AutomataMatcher;

/**
 * Represents a "segment" of a pattern. Each pattern consists of a number of
 * tokens separated by "/" characters. This class holds the raw string
 * representation of that segment as well as the regular expression that matches
 * that segment.
 *
 * @internal
 */
final class Token
{
    /**
     * @var string
     */
    private string $pattern;

    /**
     * @var string
     */
    private string $regex;

    /**
     * @param string $pattern
     * @param string $regex
     */
    public function __construct(
        string $pattern,
        string $regex
    ) {
        $this->pattern = $pattern;
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }
}

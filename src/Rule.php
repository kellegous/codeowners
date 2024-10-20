<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

final class Rule
{
    /**
     * @var Pattern
     */
    private Pattern $pattern;

    /**
     * @var string[]
     */
    private array $owners;

    private SourceInfo $sourceInfo;

    /**
     * @param Pattern $pattern
     * @param string[] $owners
     */
    public function __construct(
        Pattern $pattern,
        array $owners,
        SourceInfo $sourceInfo
    ) {
        $this->pattern = $pattern;
        $this->owners = $owners;
        $this->sourceInfo = $sourceInfo;
    }

    public static function parse(
        string $line,
        SourceInfo $sourceInfo
    ): Rule {
        $parts = preg_split('/\s+/', $line);
        if (!is_array($parts) || count($parts) < 1) {
            throw new ParseException(
                "Failed to parse rule on line {$sourceInfo->getLineNumber()}"
            );
        }
        $pattern = array_shift($parts);
        return new self(
            Pattern::parse($pattern),
            $parts,
            $sourceInfo
        );
    }

    /**
     * @return Pattern
     */
    public function getPattern(): Pattern
    {
        return $this->pattern;
    }

    /**
     * @return string[]
     */
    public function getOwners(): array
    {
        return $this->owners;
    }

    /**
     * @return SourceInfo
     */
    public function getSourceInfo(): SourceInfo
    {
        return $this->sourceInfo;
    }
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

/**
 * Represents a rule in the code owners file. A rule consists of a pattern, a list of zero or more
 * owners and an optional trailing comment.
 */
final class Rule implements Entry
{
    /**
     * @var Pattern
     */
    private Pattern $pattern;

    /**
     * @var string[]
     */
    private array $owners;

    /**
     * @var SourceInfo
     */
    private SourceInfo $sourceInfo;

    /**
     * @var string|null
     */
    private ?string $comment;

    /**
     * @param Pattern $pattern
     * @param string[] $owners
     * @param SourceInfo $sourceInfo
     * @param string|null $comment
     */
    public function __construct(
        Pattern $pattern,
        array $owners,
        SourceInfo $sourceInfo,
        ?string $comment
    ) {
        $this->pattern = $pattern;
        $this->owners = $owners;
        $this->sourceInfo = $sourceInfo;
        $this->comment = $comment;
    }

    /**
     * Parse a rule from a line in the code owners file.
     *
     * @param string $line
     * @param SourceInfo $sourceInfo
     * @param string|null $comment
     * @return Rule
     * @throws ParseException
     */
    public static function parse(
        string $line,
        SourceInfo $sourceInfo,
        ?string $comment
    ): Rule {
        $ix = self::cutAfterPattern($line);
        if ($ix === -1) {
            $pattern = $line;
            $owners = [];
        } else {
            $pattern = substr($line, 0, $ix);
            $owners = preg_split('/\s+/', trim(substr($line, $ix)));
        }

        if ($pattern === '' || !is_array($owners)) {
            throw new ParseException(
                "Failed to parse rule on line {$sourceInfo->getLineNumber()}"
            );
        }

        return new self(
            Pattern::parse($pattern),
            $owners,
            $sourceInfo,
            $comment
        );
    }

    /**
     * Finds the end of the pattern in the given line, taking into account escaping characters.
     *
     * @param string $line
     * @return int
     */
    private static function cutAfterPattern(string $line): int
    {
        $escaped = false;
        for ($i = 0, $n = strlen($line); $i < $n; $i++) {
            if ($escaped) {
                continue;
            }

            $c = $line[$i];
            if ($c === '\\') {
                $escaped = true;
                continue;
            }

            if (ctype_space($c)) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * Get the pattern for the rule.
     *
     * @return Pattern
     */
    public function getPattern(): Pattern
    {
        return $this->pattern;
    }

    /**
     * Get the owners for the rule.
     *
     * @return string[]
     */
    public function getOwners(): array
    {
        return $this->owners;
    }

    /**
     * Get the trailing comment, if any. The comment will include the leading `#` character.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @Override
     * @return SourceInfo
     */
    public function getSourceInfo(): SourceInfo
    {
        return $this->sourceInfo;
    }

    /**
     * @Override
     * @return string
     */
    public function toString(): string
    {
        $line = $this->pattern->toString();

        if (!empty($this->owners)) {
            $line .= ' ' . implode(' ', $this->owners);
        }

        if ($this->comment !== null) {
            $line .= ' ' . $this->comment;
        }

        return $line;
    }
}
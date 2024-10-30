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
     * @return Rule
     * @throws ParseException
     */
    public static function parse(
        string $line,
        SourceInfo $sourceInfo
    ): Rule {
        [$pattern, $line] = self::cutAfterPattern($line);
        [$owners, $comment] = self::cutBeforeComment($line);
        $owners = $owners !== ''
            ? preg_split('/\s+/', $owners)
            : [];

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
     * Split the line after the pattern.
     *
     * @param string $line
     * @return array{string, string}
     */
    private static function cutAfterPattern(string $line): array
    {
        $escaped = false;
        for ($i = 0, $n = strlen($line); $i < $n; $i++) {
            if ($escaped) {
                $escaped = false;
                continue;
            }

            $c = $line[$i];
            if ($c === '\\') {
                $escaped = true;
                continue;
            }

            if (ctype_space($c)) {
                return [
                    substr($line, 0, $i),
                    substr($line, $i)
                ];
            }
        }
        return [$line, ''];
    }

    /**
     * @param string $line
     * @return array{string, string|null}
     */
    private static function cutBeforeComment(string $line): array
    {
        $ix = strpos($line, '#');
        return $ix === false
            ? [trim($line), null]
            : [trim(substr($line, 0, $ix)), trim(substr($line, $ix))];
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
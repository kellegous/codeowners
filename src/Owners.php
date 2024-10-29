<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use Iterator;

/**
 * Represents the contents of a code owners file.
 */
final class Owners
{
    /**
     * @var Entry[]
     */
    private array $entries;

    /**
     * @param Entry[] $entries
     */
    private function __construct(
        array $entries
    ) {
        $this->entries = $entries;
    }

    /**
     * Parse the contents of a code owners file.
     *
     * @param string $filename
     * @return self
     * @throws ParseException
     */
    public static function fromFile(string $filename): self
    {
        $entries = iterator_to_array(
            self::entriesFrom(
                self::readLinesFrom($filename),
                $filename
            )
        );
        return new self($entries);
    }

    /**
     * @param iterable<int, string> $iter
     * @param string|null $filename
     * @return Iterator<Entry>
     * @throws ParseException
     */
    private static function entriesFrom(
        iterable $iter,
        ?string $filename
    ): Iterator {
        foreach ($iter as $index => $line) {
            yield self::parseEntry(
                $line,
                new SourceInfo($index + 1, $filename)
            );
        }
    }

    /**
     * @param string $line
     * @param SourceInfo $sourceInfo
     * @return Entry
     * @throws ParseException
     */
    private static function parseEntry(
        string $line,
        SourceInfo $sourceInfo
    ): Entry {
        $comment_start = strpos($line, '#');
        if ($comment_start === false) {
            $content = trim($line);
            $comment = '';
        } else {
            $content = trim(substr($line, 0, $comment_start));
            $comment = trim(substr($line, $comment_start));
        }

        if ($content === '' && $comment === '') {
            return new Blank($sourceInfo);
        } elseif ($content === '' && $comment !== '') {
            return new Comment($comment, $sourceInfo);
        }

        return Rule::parse(
            $content,
            $sourceInfo,
            $comment === '' ? null : $comment
        );
    }

    /**
     * @param string $filename
     * @return iterable<string>
     * @throws ParseException
     */
    private static function readLinesFrom(string $filename): iterable
    {
        $file = fopen($filename, 'r');
        if ($file === false) {
            throw new ParseException("Failed to open file: {$filename}");
        }
        try {
            while (($line = fgets($file)) !== false) {
                yield trim($line);
            }
        } finally {
            fclose($file);
        }
    }

    /**
     * Parse the contents of a code owners file as a string.
     *
     * @param string $content
     * @param string|null $filename
     * @return self
     * @throws ParseException
     */
    public static function fromString(
        string $content,
        ?string $filename = null
    ): self {
        if ($content === '') {
            return new self([]);
        }

        $entries = iterator_to_array(
            self::entriesFrom(
                explode(PHP_EOL, $content),
                $filename
            )
        );

        return new self($entries);
    }

    /**
     * Get only the rules that are present in the code owners structure. This omits
     * blank lines and comments.
     *
     * @return iterable<Rule>
     */
    public function getRules(): iterable
    {
        foreach ($this->entries as $entry) {
            if ($entry instanceof Rule) {
                yield $entry;
            }
        }
    }

    /**
     * Get all entries from the code owners file.
     *
     * @return iterable<Entry>
     */
    public function getEntries(): iterable
    {
        return $this->entries;
    }
}
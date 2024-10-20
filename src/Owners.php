<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use Iterator;

final class Owners
{
    /**
     * @var Rule[]
     */
    private array $rules;

    /**
     * @param Rule[] $rules
     */
    private function __construct(
        array $rules
    ) {
        $this->rules = $rules;
    }

    /**
     * @param string $filename
     * @return self
     * @throws ParseException
     */
    public static function fromFile(string $filename): self
    {
        $rules = iterator_to_array(
            self::rulesFrom(
                self::readLinesFrom($filename),
                $filename
            )
        );

        return new self($rules);
    }

    /**
     * @param iterable<int, string> $iter
     * @param string|null $filename
     * @return Iterator<Rule>
     * @throws ParseException
     */
    private static function rulesFrom(
        iterable $iter,
        ?string $filename
    ): Iterator {
        foreach ($iter as $index => $line) {
            $line = self::stripComment($line);
            if ($line === '') {
                continue;
            }

            yield Rule::parse(
                $line,
                new SourceInfo($index + 1, $filename)
            );
        }
    }

    private static function stripComment(string $line): string
    {
        $comment_start = strpos($line, '#');
        if ($comment_start === false) {
            return $line;
        }
        return trim(substr($line, 0, $comment_start));
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
     * @param string $content
     * @param string|null $filename
     * @return self
     * @throws ParseException
     */
    public static function fromString(
        string $content,
        ?string $filename = null
    ): self {
        $rules = iterator_to_array(
            self::rulesFrom(
                explode(PHP_EOL, $content),
                $filename
            )
        );

        return new self($rules);
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
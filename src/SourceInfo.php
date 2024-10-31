<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

/**
 * Represents the source information, file and line number, for any entry
 * in a code owners file.
 */
final class SourceInfo
{
    /**
     * @var string|null
     */
    private ?string $filename;

    /**
     * @var int
     */
    private int $lineNumber;

    /**
     * @param int $lineNumber
     * @param string|null $filename
     */
    public function __construct(
        int $lineNumber,
        ?string $filename = null
    ) {
        $this->lineNumber = $lineNumber;
        $this->filename = $filename;
    }

    /**
     * The filename from which the entry was parsed. If the entry was parsed
     * as a string without a filename given, this will return `null`.
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * The line number from which the entry was parsed.
     *
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}

<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

final class SourceInfo
{
    private ?string $filename;

    private int $lineNumber;

    public function __construct(
        int $lineNumber,
        ?string $filename = null
    ) {
        $this->lineNumber = $lineNumber;
        $this->filename = $filename;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
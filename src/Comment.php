<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

final class Comment implements Entry
{
    private string $text;

    private SourceInfo $sourceInfo;

    public function __construct(
        string $text,
        SourceInfo $sourceInfo
    ) {
        $this->text = $text;
        $this->sourceInfo = $sourceInfo;
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
        return $this->text;
    }
}
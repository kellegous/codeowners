<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

final class Blank implements Entry
{
    private SourceInfo $sourceInfo;

    public function __construct(
        SourceInfo $sourceInfo
    ) {
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
        return '';
    }
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

/**
 * Represents a comment in the code owners file.
 */
final class Comment implements Entry
{
    /**
     * The text of the comment including the leading `#`.
     * @var string
     */
    private string $text;

    /**
     * @var SourceInfo
     */
    private SourceInfo $sourceInfo;

    /**
     * @param string $text
     * @param SourceInfo $sourceInfo
     */
    public function __construct(
        string $text,
        SourceInfo $sourceInfo
    ) {
        $this->text = $text;
        $this->sourceInfo = $sourceInfo;
    }

    /**
     * @inheritDoc
     * @Override
     * @return SourceInfo
     */
    public function getSourceInfo(): SourceInfo
    {
        return $this->sourceInfo;
    }

    /**
     * #inheritDoc
     * @Override
     * @return string
     */
    public function toString(): string
    {
        return $this->text;
    }
}

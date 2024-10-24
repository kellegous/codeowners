<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

/**
 * Entry represents a line in a CODEOWNERS file. An entry can take several forms. It can be a rule, a comment, or it
 * can be empty. Treating a CDOEOWNERS file as a collection of entries can be useful if you are mutating the file and
 * with to preserve comments or spacial groupings.
 */
interface Entry
{
    /**
     * Returns the source information for this entry.
     *
     * @return SourceInfo
     */
    public function getSourceInfo(): SourceInfo;

    /**
     * Returns the string representation of this entry.
     *
     * @return string
     */
    public function toString(): string;
}
<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use Exception;

/**
 * Thrown when the contents of an owners file cannot be parsed.
 */
final class ParseException extends Exception
{
}
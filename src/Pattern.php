<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use Closure;

/**
 * Represents a file pattern part of a rule in a code owners file.
 */
final class Pattern
{
    /**
     * @var string
     */
    private string $pattern;

    /**
     * @param string $pattern
     */
    private function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Parse the pattern from a string to a Pattern instance.
     *
     * @param string $pattern
     * @return self
     * @throws ParseException
     */
    public static function parse(string $pattern): self
    {
        if (strpos($pattern, '***') !== false || $pattern === '') {
            throw new ParseException("Invalid pattern: $pattern");
        }
        return new self($pattern);
    }

    /**
     * Get the string representation of the pattern.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->pattern;
    }

    /**
     * Get a matcher function for the pattern.
     *
     * @return Closure(string):bool
     */
    public function getMatcher(): Closure
    {
        $regexp = self::toRegexp($this->pattern);
        return function (string $path) use ($regexp): bool {
            $m = preg_match($regexp, $path);
            if ($m === false) {
                throw new \RuntimeException('Failed to match pattern');
            }
            return $m === 1;
        };
    }

    /**
     * Create a regular expression from a pattern.
     *
     * @param string $pattern
     * @return string
     */
    private static function toRegexp(string $pattern): string
    {
        if ($pattern === '/') {
            return '/\A\Z/';
        }

        $segments = explode('/', $pattern);
        if ($segments[0] === '') {
            array_shift($segments);
        } elseif (count($segments) === 1 || (count($segments) === 2 && $segments[1] === '')) {
            if ($segments[0] !== '**') {
                $segments = array_merge(['**'], $segments);
            }
        }

        if (count($segments) > 1 && $segments[count($segments) - 1] === '') {
            $segments[count($segments) - 1] = '**';
        }


        $last_seg_index = count($segments) - 1;
        $need_slash = false;
        $buffer = '\A';
        foreach ($segments as $i => $seg) {
            switch ($seg) {
                case '**':
                    if ($i === 0 && $i == $last_seg_index) {
                        $buffer .= '.+';
                    } elseif ($i === 0) {
                        $buffer .= '(?:.+/)?';
                        $need_slash = false;
                    } elseif ($i === $last_seg_index) {
                        $buffer .= '/.*';
                    } else {
                        $buffer .= '(?:/.+)?';
                        $need_slash = true;
                    }
                    break;
                case '*':
                    if ($need_slash) {
                        $buffer .= '/';
                    }
                    $buffer .= '[^/]+';
                    $need_slash = true;
                    break;
                default:
                    if ($need_slash) {
                        $buffer .= '/';
                    }

                    $escape = false;
                    for ($j = 0, $n = strlen($seg); $j < $n; $j++) {
                        if ($escape) {
                            $escape = false;
                            $buffer .= preg_quote($seg[$j], '#');
                            continue;
                        }

                        switch ($seg[$j]) {
                            case '\\':
                                $escape = true;
                                break;
                            case '*':
                                $buffer .= '[^/]*';
                                break;
                            case '?':
                                $buffer .= '[^/]';
                                break;
                            default:
                                $buffer .= preg_quote($seg[$j], '#');
                                break;
                        }
                    }

                    if ($i === $last_seg_index) {
                        $buffer .= '(?:/.+)?';
                    }

                    $need_slash = true;
                    break;
            }
        }
        $buffer .= '\Z';
        return "#$buffer#";
    }
}

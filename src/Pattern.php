<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use RuntimeException;

final class Pattern
{
    private string $pattern;

    private string $regexp;

    private SourceInfo $sourceInfo;

    public function __construct(
        string $pattern,
        string $regexp,
        SourceInfo $sourceInfo
    ) {
        $this->pattern = $pattern;
        $this->regexp = $regexp;
        $this->sourceInfo = $sourceInfo;
    }

    public static function parse(
        string $pattern,
        SourceInfo $sourceInfo
    ): self {
        return new self($pattern, self::toRegexp($pattern), $sourceInfo);
    }

    private static function toRegexp(string $pattern): string
    {
        if (strpos($pattern, '***') !== false || $pattern === '') {
            throw new ParseException("Invalid pattern: $pattern");
        } elseif ($pattern === '/') {
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

    public function matches(string $path): bool
    {
        $m = preg_match($this->regexp, $path);
        if ($m === false) {
            throw new RuntimeException("match error: {$this->regexp}");
        }
        return $m === 1;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getRegexp(): string
    {
        return $this->regexp;
    }

    public function getSourceInfo(): SourceInfo
    {
        return $this->sourceInfo;
    }
}
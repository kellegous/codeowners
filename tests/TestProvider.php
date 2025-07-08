<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use Closure;
use Exception;
use InvalidArgumentException;

/**
 * Provides a common set of tests for all RuleMatchers.
 */
final class TestProvider
{

    private function __construct()
    {
    }

    /**
     * @template T extends RulMatcher
     * @param Closure(iterable<Rule>):T $create_matcher
     * @return iterable<array{T, string, ?int}>
     * @throws ParseException
     */
    public static function forMatchTests(
        Closure $create_matcher,
        string $unique_identifier = ""
    ): iterable {
        $tests = include __DIR__ . '/matcher_tests.php';
        foreach ($tests as $desc => ['lines' => $lines, 'expected' => $expected]) {
            $rules = Owners::fromString(
                implode(PHP_EOL, $lines)
            )->getRules();
            $matcher = $create_matcher($rules);
            if ($unique_identifier !== "") {
                $unique_identifier = "{$unique_identifier}: ";
            }

            foreach ($expected as $path => $line) {
                yield "{$unique_identifier}{$desc} w/ {$path}" => [
                    $matcher,
                    $path,
                    $line
                ];
            }
        }
    }

    /**
     * @template T extends RuleMatcher
     * @param Closure(iterable<Rule>):T $create_matcher
     * @return iterable<array{T, string, ?int}>
     * @throws ParseException
     */
    public static function forExampleTests(
        Closure $create_matcher,
        string $unique_identifier = ""
    ): iterable {
        $owners = Owners::fromFile(__DIR__ . '/CODEOWNERS.example');
        $matcher = $create_matcher($owners->getRules());
        $tests = include __DIR__ . '/example_tests.php';
        if ($unique_identifier !== "") {
            $unique_identifier = "{$unique_identifier}: ";
        }

        foreach ($tests as $path => $line) {
            yield "{$unique_identifier}{$path}" => [
                $matcher,
                $path,
                $line
            ];
        }
    }

    /**
     * @template T extends RuleMatcher
     * @param Closure(iterable<Rule>):T $create_matcher
     * @return iterable<array{T, string, Exception}>
     */
    public static function forBadInputTests(
        Closure $create_matcher,
        string $unique_identifier = ""
    ): iterable {
        $matcher = $create_matcher([]);
        if ($unique_identifier !== "") {
            $unique_identifier = "{$unique_identifier}: ";
        }

        yield "{$unique_identifier}leading /" => [
            $matcher,
            '/foo/bar',
            new InvalidArgumentException(
                "path should be a relative path to a file, thus it cannot start or end with a /"
            )
        ];
        yield "{$unique_identifier}trailing /" => [
            $matcher,
            'foo/bar/',
            new InvalidArgumentException(
                "path should be a relative path to a file, thus it cannot start or end with a /"
            )
        ];
    }
}

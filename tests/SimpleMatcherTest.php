<?php

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers SimpleMatcher
 */
class SimpleMatcherTest extends TestCase
{
    /**
     * @return iterable<array{SimpleMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getMatchTests(): iterable
    {
        $tests = include __DIR__ . '/matcher_tests.php';
        foreach ($tests as $desc => ['lines' => $lines, 'expected' => $expected]) {
            $matcher = self::matcherWith($lines);
            foreach ($expected as $path => $line) {
                yield "{$desc} w/ {$path}" => [
                    $matcher,
                    $path,
                    $line
                ];
            }
        }
    }

    /**
     * @param string[] $lines
     * @return SimpleMatcher
     * @throws ParseException
     */
    private static function matcherWith(array $lines): SimpleMatcher
    {
        return new SimpleMatcher(
            Owners::fromString(
                implode(PHP_EOL, $lines)
            )->getRules()
        );
    }

    /**
     * @return iterable<array{SimpleMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getExampleTests(): iterable
    {
        $owners = Owners::fromFile(__DIR__ . '/CODEOWNERS.example');
        $matcher = new SimpleMatcher($owners->getRules());
        $tests = include __DIR__ . '/example_tests.php';
        foreach ($tests as $path => $line) {
            yield $path => [
                $matcher,
                $path,
                $line
            ];
        }
    }

    /**
     * @param SimpleMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider getMatchTests
     */
    public function testMatch(
        SimpleMatcher $matcher,
        string $path,
        ?int $expected_line
    ): void {
        $rule = $matcher->match($path);
        $line = $rule !== null
            ? $rule->getSourceInfo()->getLineNumber()
            : null;
        self::assertSame($expected_line, $line);
    }

    /**
     * @param SimpleMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider getExampleTests
     */
    public function testExampleMatch(
        SimpleMatcher $matcher,
        string $path,
        ?int $expected_line
    ): void {
        $rule = $matcher->match($path);
        $line = $rule !== null
            ? $rule->getSourceInfo()->getLineNumber()
            : null;
        self::assertSame($expected_line, $line);
    }
}
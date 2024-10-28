<?php

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers AutomataMatcher
 */
final class AutomataMatcherTest extends TestCase
{
    /**
     * @return iterable<array{AutomataMatcher, string, ?int}>
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
     * @return AutomataMatcher
     * @throws ParseException
     */
    private static function matcherWith(array $lines): AutomataMatcher
    {
        return AutomataMatcher::build(
            Owners::fromString(
                implode(PHP_EOL, $lines)
            )->getRules()
        );
    }

    /**
     * @return iterable<array{AutomataMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getExampleTests(): iterable
    {
        $owners = Owners::fromFile(__DIR__ . '/CODEOWNERS.example');
        $matcher = AutomataMatcher::build($owners->getRules());
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
     * @param AutomataMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider getMatchTests
     */
    public function testMatch(
        AutomataMatcher $matcher,
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
     * @param AutomataMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider getExampleTests
     */
    public function testExampleMatch(
        AutomataMatcher $matcher,
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

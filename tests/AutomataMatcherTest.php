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
    public static function matchTests(): iterable
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

//        yield from self::createTest(
//            'wildcard overlap w/ literal',
//            ['/a', '/a/d'],
//            [
//                'a/x/d' => 1,
//                'a/d' => 2,
//                'a' => 1,
//            ]
//        );
//
//        yield from self::createTest(
//            '/a*',
//            ['/a*'],
//            [
//                'b/a' => null,
//                'a' => 1,
//                'a1' => 1,
//                'a2/b' => 1,
//                'a2/c/d' => 1,
//            ]
//        );
//
//        yield from self::createTest(
//            '/*a',
//            ['/*a'],
//            [
//                'b/a' => null,
//                'a' => 1,
//                '1a' => 1,
//                '2a/b' => 1,
//                '2a/b/c' => 1,
//            ]
//        );
//
//        yield from self::createTest(
//            '*/',
//            ['*/'],
//            [
//                'a' => null,
//                'b/c' => 1,
//                'b/d/e' => 1,
//                'c/a' => 1,
//                'c/b/c' => 1,
//                'c/b/d/e' => 1,
//            ]
//        );
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

//    private static function createTest(
//        string $description,
//        array $lines,
//        array $paths
//    ): iterable {
//        $matcher = self::matcherWith($lines);
//        foreach ($paths as $path => $expected) {
//            yield "{$description} w/ {$path}" => [
//                $matcher,
//                $path,
//                $expected
//            ];
//        }
//    }

    /**
     * @param AutomataMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider matchTests
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
}
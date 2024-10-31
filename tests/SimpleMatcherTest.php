<?php

namespace Kellegous\CodeOwners;

use Exception;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestProvider.php';

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
        return TestProvider::forMatchTests(
            fn(iterable $rules) => new SimpleMatcher($rules)
        );
    }

    /**
     * @return iterable<array{SimpleMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getExampleTests(): iterable
    {
        return TestProvider::forExampleTests(
            fn(iterable $rules) => new SimpleMatcher($rules)
        );
    }

    /**
     * @return iterable<array{SimpleMatcher, string, Exception}>
     */
    public static function getBadInputTests(): iterable
    {
        return TestProvider::forBadInputTests(
            fn(iterable $rules) => new SimpleMatcher($rules)
        );
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

    /**
     * @param RuleMatcher $matcher
     * @param string $path
     * @param Exception $exception
     * @return void
     *
     * @dataProvider getBadInputTests
     */
    public function testBadInput(
        RuleMatcher $matcher,
        string $path,
        Exception $exception
    ): void {
        self::expectExceptionObject($exception);
        $matcher->match($path);
    }
}

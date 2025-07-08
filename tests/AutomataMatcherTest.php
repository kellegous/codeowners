<?php

namespace Kellegous\CodeOwners;

use Exception;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestProvider.php';

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
        return TestProvider::forMatchTests(
            fn(iterable $rules) => AutomataMatcher::build($rules)
        );
    }

    /**
     * @return iterable<array{AutomataMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getSerializedMatchTests(): iterable
    {
        return TestProvider::forMatchTests(
            fn(iterable $rules) => unserialize(serialize(AutomataMatcher::build($rules))),
            "serialized"
        );
    }

    /**
     * @return iterable<array{AutomataMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function getExampleTests(): iterable
    {
        return TestProvider::forExampleTests(
            fn(iterable $rules) => AutomataMatcher::build($rules)
        );
    }

    /**
     * @return iterable<array{AutomataMatcher, string, Exception}>
     */
    public static function getBadInputTests(): iterable
    {
        return TestProvider::forBadInputTests(
            fn(iterable $rules) => AutomataMatcher::build($rules)
        );
    }

    /**
     * @param AutomataMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     *
     * @dataProvider getMatchTests
     * @dataProvider getSerializedMatchTests
     * @dataProvider getExampleTests
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

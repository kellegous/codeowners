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
        $matcher = AutomataMatcher::build(
            Owners::fromString(
                implode(PHP_EOL, ['/a', '/a/d'])
            )->getRules()
        );
        yield 'example' => [
            $matcher,
            'a/x/d',
            1
        ];
    }

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
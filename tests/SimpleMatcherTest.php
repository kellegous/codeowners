<?php

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers SimpleMatcher
 */
class SimpleMatcherTest extends TestCase
{
    /**
     * @return iterable<string, array{SimpleMatcher, string, ?int}>
     * @throws ParseException
     */
    public static function githubExampleTests(): iterable
    {
        $owners = Owners::fromFile(__DIR__ . '/CODEOWNERS.example');
        $matcher = new SimpleMatcher($owners->getRules());

        yield 'example' => [
            $matcher,
            'x/y/.js',
            16
        ];
    }

    /**
     * @param SimpleMatcher $matcher
     * @param string $path
     * @param int|null $expected_line
     * @return void
     * @dataProvider githubExampleTests
     */
    public function testGithubExample(
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
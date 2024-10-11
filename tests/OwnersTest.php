<?php

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers Owners
 */
class OwnersTest extends TestCase
{
    /**
     * @return iterable<string, array{Owners, string, ?int}>
     * @throws ParseException
     */
    public static function githubExampleTests(): iterable
    {
        $owners = Owners::fromFile(__DIR__ . '/CODEOWNERS.example');

        yield 'example' => [
            $owners,
            'x/y/.js',
            16
        ];
    }

    /**
     * @param Owners $owners
     * @param string $path
     * @param int|null $expected_line
     * @return void
     * @dataProvider githubExampleTests
     */
    public function testGithubExample(
        Owners $owners,
        string $path,
        ?int $expected_line
    ): void {
        $rule = $owners->match($path);
        $line = $rule !== null
            ? $rule->getPattern()->getSourceInfo()->getLineNumber()
            : null;
        self::assertSame($expected_line, $line);
    }
}
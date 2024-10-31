<?php

declare(strict_types=1);

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers Owners
 */
class OwnersTest extends TestCase
{
    /**
     * @return iterable<string, array{string, Entry[]}>
     * @throws ParseException
     */
    public static function getEntriesTests(): iterable
    {
        yield 'empty file' => [
            '',
            []
        ];

        yield 'all blanks' => [
            "\n\n",
            [
                new Blank(new SourceInfo(1, null)),
                new Blank(new SourceInfo(2, null)),
                new Blank(new SourceInfo(3, null)),
            ]
        ];

        yield 'all comments' => [
            self::fromLines([
                '  # comment 1',
                ' # comment 2',
                '# comment 3',
            ]),
            [
                new Comment('# comment 1', new SourceInfo(1, null)),
                new Comment('# comment 2', new SourceInfo(2, null)),
                new Comment('# comment 3', new SourceInfo(3, null)),
            ]
        ];

        yield 'mixed entries' => [
            self::fromLines([
                ' # first rule',
                '',
                ' /a/ # no owner',
                ' # second rule',
                '     /b/ @a',
                '    # third rule',
                '/c/ @a @b # inline comment',
            ]),
            [
                new Comment('# first rule', new SourceInfo(1, null)),
                new Blank(new SourceInfo(2, null)),
                new Rule(
                    Pattern::parse('/a/'),
                    [],
                    new SourceInfo(3, null),
                    '# no owner'
                ),
                new Comment('# second rule', new SourceInfo(4, null)),
                new Rule(
                    Pattern::parse('/b/'),
                    ['@a'],
                    new SourceInfo(5, null),
                    null
                ),
                new Comment('# third rule', new SourceInfo(6, null)),
                new Rule(
                    Pattern::parse('/c/'),
                    ['@a', '@b'],
                    new SourceInfo(7, null),
                    '# inline comment'
                )
            ]
        ];

        yield 'patterns w/ escapes' => [
            self::fromLines([
                '/a\\ b/', #escaped space
                '/z\\\\b/ @a', // escaped backslash
                '/c\\ / @a # \\ a comment', // escaped space and backslash in comment
            ]),
            [
                new Rule(
                    Pattern::parse('/a\ b/'),
                    [],
                    new SourceInfo(1, null),
                    null
                ),
                new Rule(
                    Pattern::parse('/z\\\b/'),
                    ['@a'],
                    new SourceInfo(2, null),
                    null
                ),
                new Rule(
                    Pattern::parse('/c\\ /'),
                    ['@a'],
                    new SourceInfo(3, null),
                    '# \\ a comment'
                )
            ]
        ];
    }

    /**
     * @param string[] $lines
     * @return string
     */
    private static function fromLines(array $lines): string
    {
        return implode(PHP_EOL, $lines);
    }

    /**
     * @return iterable<string, array{Entry[], string}>
     */
    public static function getToStringTests(): iterable
    {
        yield 'empty' => [
            [],
            "\n"
        ];

        $expected = <<<CODEOWNERS
            # comment 1
            # comment 2
            # comment 3
            
            CODEOWNERS;

        yield 'all comments' => [
            [
                new Comment('# comment 1', new SourceInfo(1, null)),
                new Comment('# comment 2', new SourceInfo(2, null)),
                new Comment('# comment 3', new SourceInfo(3, null)),
            ],
            $expected
        ];

        $expected = <<<CODEOWNERS
            
            
            CODEOWNERS;
        yield 'a blank' => [
            [
                new Blank(new SourceInfo(1, null)),
            ],
            $expected
        ];

        $expected = <<<CODEOWNERS
            /a/ @a # first rule
            /b/ @b
            # comment 1
            
            /z
            
            CODEOWNERS;
        yield 'mixed' => [
            [
                new Rule(
                    Pattern::parse('/a/'),
                    ['@a'],
                    new SourceInfo(1, null),
                    '# first rule'
                ),
                new Rule(
                    Pattern::parse('/b/'),
                    ['@b'],
                    new SourceInfo(2, null),
                    null
                ),
                new Comment('# comment 1', new SourceInfo(3, null)),
                new Blank(new SourceInfo(4, null)),
                new Rule(
                    Pattern::parse('/z'),
                    [],
                    new SourceInfo(5, null),
                    null
                ),
            ],
            $expected
        ];
    }

    /**
     * @param string $owners_file
     * @param Entry[] $expected
     * @return void
     * @throws ParseException
     *
     * @dataProvider getEntriesTests
     */
    public function testEntries(
        string $owners_file,
        array $expected
    ): void {
        $owners = Owners::fromString($owners_file);
        self::assertEquals(
            $expected,
            $owners->getEntries()
        );
    }

    /**
     * @param Entry[] $entries
     * @param string $expected
     * @return void
     * @dataProvider getToStringTests
     */
    public function testToString(
        array $entries,
        string $expected
    ): void {
        $owners = new Owners($entries);
        self::assertSame(
            $expected,
            $owners->toString()
        );
    }

    public function testParseExceptionIsThrownIfCodeOwnersFileDoesNotExist(): void
    {
        $this->assertFileDoesNotExist('/this/file/does/not/exist');
        $this->expectException(ParseException::class);

        Owners::fromFile('/this/file/does/not/exist');
    }
}

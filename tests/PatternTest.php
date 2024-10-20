<?php

namespace Kellegous\CodeOwners;

use PHPUnit\Framework\TestCase;

/**
 * @covers Pattern
 */
class PatternTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function getPatternMatchTests(): iterable
    {
        $tests = array_merge(
            self::testsFrom(__DIR__ . '/patterns.json'),
            self::testsFrom(__DIR__ . '/new-patterns.json')
        );

        foreach ($tests as ['name' => $name, 'pattern' => $pattern, 'paths' => $paths]) {
            foreach ($paths as $path => $expected) {
                yield "{$name} w/ path {$path}" => [
                    $pattern,
                    $path,
                    $expected,
                ];
            }
        }
    }

    /**
     * @param string $filename
     * @return array{name:string, pattern:string, paths:array<string,bool>}[]
     */
    private static function testsFrom(string $filename): array
    {
        $contents = file_get_contents($filename);
        if ($contents === false) {
            throw new \RuntimeException('failed to read patterns.json');
        }

        $tests = json_decode($contents, true);
        if (!is_array($tests)) {
            throw new \RuntimeException('failed to decode patterns.json');
        }

        return $tests;
    }

    /**
     * @param string $pattern
     * @param string $path
     * @param bool $expected
     * @return void
     * @dataProvider getPatternMatchTests
     * @throws ParseException
     */
    public function testPatternMatch(
        string $pattern,
        string $path,
        bool $expected
    ): void {
        $pattern = Pattern::parse($pattern);
        $matcher = $pattern->getMatcher();
        $this->assertSame(
            $expected,
            $matcher($path)
        );
    }
}
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
    public static function getHmarrTests(): iterable
    {
        $contents = file_get_contents(__DIR__ . '/patterns.json');
        if ($contents === false) {
            throw new \RuntimeException('failed to read patterns.json');
        }

        $tests = json_decode($contents, true);
        if (!is_array($tests)) {
            throw new \RuntimeException('failed to decode patterns.json');
        }

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
     * @param string $pattern
     * @param string $path
     * @param bool $expected
     * @return void
     * @dataProvider getHmarrTests
     */
    public function testHmarrCases(
        string $pattern,
        string $path,
        bool $expected
    ): void {
        $pattern = Pattern::parse($pattern, new SourceInfo(1));
        $this->assertSame($expected, $pattern->matches($path));
    }
}
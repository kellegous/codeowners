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
        $tests = include __DIR__ . '/pattern_tests.php';
        foreach ($tests as $desc => ['pattern' => $pattern, 'paths' => $paths]) {
            foreach ($paths as $path => $expected) {
                yield "{$desc} w/ {$path}" => [
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

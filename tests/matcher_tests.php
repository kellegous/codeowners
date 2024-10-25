<?php

declare(strict_types=1);

return [
    'wildcard overlap w/ literal' => [
        'lines' => ['/a', '/a/d'],
        'expected' => [
            'a/x/d' => 1,
            'a/d' => 2,
            'a' => 1,
        ]
    ],

    '/a*' => [
        'lines' => ['/a*'],
        'expected' => [
            'b/a' => null,
            'a' => 1,
            'a1' => 1,
            'a2/b' => 1,
            'a2/c/d' => 1,
        ]
    ],

    '/*a' => [
        'lines' => ['/*a'],
        'expected' => [
            'b/a' => null,
            'a' => 1,
            '1a' => 1,
            '2a/b' => 1,
            '2a/b/c' => 1,
        ]
    ],

    '*/' => [
        'lines' => ['*/'],
        'expected' => [
            'a' => null,
            'b/c' => 1,
            'b/d/e' => 1,
            'c/a' => 1,
            'c/b/c' => 1,
            'c/b/d/e' => 1,
        ]
    ],

    '*a/' => [
        'lines' => ['*a/'],
        'expected' => [
            '1a' => null,
            '2a/b' => 1,
            '2a/c/d' => 1,
            'b/1a' => null,
            'b/2a/b' => 1,
            'b/2a/c/d' => 1,
        ]
    ],
    'a*/' => [
        'lines' => ['a*/'],
        'expected' => [
            'a1' => null,
            'a2/b' => 1,
            'a2/c/d' => 1,
            'b/a1' => null,
            'b/a2/b' => 1,
            'b/a2/c/d' => 1,
        ]
    ],
    '/a/' => [
        'lines' => ['/a/'],
        'expected' => [
            'a' => null,
            'a/b' => 1,
            'a/c/d' => 1,
            'b/a' => null,
        ]
    ],
    '/*/' => [
        'lines' => ['/*/'],
        'expected' => [
            'a' => null,
            'b/c' => 1,
            'b/d/e' => 1,
        ]
    ],
    '/*a/' => [
        'lines' => ['/*a/'],
        'expected' => [
            '1a' => null,
            '2a/b/' => 1,
            '2a/c/d/' => 1,
        ]
    ],
    '/a*/' => [
        'lines' => ['/a*/'],
        'expected' => [
            'a1' => null,
            'a2/b' => 1,
            'a2/c/d' => 1,
        ]
    ],
    '*' => [
        'lines' => ['*'],
        'expected' => [
            'a' => 1,
            'b/c' => 1,
            'b/d/e' => 1,
            'c/a' => 1,
            'c/b/c' => 1,
            'c/b/d/e' => 1,
        ]
    ],
    "*a" => [
        'lines' => ["*a"],
        'expected' => [
            '1a' => 1,
            '2a/b' => 1,
            '2a/c/d' => 1,
            'b/1a' => 1,
            'b/2a/b' => 1,
            'b/2a/c/d' => 1,
            'xb/xb' => null,
        ]
    ],
    "a*" => [
        'lines' => ["a*"],
        'expected' => [
            'a1' => 1,
            'a2/b' => 1,
            'a2/c/d' => 1,
            'b/a1' => 1,
            'b/a2/b' => 1,
            'b/a2/c/d' => 1,
            'xb/xb' => null,
        ]
    ],
    "/a" => [
        'lines' => ["/a"],
        'expected' => [
            'a' => 1,
            'a/b' => 1,
            'a/c/d' => 1,
            'b/a' => null,
            'xb/xb' => null,
        ]
    ],
    "/*" => [
        'lines' => ["/*"],
        'expected' => [
            'a' => 1,
            'b/c' => null,
            'b/d/e' => null,
        ]
    ],

];
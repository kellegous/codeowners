<?php

declare(strict_types=1);

return [
    "single-segment pattern" => [
        "pattern" => "foo",
        "paths" => [
            "foo" => true,
            "foo.txt" => false,
            "foo/bar" => true,
            "bar/foo" => true,
            "bar/foo.txt" => false,
            "bar/baz" => false,
            "bar/foo/baz" => true,
        ],
    ],
    "single-segment pattern with leading slash" => [
        "pattern" => "/foo",
        "paths" => [
            "foo" => true,
            "fool.txt" => false,
            "foo/bar" => true,
            "bar/foo" => false,
            "bar/baz" => false,
            "foo/bar/baz" => true,
            "bar/foo/baz" => false,
        ],
    ],
    "single-segment pattern with trailing slash" => [
        "pattern" => "foo/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "foo/bar/baz" => true,
            "bar/foo" => false,
            "bar/baz" => false,
            "bar/foo/baz" => true,
            "bar/foo/baz/qux" => true,
        ],
    ],
    "single-segment pattern with leading and trailing slash" => [
        "pattern" => "/foo/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "foo/bar/baz" => true,
            "bar/foo" => false,
            "bar/baz" => false,
            "bar/foo/baz" => false,
            "bar/foo/baz/qux" => false,
        ],
    ],
    "multi-segment (implicitly left-anchored) pattern" => [
        "pattern" => "foo/bar",
        "paths" => [
            "foo/bar" => true,
            "foo/bart" => false,
            "foo/bar/baz" => true,
            "baz/foo/bar" => false,
            "baz/foo/bar/qux" => false,
        ],
    ],
    "multi-segment pattern with leading slash" => [
        "pattern" => "/foo/bar",
        "paths" => [
            "foo/bar" => true,
            "foo/bart" => false,
            "foo/bar/baz" => true,
            "baz/foo/bar" => false,
            "baz/foo/bar/qux" => false,
        ],
    ],
    "multi-segment pattern with trailing slash" => [
        "pattern" => "foo/bar/",
        "paths" => [
            "foo/bar" => false,
            "foo/bart" => false,
            "foo/bar/baz" => true,
            "baz/foo/bar" => false,
            "baz/foo/bar/qux" => false,
        ],
    ],
    "multi-segment pattern with leading and trailing slash" => [
        "pattern" => "/foo/bar/",
        "paths" => [
            "foo/bar" => false,
            "foo/bart" => false,
            "foo/bar/baz" => true,
            "foo/bar/baz/qux" => true,
            "baz/foo/bar" => false,
            "baz/foo/bar/qux" => false,
        ],
    ],
    "single segment lone wildcard" => [
        "pattern" => "*",
        "paths" => [
            "foo" => true,
            "foo/bar" => true,
            "bar/foo" => true,
            "bar/foo/baz" => true,
            "bar/baz" => true,
            "xfoo" => true,
        ],
    ],
    "single segment pattern with wildcard" => [
        "pattern" => "f*",
        "paths" => [
            "foo" => true,
            "foo/bar" => true,
            "foo/bar/baz" => true,
            "bar/foo" => true,
            "bar/foo/baz" => true,
            "bar/baz" => false,
            "xfoo" => false,
        ],
    ],
    "single segment pattern with leading slash and lone wildcard" => [
        "pattern" => "/*",
        "paths" => [
            "foo" => true,
            "bar" => true,
            "foo/bar" => false,
            "foo/bar/baz" => false,
        ],
    ],
    "single segment pattern with leading slash and wildcard" => [
        "pattern" => "/f*",
        "paths" => [
            "foo" => true,
            "foo/bar" => true,
            "foo/bar/baz" => true,
            "bar/foo" => false,
            "bar/foo/baz" => false,
            "bar/baz" => false,
            "xfoo" => false,
        ],
    ],
    "single segment pattern with trailing slash and wildcard" => [
        "pattern" => "f*/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "bar/foo" => false,
            "bar/foo/baz" => true,
            "bar/baz" => false,
            "xfoo" => false,
        ],
    ],
    "single segment pattern with leading and trailing slash and lone wildcard" => [
        "pattern" => "/*/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "bar/foo" => true,
            "bar/foo/baz" => true,
        ],
    ],
    "single segment pattern with leading and trailing slash and wildcard" => [
        "pattern" => "/f*/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "bar/foo" => false,
            "bar/foo/baz" => false,
            "bar/baz" => false,
            "xfoo" => false,
        ],
    ],
    "single segment pattern with escaped wildcard" => [
        "pattern" => "f\\*o",
        "paths" => [
            "foo" => false,
            "f*o" => true,
        ],
    ],
    "pattern with trailing wildcard segment" => [
        "pattern" => "foo/*",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "foo/bar/baz" => false,
            "bar/foo" => false,
            "bar/foo/baz" => false,
            "bar/baz" => false,
            "xfoo" => false,
        ],
    ],
    "multi-segment pattern with wildcard" => [
        "pattern" => "foo/*.txt",
        "paths" => [
            "foo" => false,
            "foo/bar.txt" => true,
            "foo/bar/baz.txt" => false,
            "qux/foo/bar.txt" => false,
            "qux/foo/bar/baz.txt" => false,
        ],
    ],
    "multi-segment pattern with lone wildcard" => [
        "pattern" => "foo/*/baz",
        "paths" => [
            "foo" => false,
            "foo/bar" => false,
            "foo/baz" => false,
            "foo/bar/baz" => true,
            "foo/bar/baz/qux" => true,
        ],
    ],
    "single segment pattern with single-character wildcard" => [
        "pattern" => "f?o",
        "paths" => [
            "foo" => true,
            "fo" => false,
            "fooo" => false,
        ],
    ],
    "single segment pattern with escaped single-character wildcard" => [
        "pattern" => "f\\?o",
        "paths" => [
            "foo" => false,
            "f?o" => true,
        ],
    ],
    "leading double-asterisk wildcard" => [
        "pattern" => "**/foo/bar",
        "paths" => [
            "foo/bar" => true,
            "qux/foo/bar" => true,
            "qux/foo/bar/baz" => true,
            "foo/baz/bar" => false,
            "qux/foo/baz/bar" => false,
        ],
    ],
    "leading double-asterisk wildcard with regular wildcard" => [
        "pattern" => "**/*bar*",
        "paths" => [
            "bar" => true,
            "foo/bar" => true,
            "foo/rebar" => true,
            "foo/barrio" => true,
            "foo/qux/bar" => true,
        ],
    ],
    "trailing double-asterisk wildcard" => [
        "pattern" => "foo/bar/**",
        "paths" => [
            "foo/bar" => false,
            "foo/bar/baz" => true,
            "foo/bar/baz/qux" => true,
            "qux/foo/bar" => false,
            "qux/foo/bar/baz" => false,
        ],
    ],
    "middle double-asterisk wildcard" => [
        "pattern" => "foo/**/bar",
        "paths" => [
            "foo/bar" => true,
            "foo/bar/baz" => true,
            "foo/qux/bar/baz" => true,
            "foo/qux/quux/bar/baz" => true,
            "foo/bar/baz/qux" => true,
            "qux/foo/bar" => false,
            "qux/foo/bar/baz" => false,
        ],
    ],
    "middle double-asterisk wildcard with trailing slash" => [
        "pattern" => "foo/**/",
        "paths" => [
            "foo" => false,
            "foo/bar" => true,
            "foo/bar/" => true,
            "foo/bar/baz" => true,
        ],
    ],
    "middle double-asterisk wildcard with trailing wildcard" => [
        "pattern" => "foo/**/bar/b*",
        "paths" => [
            "foo/bar" => false,
            "foo/bar/baz" => true,
            "foo/bar/qux" => false,
            "foo/qux/bar" => false,
            "foo/qux/bar/baz" => true,
            "foo/qux/bar/qux" => false,
        ],
    ],
    "/a*" => [
        "pattern" => "/a*",
        "paths" => [
            "a1" => true,
            "a2/b" => true,
            "a2/c/d" => true,
        ],
    ],
    "/*a" => [
        "pattern" => "/*a",
        "paths" => [
            "1a" => true,
            "2a/b" => true,
            "2a/c/d" => true,
        ],
    ],
    "/*" => [
        "pattern" => "/*",
        "paths" => [
            "a" => true,
            "b/c" => false,
            "b/d/e" => false,
        ],
    ],
    "/a" => [
        "pattern" => "/a",
        "paths" => [
            "a/b" => true,
            "a/c/d" => true,
        ],
    ],
    "a*" => [
        "pattern" => "a*",
        "paths" => [
            "a1" => true,
            "a2/b" => true,
            "a2/c/d" => true,
            "b/a1" => true,
            "b/a2/b" => true,
            "b/a2/c/d" => true,
        ],
    ],
    "*a" => [
        "pattern" => "*a",
        "paths" => [
            "1a" => true,
            "2a/b" => true,
            "2a/c/d" => true,
            "b/1a" => true,
            "b/2a/b" => true,
            "b/2a/c/d" => true,
        ],
    ],
    "*" => [
        "pattern" => "*",
        "paths" => [
            "a" => true,
            "b/c" => true,
            "b/d/e" => true,
            "c/a" => true,
            "c/b/c" => true,
            "c/b/d/e" => true,
        ],
    ],
    "/a*/" => [
        "pattern" => "/a*/",
        "paths" => [
            "a1" => false,
            "a2/b" => true,
            "a2/c/d" => true,
        ],
    ],
    "/*a/" => [
        "pattern" => "/*a/",
        "paths" => [
            "1a" => false,
            "2a/b" => true,
            "2a/c/d" => true,
        ],
    ],
    "/*/" => [
        "pattern" => "/*/",
        "paths" => [
            "a" => false,
            "b/c" => true,
            "b/d/e" => true,
        ],
    ],
    "/a/" => [
        "pattern" => "/a/",
        "paths" => [
            "a" => false,
            "a/b" => true,
            "a/c/d" => true,
        ],
    ],
    "a*/" => [
        "pattern" => "a*/",
        "paths" => [
            "a1" => false,
            "a2/b" => true,
            "a2/c/d" => true,
            "b/a1" => false,
            "b/a2/b" => true,
            "b/a2/c/d" => true,
        ],
    ],
    "*a/" => [
        "pattern" => "*a/",
        "paths" => [
            "1a" => false,
            "2a/b" => true,
            "2a/c/d" => true,
            "b/1a" => false,
            "b/2a/b" => true,
            "b/2a/c/d" => true,
        ],
    ],
    "*/" => [
        "pattern" => "*/",
        "paths" => [
            "a" => false,
            "b/c" => true,
            "b/d/e" => true,
            "c/a" => true,
            "c/b/c" => true,
            "c/b/d/e" => true,
        ],
    ],
];

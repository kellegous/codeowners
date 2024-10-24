# CODEOWNERS

This provides a parser and a set of matchers for
the [CDOEOWNERS file format](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners).

## Installation

via [composer](https://getcomposer.org/)

```bash
composer require kellegous/codeowners
```

## Usage

### SimpleMatcher

The [SimpleMatcher](src/SimpleMatcher.php) is a straight-forward implementation of a [RuleMatcher](src/RuleMatcher.php)
that can find the relevant pattern for a file path in `O(N)` time relative to the number of rules in the `CODEOWNERS`
file.

```php
$owners = Owners::fromFile('.github/CODEOWNERS');
$matcher = new SimpleMatcher($owners->getRules());
$rule = $matcher->match($relative_path);
```

### AutomataMatcher

The [AutomatMatcher](src/AutomataMatcher.php) is a more complex implementation of a [RuleMatcher](src/RuleMatcher.php)
that requires a bit more memory that the `SimpleMatcher` but is able to do matching in `O(log N)` time relative to the
number of rules in the `CODEOWNERS` file.

```php
$owners = Owners::fromFile('.github/CODEOWNERS');
$matcher = AutomataMatcher::build($owners->getRules());
$rule = $matcher->match($relative_path);
```

## TODO

[ ] Add comments to the structure
[ ] Complete phpdocs
[ ] Add error checking on `match` to normalize or reject impossible patterns (i.e. "" and "/...")
[ ] Explain how `AutomataMatcher` works in this file.

## Author(s)

- [Kelly Norton](https://github.com/kellegous)
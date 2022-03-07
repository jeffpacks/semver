# About
The VersionNumber class of this library represents a [SemVer 2.0.0](https://semver.org/) semi-compliant version number. The class offers methods for querying the version number it represents as well as methods for comparison and manipulation.

# Requirements
This library requires PHP 7.4 and jeffpacks/substractor.

# Installing
Run `composer require jeffpacks/semver` in your project's root directory and `use \jeffpacks\semver\VersionNumber` in your PHP script/class.

If you don't want to use Composer or its classloader and have downloaded this library, you may use the built-in classloader instead:
```php
<?php
require_once 'path/to/semver/autoload.php';
use jeffpacks\semver\VersionNumber;
```

# Code examples
```php
<?php
require_once '../vendor/autoload.php'; # Using the Composer classloader
use jeffpacks\semver\VersionNumber;

$alphaVersion = new VersionNumber('1.2.0-alpha.12');
$betaVersion = new VersionNumber('1.0.3-beta.3');
$stableVersion = new VersionNumber('1.2.3');

$stableVersion->isMajor(); # false
$stableVersion->isMinor(); # false
$stableVersion->isPatch(); # true
$stableVersion->isAlpha(); # false
$stableVersion->isBeta(); # false

$alphaVersion->isStable(); # false
$betaVersion->isStable(); # false
$stableVersion->isStable(); # true

$stableVersion->getMajor(); # 1
$stableVersion->getMinor(); # 2
$stableVersion->getPatch(); # 3
$stableVersion->getAux(); # null
$betaVersion->getPreReleaseNumber(); # 3
$alphaVersion->getPreReleaseNumber(); # 12

$stableVersion->isHigherThan('1.1.9'); # true
$alphaVersion->isHigherThan($betaVersion); # true
$stableVersion->isLowerThan('1.3.0'); # true
$stableVersion->isEqualTo($betaVersion); # false
$stableVersion->isEqualTo($betaVersion, VersionNumber::MAJOR | VersionNumber::PATCH); # true
$stableVersion->matches('1.*.3'); # true

$stableVersion->increment(); # 1.2.4
$betaVersion->increment(); # 1.0.3-beta.4
$stableVersion->decrement(VersionNumber::MINOR); # 1.1.4
$alphaVersion->setBeta(); # 1.2.0-beta.1
$alphaVersion->setBeta(5); # 1.2.0-beta.5
```

# Semantic Versioning compliance
This library supports some formats that are not covered by [SemVer 2.0.0](https://semver.org/), but lacks support for other esoteric formats that SemVer defines. The most important one is that while [SemVer 2.0.0](https://semver.org/) defines the version core MAJOR.MINOR.PATCH, MINOR and PATCH are optional in this library. You may work with version numbers consisting of a MAJOR segment only or a MAJOR.MINOR format. Furthermore, the library also supports a fourth optional segment known as the AUX segment. Use it for whatever floats your boat.

This library does not yet support the flexible pre-release segment format specified in SemVer 2.0, but is currently limited to the following format (Backus–Naur form grammar):
```
<pre-release> ::= <pre-release identifier> | <pre-release identifier> "." <positive numeric identifier>
<numeric identifier> ::= <positive digit> | <positive digit> <digits>
<digits> ::= <digit> | <digit> <digits>
<digit> ::= "0" | <positive digit>
<positive digit> ::= "1" | "2" | "3" | "4" | "5" | "6" | "7" | "8" | "9"
```
In short, `beta`, `beta.1`, `beta.25` etc are allowed pre-release segments, but not `beta.0`, `beta.1.13` etc. 

## Zero-major segment interpretation
A version number with a MAJOR segment set to `0` is interpreted by this library as a non-stable version, on par with an ALPHA or BETA version. Here are some of the allowed combinations:
- 0
- 0.0
- 0.0.0
- 0.0.0.0
- 0.0.0-alpha
- 0.0.0-alpha.1

Which of `0.0.0` and `0.0.0-alpha.1` is higher is not addressed by [SemVer 2.0.0](https://semver.org/), so the rule of thumb for this library is that a version number without a pre-release segment (`alpha.1` in this case) is always considered higher than a version number with a pre-release segment. In short, `0.0.0` > `0.0.0-beta.1` > `0.0.0-alpha.1`.

# Authors
* [Johan Fredrik Varen](https://github.com/JohanFredrikVaren)

# License
MIT License
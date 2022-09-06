# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.4.0] - 2022-09-06

### Added
- VersionRange::isEqualTo()
- VersionRange::isHigherThan()
- VersionRange::isLowerThan()

## [3.3.0] - 2022-06-18

### Added
- Support for caret ranges (VersionRange)
- VersionNumber::isValid()

## [3.2.0] - 2022-06-16

### Added
- Callback function parameter to VersionNumber::getSorter()

## [3.1.0] - 2022-06-16

### Added
- VersionNumber::getSorter()

## [3.0.0] - 2022-04-07
The breaking change in this release lies is the comparison between version numbers of different version core formats. The version numbers `1.0` and `1.0.0` have previously been regarded as equal, while this release considers `1.0` < `1.0.0` to be true. Please refer to README.md for more details concerning comparison.

### Changed
- Issue #2: Version core format comparison

### Added
- VersionNumber::sort()

### Fixed
- Issue #1: VersionNumber::isEqualTo() fails to compare pre-release numbers

## [2.1.0] - 2022-03-20

### Added
- Support for PHP 8.0

### Changed
- Upgraded to jeffpacks/substractor 2.0

## [2.0.1] - 2022-03-07

### Fixed
- VersionNumber::isHigherThan() fails to compare version numbers with different amounts of segments

## [2.0.0] - 22-03-03

### Removed
- VersionNumber::STANDARD_SEMVER_2_0_0
- VersionNumber::isValid()
- examples/ directory

### Fixed
- VersionNumber::__toString() repeats pre-release type identifier (alpha/beta) instead of appending pre-release number
- VersionNumber::decrement() removes pre-release segment
- VersionNumber::getLeastSignificantIdentifier() always returns VersionNumber::PRE when major number is zero
- VersionNumber::setAux() will cause an invalid version number if no patch segment exists
- VersionNumber::setPatch() will cause an invalid version number if no minor segment exists
- VersionNumber::increment() adds and increments specified segment if it doesn't exist in the version number already
- VersionNumber doesn't support version numbers with unnumbered pre-release segment (e.g. 1.0.0-beta)
- VersionNumber doesn't support single segment version number with pre-release segment (e.g. 1-beta.1)
- VersionNumber::increment(VersionNumber::MINOR) increments major segment instead of minor segment when major segment is zero

### Added
- VersionNumber::adjust()
- VersionNumber::hasPre()
- VersionNumber::hasSegment()
- VersionNumber::setSegment()
- Unit tests

## [1.0.1] - 2021-12-07

### Fixed
- VersionNumber::__toString() produces trailing dash and period when the major segment is zero

## [1.0.0] - 2021-10-12

### Added
- examples/example.php

### Changes
- Upgraded to jeffpacks/substractor 1.0

## [1.0.0-alpha.1] - 2020-03-12

### Added
- src/VersionNumber.php
- autoload.php
- changelog.md
- composer.json
- LICENSE.md
- README.md
- .gitignore
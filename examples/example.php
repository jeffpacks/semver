<?php
require_once '../vendor/autoload.php';
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
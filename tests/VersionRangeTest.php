<?php

namespace jeffpacks\semver\tests;

use jeffpacks\semver\VersionNumber;
use jeffpacks\semver\VersionRange;
use PHPUnit\Framework\TestCase;

class VersionRangeTest extends TestCase {

	public function testGetHighestMatch() {

		$range = new VersionRange('^2.1.2');

		$versionNumbers = [
			'1.0.0',
			'1.2.0',
			'2.0.0',
			'2.1.0',
			'2.1.1',
			'2.2.0',
			'3.0.0'
		];

		$this->assertEquals('2.2.0', $range->getHighestMatch($versionNumbers));

	}

	public function testGetVersionNumber() {

		$range = new VersionRange('^1.2.3');
		$this->assertInstanceof(VersionNumber::class, $range->getVersionNumber());
		$this->assertEquals('1.2.3', $range->getVersionNumber()->__toString());

	}

	public function testIsValid() {

		$this->assertTrue(VersionRange::isValid('^1.2.3'));
		$this->assertTrue(VersionRange::isValid('^1.2.3-alpha'));
		$this->assertTrue(VersionRange::isValid('^1.2.3-alpha.1'));

		$this->assertFalse(VersionRange::isValid('^ 1.2.3'));
		$this->assertFalse(VersionRange::isValid('1.2.3'));

	}

	public function testIsInRange() {

		$range = new VersionRange('^2.0');

		$this->assertTrue($range->isInRange('2.0'));
		$this->assertTrue($range->isInRange('2.0.0'));
		$this->assertTrue($range->isInRange('2.1.0'));

		$this->assertFalse($range->isInRange('2'));
		$this->assertFalse($range->isInRange('1.2.2'));
		$this->assertFalse($range->isInRange('3.0.0'));

	}
}
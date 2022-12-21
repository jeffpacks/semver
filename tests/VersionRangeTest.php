<?php

namespace jeffpacks\semver\tests;

use jeffpacks\semver\VersionRange;
use jeffpacks\semver\VersionNumber;
use jeffpacks\semver\exceptions\InvalidFormatException;
use PHPUnit\Framework\TestCase;

class VersionRangeTest extends TestCase {

	/**
	 * @throws InvalidFormatException
	 */
	public function testGetHighestMatch() {

		$range = new VersionRange('^2.1');

		$versionNumbers = [
			'3.0.0',
			'2.2.0',
			'1.0.0',
			'2.0.0',
			'1.2.0',
			'2.1.0',
			'2.1.1',
		];

		$this->assertEquals('2.2.0', $range->getHighestMatch($versionNumbers));
		$this->assertEquals('2.1.1', $range->getHighestMatch($versionNumbers, '2.2.0'));
		$this->assertEquals('2.2.0', $range->getHighestMatch($versionNumbers, new VersionNumber('3.0.0')));

	}

	public function testGetVersionNumber() {

		$range = new VersionRange('^1.2.3');
		$this->assertInstanceof(VersionNumber::class, $range->getVersionNumber());
		$this->assertEquals('1.2.3', $range->getVersionNumber()->__toString());

	}

	public function testIsEqual() {

		$range = new VersionRange('^1.5');
		$this->assertTrue($range->isEqualTo('^1.5'));
		$this->assertTrue($range->isEqualTo(new VersionNumber('1.5')));
		$this->assertTrue($range->isEqualTo(new VersionRange('^1.5')));
		$this->assertFalse($range->isEqualTo('^1.4'));
		$this->assertFalse($range->isEqualTo(new VersionNumber('1.4')));
		$this->assertFalse($range->isEqualTo(new VersionRange('^1.4')));

	}

	public function testIsHigherThan() {

		$range = new VersionRange('^1.5');
		$this->assertTrue($range->isHigherThan('^1.4'));
		$this->assertTrue($range->isHigherThan(new VersionNumber('1.4')));
		$this->assertTrue($range->isHigherThan(new VersionRange('^1.4')));
		$this->assertFalse($range->isHigherThan('^1.5'));
		$this->assertFalse($range->isHigherThan(new VersionNumber('1.6')));
		$this->assertFalse($range->isHigherThan(new VersionRange('^2.0')));

	}

	public function testIsLowerThan() {

		$range = new VersionRange('^1.5');
		$this->assertTrue($range->isLowerThan('^1.6'));
		$this->assertTrue($range->isLowerThan(new VersionNumber('2.0')));
		$this->assertTrue($range->isLowerThan(new VersionRange('^2.1')));
		$this->assertFalse($range->isLowerThan('^1.5'));
		$this->assertFalse($range->isLowerThan(new VersionNumber('1.4')));
		$this->assertFalse($range->isLowerThan(new VersionRange('^1.4')));

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
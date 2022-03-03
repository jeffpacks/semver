<?php

namespace jeffpacks\semver\tests;

use jeffpacks\semver\VersionNumber;
use PHPUnit\Framework\TestCase;

class VersionNumberTest extends TestCase {

	public function testIsStable() {

		$versionNumber = new VersionNumber('0');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.1.0');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.1.0.0');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.0-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.0-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.0.0-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('0.0.0-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0-alpha');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0-alpha.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1-beta.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0-beta');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0-beta.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0-beta');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0-beta.1');
		$this->assertFalse($versionNumber->isStable());

		$versionNumber = new VersionNumber('1');
		$this->assertTrue($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0');
		$this->assertTrue($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0');
		$this->assertTrue($versionNumber->isStable());

		$versionNumber = new VersionNumber('1.0.0.0');
		$this->assertTrue($versionNumber->isStable());

	}

	/**
	 * @throws \Exception
	 */
	public function testIncrement() {

		$versionNumber = new VersionNumber('1');
		$versionNumber->increment();
		$this->assertEquals('2', (string) $versionNumber);
		$versionNumber->increment(VersionNumber::MAJOR);
		$this->assertEquals('3', (string) $versionNumber);
		$versionNumber->increment(VersionNumber::MINOR);
		$this->assertEquals('3', (string) $versionNumber);
		$versionNumber->increment(VersionNumber::PATCH);
		$this->assertEquals('3', (string) $versionNumber);
		$versionNumber->increment(VersionNumber::AUX);
		$this->assertEquals('3', (string) $versionNumber);

		$versionNumber = new VersionNumber('0.0');
		$versionNumber->increment();
		$this->assertEquals('0.1', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0');
		$versionNumber->increment();
		$this->assertEquals('1.1', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0.0');
		$versionNumber->increment();
		$this->assertEquals('1.0.1', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0.0.0');
		$versionNumber->increment();
		$this->assertEquals('1.0.0.1', (string) $versionNumber);

	}

	public function testDecrement() {

		$versionNumber = new VersionNumber('0.0.1');
		$versionNumber->decrement();
		$this->assertEquals('0.0.0', (string) $versionNumber);
		$versionNumber->decrement();
		$this->assertEquals('0.0.0', (string) $versionNumber);

		$versionNumber = new VersionNumber('3.2.1');
		$versionNumber->decrement(VersionNumber::MAJOR);
		$this->assertEquals('2.2.1', (string) $versionNumber);
		$versionNumber->decrement(VersionNumber::MAJOR);
		$this->assertEquals('1.2.1', (string) $versionNumber);
		$versionNumber->decrement(VersionNumber::MAJOR);
		$this->assertEquals('0.2.1', (string) $versionNumber);
		$versionNumber->decrement(VersionNumber::MAJOR);
		$this->assertEquals('0.2.1', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0.0-alpha.2');
		$versionNumber->decrement();
		$this->assertEquals('1.0.0-alpha.1', (string) $versionNumber);
		$versionNumber->decrement();
		$this->assertEquals('1.0.0-alpha.1', (string) $versionNumber);

	}
}
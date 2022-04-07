<?php

namespace jeffpacks\semver\tests;

use Exception;
use jeffpacks\semver\VersionNumber;
use PHPUnit\Framework\TestCase;

class VersionNumberTest extends TestCase {

	public function testAdjust() {

		$versionNumber = new VersionNumber('0.0.1');

		$versionNumber->adjust(VersionNumber::MAJOR, 2);
		$this->assertEquals('2.0.1', (string) $versionNumber);

		$versionNumber->adjust(VersionNumber::MAJOR, -3);
		$this->assertEquals('0.0.1', (string) $versionNumber);

		$versionNumber->adjust(VersionNumber::AUX, 1);
		$this->assertEquals('0.0.1', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0.0-alpha');
		$versionNumber->adjust(VersionNumber::PRE, 1);
		$this->assertEquals('1.0.0-alpha', (string) $versionNumber);

		$versionNumber = new VersionNumber('1.0.0-alpha.1');
		$versionNumber->adjust(VersionNumber::PRE, 1);
		$this->assertEquals('1.0.0-alpha.2', (string) $versionNumber);

		$versionNumber->adjust(VersionNumber::PRE, -2);
		$this->assertEquals('1.0.0-alpha.1', (string) $versionNumber);

	}

	/**
	 * @throws Exception
	 */
	public function testCompare() {

		$versionNumber = new VersionNumber('0.1.0');

		$this->assertEquals(0, $versionNumber->compare(new VersionNumber('0.1.0')));
		$this->assertEquals(0, $versionNumber->compare('0.1.0'));
		$this->assertEquals(1, $versionNumber->compare('0.1'));
		$this->assertEquals(1, $versionNumber->compare('0.1-beta'));

	}

	/**
	 * @throws Exception
	 */
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

	/**
	 * @throws Exception
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

	/**
	 * @throws Exception
	 */
	public function testIsEqualTo() {

		$versionNumber = new VersionNumber('0.1.0');
		$this->assertTrue($versionNumber->isEqualTo('0.1.0'));
		$this->assertFalse($versionNumber->isEqualTo('0.1'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0.0'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0-alpha'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0-beta.1'));

		$versionNumber = new VersionNumber('1.1.0');
		$this->assertTrue($versionNumber->isEqualTo('1.1.0'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-alpha'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-beta'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-alpha.1'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-beta.1'));

		$versionNumber = new VersionNumber('1.0.0-alpha.1');
		$this->assertTrue($versionNumber->isEqualTo('1.0.0-alpha.1'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0-alpha'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0-beta'));
		$this->assertFalse($versionNumber->isEqualTo('0.1.0-alpha.2'));

		$versionNumber = new VersionNumber('1.1.0-alpha.2');
		$this->assertTrue($versionNumber->isEqualTo('1.1.0-alpha.2'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-alpha.3'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-beta'));
		$this->assertFalse($versionNumber->isEqualTo('1.1.0-beta.1'));

	}

	/**
	 * @throws Exception
	 */
	public function testIsHigherThan() {

		$versionNumber = new VersionNumber('0.1.0');
		$this->assertTrue($versionNumber->isHigherThan('0.1'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-alpha'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-beta'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-alpha.1'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-beta.1'));

		$versionNumber = new VersionNumber('1.1.0');
		$this->assertTrue($versionNumber->isHigherThan('1.1'));
		$this->assertTrue($versionNumber->isHigherThan('1.1.0-alpha'));
		$this->assertTrue($versionNumber->isHigherThan('1.1.0-beta'));
		$this->assertTrue($versionNumber->isHigherThan('1.1.0-alpha.1'));
		$this->assertTrue($versionNumber->isHigherThan('1.1.0-beta.1'));

		$versionNumber = new VersionNumber('0.1.0-beta.1');
		$this->assertTrue($versionNumber->isHigherThan('0.1-alpha'));
		$this->assertTrue($versionNumber->isHigherThan('0.1-beta'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-alpha'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-beta'));
		$this->assertTrue($versionNumber->isHigherThan('0.1.0-alpha.2'));

		$versionNumber = new VersionNumber('1.1.0-alpha.2');
		$this->assertTrue($versionNumber->isHigherThan('0.1-alpha'));
		$this->assertTrue($versionNumber->isHigherThan('0.1-beta'));
		$this->assertFalse($versionNumber->isHigherThan('1.1.0-alpha.3'));
		$this->assertFalse($versionNumber->isHigherThan('1.1.0-beta'));
		$this->assertFalse($versionNumber->isHigherThan('1.1.0-beta.1'));

	}

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

	public function testMatches() {

		$versionNumber = new VersionNumber('1.2.13');

		$this->assertTrue($versionNumber->matches('*.*.*'));
		$this->assertTrue($versionNumber->matches('*.*.13'));
		$this->assertTrue($versionNumber->matches('*.2.*'));
		$this->assertTrue($versionNumber->matches('*.2.13'));
		$this->assertTrue($versionNumber->matches('1.*.*'));
		$this->assertTrue($versionNumber->matches('1.2.*'));
		$this->assertTrue($versionNumber->matches('1.2.13'));

		$this->assertTrue($versionNumber->matches('?.?.??'));
		$this->assertTrue($versionNumber->matches('?.?.?3'));
		$this->assertTrue($versionNumber->matches('?.?.1?'));
		$this->assertTrue($versionNumber->matches('?.?.13'));

		$this->assertTrue($versionNumber->matches('?.2.??'));
		$this->assertTrue($versionNumber->matches('?.2.?3'));
		$this->assertTrue($versionNumber->matches('?.2.1?'));
		$this->assertTrue($versionNumber->matches('?.2.13'));

		$this->assertTrue($versionNumber->matches('1.?.??'));
		$this->assertTrue($versionNumber->matches('1.?.?3'));
		$this->assertTrue($versionNumber->matches('1.?.1?'));
		$this->assertTrue($versionNumber->matches('1.?.13'));

	}
}
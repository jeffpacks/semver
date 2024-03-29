<?php

namespace jeffpacks\semver\tests;

use jeffpacks\semver\VersionNumber;
use jeffpacks\semver\exceptions\InvalidFormatException;
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

	public function testCompare() {

		$versionNumber = new VersionNumber('0.1.0');

		$this->assertEquals(0, $versionNumber->compare(new VersionNumber('0.1.0')));
		$this->assertEquals(0, $versionNumber->compare('0.1.0'));
		$this->assertEquals(1, $versionNumber->compare('0.1'));
		$this->assertEquals(1, $versionNumber->compare('0.1-beta'));

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

	public function testIsValid() {

		$this->assertTrue(VersionNumber::isValid('0'));
		$this->assertTrue(VersionNumber::isValid('0-alpha'));
		$this->assertTrue(VersionNumber::isValid('0-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('0-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('0-beta'));
		$this->assertTrue(VersionNumber::isValid('0-beta.0'));
		$this->assertTrue(VersionNumber::isValid('0-beta.1'));

		$this->assertTrue(VersionNumber::isValid('1'));
		$this->assertTrue(VersionNumber::isValid('1-alpha'));
		$this->assertTrue(VersionNumber::isValid('1-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('1-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('1-beta'));
		$this->assertTrue(VersionNumber::isValid('1-beta.0'));
		$this->assertTrue(VersionNumber::isValid('1-beta.1'));

		$this->assertTrue(VersionNumber::isValid('0.1'));
		$this->assertTrue(VersionNumber::isValid('0.1-alpha'));
		$this->assertTrue(VersionNumber::isValid('0.1-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('0.1-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('0.1-beta'));
		$this->assertTrue(VersionNumber::isValid('0.1-beta.0'));
		$this->assertTrue(VersionNumber::isValid('0.1-beta.1'));

		$this->assertTrue(VersionNumber::isValid('1.0'));
		$this->assertTrue(VersionNumber::isValid('1.0-alpha'));
		$this->assertTrue(VersionNumber::isValid('1.0-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('1.0-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('1.0-beta'));
		$this->assertTrue(VersionNumber::isValid('1.0-beta.0'));
		$this->assertTrue(VersionNumber::isValid('1.0-beta.1'));

		$this->assertTrue(VersionNumber::isValid('1.0.0'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-alpha'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-beta'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-beta.0'));
		$this->assertTrue(VersionNumber::isValid('1.0.0-beta.1'));

		$this->assertTrue(VersionNumber::isValid('1.0.0.1'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-alpha'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-alpha.0'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-alpha.1'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-beta'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-beta.0'));
		$this->assertTrue(VersionNumber::isValid('1.0.0.1-beta.1'));

		$this->assertFalse(VersionNumber::isValid(''));
		$this->assertFalse(VersionNumber::isValid('1.'));
		$this->assertFalse(VersionNumber::isValid('.'));
		$this->assertFalse(VersionNumber::isValid('.1'));
		$this->assertFalse(VersionNumber::isValid('a.b.c'));

	}

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

	public function testIsFirst() {

		$versionNumber = new VersionNumber('1.0.0.0-alpha.0');

		$this->assertTrue($versionNumber->isFirst());
		$this->assertTrue($versionNumber->isFirst(VersionNumber::MINOR));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::PATCH));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::AUX));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PRE));

		$versionNumber = new VersionNumber('1.0.0.0-alpha.1');
		$this->assertFalse($versionNumber->isFirst());
		$this->assertFalse($versionNumber->isFirst(VersionNumber::MINOR));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PATCH));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::AUX));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PRE));

		$versionNumber = new VersionNumber('1.0.0.1-alpha.0');
		$this->assertFalse($versionNumber->isFirst());
		$this->assertFalse($versionNumber->isFirst(VersionNumber::MINOR));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PATCH));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::AUX));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PRE));

		$versionNumber = new VersionNumber('1.0.1.0-alpha.0');
		$this->assertFalse($versionNumber->isFirst());
		$this->assertFalse($versionNumber->isFirst(VersionNumber::MINOR));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::PATCH));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::AUX));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PRE));

		$versionNumber = new VersionNumber('1.1.0.0-alpha.0');
		$this->assertFalse($versionNumber->isFirst());
		$this->assertTrue($versionNumber->isFirst(VersionNumber::MINOR));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::PATCH));
		$this->assertTrue($versionNumber->isFirst(VersionNumber::AUX));
		$this->assertFalse($versionNumber->isFirst(VersionNumber::PRE));

	}

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

	public function testSort() {

		$ascendingOrder = ['1.0', '1.0', '1.1', '1.2', '1.10', '2.0', '2.1', '2.1.0', '2.2', '2.10'];
		$descendingOrder = array_reverse($ascendingOrder);
		$randomOrder = $ascendingOrder;
		shuffle($randomOrder);

		$this->assertEquals($ascendingOrder, VersionNumber::sort($randomOrder));
		$this->assertEquals($descendingOrder, VersionNumber::sort($randomOrder, true));

		$ascendingOrder = array_map(fn(string $versionNumber) => new VersionNumber($versionNumber), $ascendingOrder);
		$descendingOrder = array_reverse($ascendingOrder);
		$randomOrder = $ascendingOrder;
		shuffle($randomOrder);

		$this->assertEquals($ascendingOrder, VersionNumber::sort($randomOrder));
		$this->assertEquals($descendingOrder, VersionNumber::sort($randomOrder, true));

	}

	/**
	 * @throws InvalidFormatException
	 */
	public function testGetSorter() {

		# Test with version number strings
		$ascendingOrder = ['1.0', '1.0', '1.1', '1.2', '1.10', '2.0', '2.1', '2.1.0', '2.2', '2.10'];
		$descendingOrder = array_reverse($ascendingOrder);
		$randomOrder = $ascendingOrder;

		shuffle($randomOrder);
		$ascendingSorter = VersionNumber::getSorter();
		usort($randomOrder, $ascendingSorter);
		$this->assertEquals($ascendingOrder, $randomOrder);

		shuffle($randomOrder);
		$descendingSorter = VersionNumber::getSorter(true);
		usort($randomOrder, $descendingSorter);
		$this->assertEquals($descendingOrder, $randomOrder);

		# Test with VersionNumber objects
		$ascendingOrder = array_map(fn(string $versionNumber) => new VersionNumber($versionNumber), $ascendingOrder);
		$descendingOrder = array_reverse($ascendingOrder);
		$randomOrder = $ascendingOrder;

		shuffle($randomOrder);
		$ascendingSorter = VersionNumber::getSorter();
		usort($randomOrder, $ascendingSorter);
		$this->assertEquals($ascendingOrder, $randomOrder);

		shuffle($randomOrder);
		$descendingSorter = VersionNumber::getSorter(true);
		usort($randomOrder, $descendingSorter);
		$this->assertEquals($descendingOrder, $randomOrder);

		# Test with version numbers stored in objects
		$ascendingOrder = array_map(
			fn(string $versionNumber) => (object) ['version' => $versionNumber],
			['1.0', '1.0', '1.1', '1.2', '1.10', '2.0', '2.1', '2.1.0', '2.2', '2.10']
		);
		$descendingOrder = array_reverse($ascendingOrder);
		$randomOrder = $ascendingOrder;

		$accessor = fn($object) => $object->version;

		shuffle($randomOrder);
		$ascendingSorter = VersionNumber::getSorter(false, $accessor);
		usort($randomOrder, $ascendingSorter);
		$this->assertEquals($ascendingOrder, $randomOrder);

		shuffle($randomOrder);
		$descendingSorter = VersionNumber::getSorter(true, $accessor);
		usort($randomOrder, $descendingSorter);
		$this->assertEquals($descendingOrder, $randomOrder);

	}

	public function testGetNext() {

		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext('2.0.0'));
		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext('2.0.0', VersionNumber::MAJOR));
		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext('2.0.0', VersionNumber::MINOR));
		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext('2.0.0', VersionNumber::PATCH));
		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext('2.0.0 2.0.1'));
		$this->assertInstanceOf(VersionNumber::class, VersionNumber::getNext(['2.0.0', '2.0.1']));

		$this->assertEquals('2', (string) VersionNumber::getNext('1'));
		$this->assertEquals('1.1', (string) VersionNumber::getNext('1.0'));
		$this->assertEquals('1.0.1', (string) VersionNumber::getNext('1.0.0'));

		$this->assertEquals('2', (string) VersionNumber::getNext('1', VersionNumber::MAJOR));
		$this->assertEquals('2.0', (string) VersionNumber::getNext('1.0', VersionNumber::MAJOR));
		$this->assertEquals('2.0.0', (string) VersionNumber::getNext('1.0.0', VersionNumber::MAJOR));

		$this->assertEquals('1', (string) VersionNumber::getNext('1', VersionNumber::MINOR));
		$this->assertEquals('1.1', (string) VersionNumber::getNext('1.0', VersionNumber::MINOR));
		$this->assertEquals('1.1.0', (string) VersionNumber::getNext('1.0.0', VersionNumber::MINOR));

		$this->assertEquals('1', (string) VersionNumber::getNext('1', VersionNumber::PATCH));
		$this->assertEquals('1.0', (string) VersionNumber::getNext('1.0', VersionNumber::PATCH));
		$this->assertEquals('1.0.1', (string) VersionNumber::getNext('1.0.0', VersionNumber::PATCH));

		$this->assertEquals('5', (string) VersionNumber::getNext('1 4 3 2'));
		$this->assertEquals('1.5', (string) VersionNumber::getNext('1.0 1.3 1.4 1.2')); # 1.1 is intentionally missing
		$this->assertEquals('1.0.5', (string) VersionNumber::getNext('1.0.0 1.0.3 1.0.4 1.0.2')); # 1.0.1 is intentionally missing

	}

	/**
	 * @throws InvalidFormatException
	 */
	public function testMin() {

		$versionA = new VersionNumber('1.0.0');
		$versionB = new VersionNumber('1.1.0');

		$this->assertEquals($versionA, $versionA->min($versionB));
		$this->assertEquals($versionA, $versionB->min($versionA));
		$this->assertEquals($versionA, $versionA->min('1.1.1'));

	}

	/**
	 * @throws InvalidFormatException
	 */
	public function testMax() {

		$versionA = new VersionNumber('1.0.0');
		$versionB = new VersionNumber('1.1.0');

		$this->assertEquals($versionB, $versionA->max($versionB));
		$this->assertEquals($versionB, $versionB->max($versionA));
		$this->assertEquals($versionB, $versionB->max('0.1.0'));

	}

}
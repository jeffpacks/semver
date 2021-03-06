<?php

namespace jeffpacks\semver;

use Error;
use jeffpacks\semver\exceptions\InvalidFormatException;
use jeffpacks\substractor\Substractor;

/**
 * Represents a version number range, into which a version number may fall inside or outside.
 */
class VersionRange {

	private VersionNumber $versionNumber;

	/**
	 * VersionRange constructor.
	 *
	 * @param string $range
	 * @throws InvalidFormatException
	 */
	public function __construct(string $range) {

		if (!self::isValid($range)) {
			throw new InvalidFormatException($range);
		}

		$parts = Substractor::macros($range, '^{tuple}');

		$this->versionNumber = new VersionNumber($parts['tuple']);

	}

	/**
	 * Provides the highest of a given set of version numbers that falls within this range.
	 *
	 * @param string[]|VersionNumber[] $versionNumbers
	 * @return string|VersionNumber|null
	 */
	public function getHighestMatch(array $versionNumbers)  {

		foreach (VersionNumber::sort($versionNumbers, true) as $versionNumber) {
			if ($this->isInRange($versionNumber)) {
				try {
					return $versionNumber;
				} catch (InvalidFormatException $e) {
					throw new Error('Internal error. This is a bug.', 0, $e);
				}
			}
		}

		return null;

	}

	/**
	 * Provides the version number that is the basis for this range.
	 *
	 * @return VersionNumber
	 */
	public function getVersionNumber(): VersionNumber {
		return $this->versionNumber;
	}

	/**
	 * Indicates whether a given version number is within this range.
	 *
	 * @param string|VersionNumber $versionNumber
	 * @return bool
	 */
	public function isInRange($versionNumber): bool {

		try {
			$versionNumber = $versionNumber instanceof VersionNumber ? $versionNumber : new VersionNumber($versionNumber);
		} catch (InvalidFormatException $e) {
			return false;
		}

		return
			!$versionNumber->isHigherThan($this->versionNumber, VersionNumber::MAJOR) &&
			!$versionNumber->isLowerThan($this->versionNumber);
	}

	/**
	 * Indicates whether a given value represents a valid version number range.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function isValid($value): bool {

		if ($value instanceof VersionRange) {
			return true;
		}

		if (!is_string($value)) {
			return false;
		}

		if ($parts = Substractor::macros($value, '^{tuple}')) {
			return VersionNumber::isValid($parts['tuple']);
		}

		return false;

	}

}
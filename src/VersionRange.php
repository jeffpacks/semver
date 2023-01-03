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

		$this->versionNumber = new VersionNumber(str_replace('^', '', $range));

	}

	/**
	 * Provides the highest of a given set of version numbers that falls within this range.
	 *
	 * @param string[]|VersionNumber[] $versionNumbers A set of version numbers from whom to find the highest one that matches this range.
	 * @param string|VersionNumber|null $below If passed, the highest version number that is below this version number is returned.
	 * @return string|VersionNumber|null
	 * @throws InvalidFormatException
	 */
	public function getHighestMatch(array $versionNumbers, $below = null)  {

		foreach (VersionNumber::sort($versionNumbers, true) as $versionNumber) {
			if ($this->isInRange($versionNumber)) {
				if ($below) {
					$below = $below instanceof VersionNumber ? $below : new VersionNumber($below);
					if ($below->isHigherThan($versionNumber)) {
						return $versionNumber;
					}
				} else {
					return $versionNumber;
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
	 * Indicates whether the threshold of this version range is equal to another given version number or version range.
	 *
	 * @param VersionRange|VersionNumber|string $version The other version range or number to compare this against.
	 * @param int|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 */
	public function isEqualTo($version, ?int $segment = null): bool {

		if (is_string($version) && VersionRange::isValid($version)) {
			try {
				$version = new VersionRange($version);
				return $this->getVersionNumber()->isEqualTo($version->getVersionNumber(), $segment);
			} catch (InvalidFormatException $e) {
				throw new Error('You found a bug', 0, $e);
			}
		}

		if (VersionNumber::isValid($version)) {
			return $this->getVersionNumber()->isEqualTo($version, $segment);
		}

		if ($version instanceof VersionRange) {
			return $this->getVersionNumber()->isEqualTo($version->getVersionNumber(), $segment);
		}

		return false;

	}

	/**
	 * Indicates whether this version range has a higher threshold than another given version number or version range.
	 *
	 * @param VersionRange|VersionNumber|string $version The other version range or number to compare this against.
	 * @param int|string|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 */
	public function isHigherThan($version, $segment = null): bool {

		if (is_string($version) && VersionRange::isValid($version)) {
			try {
				$version = new VersionRange($version);
				return $this->getVersionNumber()->isHigherThan($version->getVersionNumber(), $segment);
			} catch (InvalidFormatException $e) {
				throw new Error('You found a bug', 0, $e);
			}
		}

		if (VersionNumber::isValid($version)) {
			return $this->getVersionNumber()->isHigherThan($version, $segment);
		}

		if ($version instanceof VersionRange) {
			return $this->getVersionNumber()->isHigherThan($version->getVersionNumber(), $segment);
		}

		return false;

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
	 * Indicates whether this version range has a lower threshold than another given version number or version range.
	 *
	 * @param VersionRange|VersionNumber|string $version The other version range or number to compare this against.
	 * @param int|string|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 */
	public function isLowerThan($version, $segment = null): bool {

		if (is_string($version) && VersionRange::isValid($version)) {
			try {
				$version = new VersionRange($version);
				return $this->getVersionNumber()->isLowerThan($version->getVersionNumber(), $segment);
			} catch (InvalidFormatException $e) {
				throw new Error('You found a bug', 0, $e);
			}
		}

		if (VersionNumber::isValid($version)) {
			return $this->getVersionNumber()->isLowerThan($version, $segment);
		}

		if ($version instanceof VersionRange) {
			return $this->getVersionNumber()->isLowerThan($version->getVersionNumber(), $segment);
		}

		return false;

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

	/**
	 * Provides a string representation of this version range.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return "^{$this->getVersionNumber()}";
	}
}
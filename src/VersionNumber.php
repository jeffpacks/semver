<?php

namespace jeffpacks\semver;

use Exception;
use jeffpacks\substractor\Substractor;

/**
 * Represents a SemVer 2.0.0 semi-compliant version number.
 */
class VersionNumber {

	private int $major = 0;
	private ?int $minor = null;
	private ?int $patch = null;
	private ?int $aux = null;
	private ?string $preReleaseType = null;
	private ?int $preReleaseNumber = null;

	# Version standards
	const STANDARD_SEMVER_2_0_0 = 1;

	# Pre-release segment types
	const ALPHA = 'alpha';
	const BETA = 'beta';

	# Segment identifiers
	const MAJOR = 1;
	const MINOR = 2;
	const PATCH = 4;
	const AUX = 8;
	const PRE = 16;

	/**
	 * VersionNumber constructor.
	 *
	 * @param string|null $versionString A version number representation such as "4.2.1-beta.2".
	 * @throws Exception
	 */
	public function __construct(?string $versionString = null) {

		if (is_string($versionString) || $versionString instanceof VersionNumber) {
			$this->parseString($versionString);
		}

	}

	/**
	 * Adjusts a given segment with a specific delta value.
	 *
	 * This method will not affect the number of segments in this version number.
	 *
	 * @param int $segment VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE.
	 * @param int $delta A negative integer to decrease the segment, a positive integer to increase the segment.
	 * @return VersionNumber This instance
	 */
	public function adjust(int $segment, int $delta): VersionNumber {

		if (!$this->hasSegment($segment)) {
			return $this;
		}

		switch ($segment) {
			case $this::MAJOR:
				$this->major = ($this->major + $delta) >= 0 ? $this->major + $delta : 0;
				break;
			case $this::MINOR:
				$this->minor = ($this->minor + $delta) >= 0 ? $this->minor + $delta : 0;
				break;
			case $this::PATCH:
				$this->patch = ($this->patch + $delta) >= 0 ? $this->patch + $delta : 0;
				break;
			case $this::AUX:
				$this->aux = ($this->aux + $delta) >= 0 ? $this->aux + $delta : 0;
				break;
			case $this::PRE:
				if (is_int($this->preReleaseNumber)) {
					$this->preReleaseNumber = ($this->preReleaseNumber + $delta) >= 1 ? $this->preReleaseNumber + $delta : 1;
				}
				break;
		}

		return $this;

	}

	/**
	 * Compares this version number with another version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this version number against.
	 * @param int|null $segment Which segment(s) to compare, null for all.
	 * @return int 1 if this version is higher, -1 if this version is lower, 0 if they're equal
	 * @throws Exception
	 */
	public function compare($version, ?int $segment = null): int {

		if ($this->isEqualTo($version, $segment)) {
			return 0;
		}

		return $this->isHigherThan($version, $segment) ? 1 : -1;

	}

	/**
	 * Decrements the current version number with the value of 1.
	 *
	 * This method will – unlike VersionNumber::increment() – only affect the specified (or least significant) segment
	 * and will never affect the number of segments in this version number.
	 *
	 * @param int|null $segment VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE, null for the least significant.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function decrement(?int $segment = null): VersionNumber {
		return $this->adjust($segment ?? $this->getLeastSignificantIdentifier(), -1);
	}

	/**
	 * Provides the auxiliary segment, if any.
	 *
	 * @return int|null
	 */
	public function getAux(): ?int {
		return $this->aux;
	}

	/**
	 * Provides a combination of all the currently active segment identifiers of this version number.
	 *
	 * @return int
	 */
	public function getCombinedIdentifier(): int {

		$combination = 0;

		if ($this->hasMajor()) {
			$combination = $combination | self::MAJOR;
		}

		if ($this->hasMinor()) {
			$combination = $combination | self::MINOR;
		}

		if ($this->hasPatch()) {
			$combination = $combination | self::PATCH;
		}

		if ($this->hasAux()) {
			$combination = $combination | self::AUX;
		}

		if (!$this->isStable()) {
			$combination = $combination | self::PRE;
		}

		return $combination;

	}

	/**
	 * Provides the identifier of the segment that is considered to be least
	 * significant in the current version number.
	 *
	 * @return int|null
	 */
	public function getLeastSignificantIdentifier(): ?int {

		if ($this->hasPre()) {
			return self::PRE;
		}

		if ($this->hasAux()) {
			return self::AUX;
		}

		if ($this->hasPatch()) {
			return self::PATCH;
		}

		if ($this->hasMinor()) {
			return self::MINOR;
		}

		if ($this->hasMajor()) {
			return self::MAJOR;
		}

		return null;

	}

	/**
	 * Provides the major segment of the current version number.
	 *
	 * @return int
	 */
	public function getMajor(): int {
		return $this->major;
	}

	/**
	 * Provides the minor segment of the current version number.
	 *
	 * @return int|null
	 */
	public function getMinor(): ?int {
		return $this->minor;
	}

	/**
	 * Provides the patch segment of the current version number.
	 *
	 * @return int|null
	 */
	public function getPatch(): ?int {
		return $this->patch;
	}

	/**
	 * Provides the major segment of the current version number.
	 *
	 * @return int|null
	 */
	public function getPreReleaseNumber(): ?int {
		return $this->preReleaseNumber;
	}

	/**
	 * Provides the pre-release type of this version number.
	 *
	 * @return string|null 'alpha', 'beta' or null
	 */
	public function getPreReleaseType(): ?string {
		return $this->preReleaseType;
	}

	/**
	 * Indicates whether the current version number has an aux segment.
	 *
	 * @return boolean
	 */
	public function hasAux(): bool {
		return $this->aux !== null;
	}

	/**
	 * Indicates whether the current version number has a major segment.
	 *
	 * @return boolean
	 */
	public function hasMajor(): bool {
		return $this->major !== null;
	}

	/**
	 * Indicates whether the current version number has a minor segment.
	 *
	 * @return boolean
	 */
	public function hasMinor(): bool {
		return $this->minor !== null;
	}

	/**
	 * Indicates whether the current version number has a patch segment.
	 *
	 * @return boolean
	 */
	public function hasPatch(): bool {
		return $this->patch !== null;
	}

	/**
	 * Indicates whether the current version number has a pre-release segment.
	 *
	 * @return boolean
	 */
	public function hasPre(): bool {
		return $this->preReleaseType !== null;
	}

	/**
	 * Indicates whether this version number has a specific segment.
	 *
	 * @param int $segment VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE.
	 * @return bool
	 */
	public function hasSegment(int $segment): bool {

		switch ($segment) {
			case $this::MAJOR: return true;
			case $this::MINOR: return is_int($this->minor);
			case $this::PATCH: return is_int($this->patch);
			case $this::AUX: return is_int($this->aux);
			case $this::PRE: return is_string($this->preReleaseType);
		}

		return false;

	}

	/**
	 * Increments the current version number with the value of 1.
	 *
	 * If a segment identifier is given, all lesser segments are reset to zero and pre-release segments are removed.
	 * Incrementing the MINOR segment of a version number 5.2.1-beta.2 yields the version number 5.3.0. If no segment
	 * identifier is given, the least significant identifier will be incremented. Pre-release version numbers
	 * without a pre-release number (e.g. 5.2.1-alpha, 2.1.4-beta) remain unchanged.
	 *
	 * @param int|null $segment VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE, null for the least significant segment.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function increment(?int $segment = null): VersionNumber {

		$segment = $segment ?? $this->getLeastSignificantIdentifier();

		if ($this->hasSegment($segment)) {
			$this->adjust($segment, 1);

			# Zero out any lesser segments, but never a pre-release segment
			if ($segment < $this::PRE) {
				while ($segment < $this::AUX) {
					$segment *= 2;
					if (!$this->hasSegment($segment)) {
						break;
					}
					$this->setSegment($segment, 0);
				}

				# Remove any pre-release segment when the specified segment is major, minor, patch or aux
				$this->preReleaseNumber = null;
				$this->preReleaseType = null;
			}
		}

		return $this;

	}

	/**
	 * Indicates whether this version number signifies an ALPHA
	 * pre-release.
	 *
	 * @return boolean
	 */
	public function isAlpha(): bool {
		return $this->preReleaseType === self::ALPHA;
	}

	/**
	 * Indicates whether this version number represents a new aux version.
	 *
	 * @return bool
	 */
	public function isAux(): bool {
		return $this->aux != 0; # weak comparator intended to capture NULL and zero
	}

	/**
	 * Indicates whether this version number signifies a BETA
	 * pre-release.
	 *
	 * @return boolean
	 */
	public function isBeta(): bool {
		return $this->preReleaseType === self::BETA;
	}

	/**
	 * Indicates whether a given version number is equal to this version
	 * number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isEqualTo($version, ?int $segment = null): bool {

		$version = is_string($version) ? new VersionNumber($version) : $version;

		$segment = $segment ?: self::MAJOR | self::MINOR | self::PATCH | self::AUX | self::PRE;

		$isEqual = true;

		if ($segment & self::MAJOR && $this->getMajor() != $version->getMajor()) {
			$isEqual = false;
		}

		if ($segment & self::MINOR && $this->getMinor() != $version->getMinor()) {
			$isEqual = false;
		}

		if ($segment & self::PATCH && $this->getPatch() != $version->getPatch()) {
			$isEqual = false;
		}

		if ($segment & self::AUX && $this->getAux() != $version->getAux()) {
			$isEqual = false;
		}

		if ($segment & self::PRE) {
			if ($this->getPreReleaseType() != $version->getPreReleaseType()) {
				$isEqual = false;
			}

			if ($this->getPreReleaseNumber() != $version->getPreReleaseType()) {
				$isEqual = false;
			}
		}

		return $isEqual;

	}

	/**
	 * Indicates whether this version number is higher than another given
	 * version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|string|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isHigherThan($version, $segment = null): bool {

		$version = is_string($version) ? new VersionNumber($version) : $version;

		$segment = $segment ?: self::MAJOR | self::MINOR | self::PATCH | self::AUX | self::PRE;

		if ($segment & self::MAJOR) {
			if ($this->getMajor() > $version->getMajor()) {
				return true;
			}

			if ($this->getMajor() < $version->getMajor()) {
				return false;
			}

			if ($segment === self::MAJOR) {
				return false;
			}
		}

		if ($segment & self::MINOR) {
			if ($this->getMinor() > $version->getMinor()) {
				return true;
			}

			if ($this->getMinor() < $version->getMinor()) {
				return false;
			}

			if ($segment === self::MINOR) {
				return false;
			}
		}

		if ($segment & self::PATCH) {
			if ($this->getPatch() > $version->getPatch()) {
				return true;
			}

			if ($this->getPatch() < $version->getPatch()) {
				return false;
			}

			if ($segment === self::PATCH) {
				return false;
			}
		}

		if ($segment & self::AUX) {
			if ($this->getAux() > $version->getAux()) {
				return true;
			}

			if ($this->getAux() < $version->getAux()) {
				return false;
			}

			if ($segment === self::AUX) {
				return false;
			}
		}

		if ($segment & self::PRE) {
			if ($this->hasPre() && !$version->hasPre()) {
				return false;
			}

			if ($version->hasPre() && !$this->hasPre()) {
				return true;
			}

			if ($this->isBeta() && $version->isAlpha()) {
				return true;
			}

			if ($this->isAlpha() && $version->isBeta()) {
				return false;
			}

			if ($this->getPreReleaseNumber() > $version->getPreReleaseNumber()) {
				return true;
			}

			if ($this->getPreReleaseNumber() < $version->getPreReleaseNumber()) {
				return false;
			}

			if ($segment === self::PRE) {
				return false;
			}
		}

		return false;

	}

	/**
	 * Indicates whether this version number is lower than another given
	 * version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|string|null $segment Which segment(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isLowerThan($version, $segment = null): bool {
		return !$this->isEqualTo($version, $segment) && !$this->isHigherThan($version, $segment);
	}

	/**
	 * Indicates whether this version number represents a new major version.
	 *
	 * @return bool
	 */
	public function isMajor(): bool {
		return $this->minor == 0 && $this->patch == 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether this version number represents a new minor version.
	 *
	 * @return bool
	 */
	public function isMinor(): bool {
		return $this->minor != 0 && $this->patch == 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether this version number represents a new patch version.
	 *
	 * @return bool
	 */
	public function isPatch(): bool {
		return $this->patch != 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether this version number is considered stable.
	 *
	 * <p>
	 * This version number is considered to represent a stable version if it
	 * does not have a pre-release segment and the major segment number is
	 * greater than 0.
	 * </p>
	 *
	 * @return boolean
	 */
	public function isStable(): bool {
		return $this->getPreReleaseType() === null && $this->getMajor() > 0;
	}

	/**
	 * Indicates whether this version number matches a given pattern.
	 *
	 * @param string $pattern E.g. "1.0.1", "1.*", "2.?.?"
	 * @return bool
	 */
	public function matches(string $pattern): bool {
		return Substractor::matches($this->__toString(), $pattern);
	}

	/**
	 * Parses the integer value of a numeric string.
	 *
	 * @param string|int|null $number The number to parse.
	 * @return int|null
	 * @throws Exception If the parameter is not null or does not represent zero or a positive integer.
	 */
	private function parseNumber($number): ?int {

		if ($number === null) {
			return null;
		}

		if (is_string($number) || is_int($number)) {
			if (is_numeric($number) && (int)$number == $number) {
				$number = (int)$number;
				if ($number >= 0) {
					return $number;
				}
				throw new Exception('Parameter VersionNumber::parseNumber($number) can not be a negative integer');
			}
			throw new Exception('Parameter VersionNumber::parseNumber($number) must represent an integer');
		}
		throw new Exception('Parameter VersionNumber::parseNumber($number) must be a string, integer or null');

	}

	/**
	 * Parses a string containing a version number and sets its segments to be the segments of this version number.
	 *
	 * @param string $versionString A version string on the form "MAJOR[.MINOR.[PATCH[.AUX][-alpha.N]|[-beta.N]]]".
	 * @return void
	 * @throws Exception
	 */
	private function parseString(string $versionString): void {

		$versionString = strtolower($versionString);

		$supportedFormats = array(
			'{major}.{minor}.{patch}.{aux}-{preType}.{preNumber}',
			'{major}.{minor}.{patch}-{preType}.{preNumber}',
			'{major}.{minor}-{preType}.{preNumber}',
			'{major}-{preType}.{preNumber}',

			'{major}.{minor}.{patch}.{aux}-{preType}',
			'{major}.{minor}.{patch}-{preType}',
			'{major}.{minor}-{preType}',
			'{major}-{preType}',

			'{major}.{minor}.{patch}.{aux}',
			'{major}.{minor}.{patch}',
			'{major}.{minor}',
			'{major}'
		);

		$segments = Substractor::macros($versionString, $supportedFormats);

		foreach ($segments as $name => $value) {
			switch ($name) {
				case 'major':
					$this->setMajor($value);
					break;
				case 'minor':
					$this->setMinor($value);
					break;
				case 'patch':
					$this->setPatch($value);
					break;
				case 'aux':
					$this->setAux($value);
					break;
				case 'preType':
					$this->setPreReleaseType($value);
					break;
				case 'preNumber':
					$this->setPreReleaseNumber($value);
					break;
				default:
					throw new Exception('Invalid version format');
			}
		}

	}

	/**
	 * Sets the ALPHA segment of this version number.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setAlpha($value = null): VersionNumber {

		$this->preReleaseType = self::ALPHA;

		if ($value === null && $this->preReleaseNumber === null) {
			$value = 1;
		}

		$this->preReleaseNumber = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the value of the auxiliary segment.
	 *
	 * This method MAY affect the number of segments in this version number.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setAux($value): VersionNumber {

		if (!$this->hasPatch()) {
			$this->setPatch(0);
		}

		$this->aux = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the BETA segment of this version number.
	 *
	 * @param int|string|null $value The value of the segment.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setBeta($value = null): VersionNumber {

		$this->preReleaseType = self::BETA;

		if ($value === null && $this->preReleaseNumber === null) {
			$value = 1;
		}

		$this->preReleaseNumber = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the value of the major segment.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setMajor($value): VersionNumber {

		$this->major = $this->parseNumber($value) ?? 0;

		return $this;

	}

	/**
	 * Sets the value of the minor segment.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setMinor($value): VersionNumber {

		$this->minor = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the value of the patch segment.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setPatch($value): VersionNumber {

		if (!$this->hasMinor()) {
			$this->setMinor(0);
		}

		$this->patch = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the value of the pre-release segment.
	 *
	 * @param int|string|null $value The segment value.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setPreReleaseNumber($value): VersionNumber {

		$this->preReleaseNumber = $this->parseNumber($value);

		return $this;

	}

	/**
	 * Sets the pre-release segment type.
	 *
	 * @param string|null $type One of the VersionNumber::ALPHA|BETA constants or null for none.
	 * @return VersionNumber This instance
	 */
	private function setPreReleaseType(?string $type): VersionNumber {

		$this->preReleaseType = $type;

		return $this;

	}

	/**
	 * Sets a given segment of this version number to a specific value.
	 *
	 * @param int $segment
	 * @param int $value A non-negative integer.
	 * @return VersionNumber This instance
	 */
	public function setSegment(int $segment, int $value): VersionNumber {

		switch ($segment) {
			case $this::MAJOR:
				$this->major = $value;
				break;
			case $this::MINOR:
				$this->minor = $value;
				break;
			case $this::PATCH:
				$this->patch = $value;
				break;
			case $this::AUX:
				$this->aux = $value;
				break;
			case $this::PRE:
				if ($this->preReleaseNumber !== null) {
					$this->preReleaseNumber = $value;
					break;
				}
		}

		return $this;

	}

	/**
	 * Alters this version number to the closest stable state.
	 *
	 * <p>
	 * Examples of non-stable version numbers and their closest stable states:
	 * </p>
	 * <ul>
	 * <li>1.2.3-alpha.1 -> 1.2.3</li>
	 * <li>1.2.3-beta.2 -> 1.2.3</li>
	 * <li>0.2.3 -> 1.0.0</li>
	 * </ul>
	 *
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setStable(): VersionNumber {

		if ($this->isStable()) {
			return $this;
		}

		$this->preReleaseType = null;
		$this->preReleaseNumber = null;

		# If the major segment is zero, this version number is considered non-stable
		# (see https://semver.org/#spec-item-4) and the first stable version is always 1[.0[.0[.0]]]
		if ($this->getMajor() === 0) {
			$this->setMajor(1);
			if ($this->hasMinor()) {
				$this->setMinor(0);
			}
			if ($this->hasPatch()) {
				$this->setPatch(0);
			}
			if ($this->hasAux()) {
				$this->setAux(0);
			}
		}

		return $this;

	}

	/**
	 * Provides a string representation of this version number.
	 *
	 * @return string The version number
	 */
	public function __toString(): string {

		$string = '';

		if ($this->hasMajor()) {
			$string .= $this->getMajor();
		}

		if ($this->hasMinor()) {
			$string .= ".{$this->getMinor()}";
		}

		if ($this->hasPatch()) {
			$string .= ".{$this->getPatch()}";
		}

		if ($this->hasAux()) {
			$string .= ".{$this->getAux()}";
		}

		if ($this->getPreReleaseType()) {
			$string .= "-{$this->getPreReleaseType()}";
			if ($this->getPreReleaseNumber()) {
				$string .= ".{$this->getPreReleaseNumber()}";
			}
		}

		return $string;

	}

}

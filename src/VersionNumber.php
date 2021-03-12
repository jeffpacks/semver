<?php

namespace jeffpacks\semver;

use Closure;
use Exception;
use jeffpacks\substractor\Substractor;

/**
 * Represents a SemVer 2.0.0 compliant version number.
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

	# Pre-release element types
	const ALPHA = 'alpha';
	const BETA = 'beta';

	# Element identifiers
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
	 * Compares this version number with another version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this version number against.
	 * @param int|null $element Which element(s) to compare, null for all.
	 * @return int 1 if this version is higher, -1 if this version is lower, 0 if they're equal
	 * @throws Exception
	 */
	public function compare($version, ?int $element = null): int {

		if ($this->isEqualTo($version, $element)) {
			return 0;
		}

		return $this->isHigherThan($version, $element) ? 1 : -1;

	}

	/**
	 * Decrements the current version number with the value of 1.
	 *
	 * @param int|null $element VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE, null for the least significant.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function decrement($element = null): VersionNumber {

		$element = $element ?? $this->getLeastSignificantIdentifier();

		switch ($element) {
			case self::MAJOR:
				$this->major = $this->major == 0 ? $this->major: $this->major - 1; # weak comparator intended to capture NULL and zero
				break;

			case self::MINOR:
				$this->minor = $this->minor == 0 ? $this->minor: $this->minor - 1; # weak comparator intended to capture NULL and zero
				break;

			case self::PATCH:
				$this->patch = $this->patch == 0 ? $this->patch: $this->patch - 1; # weak comparator intended to capture NULL and zero
				break;

			case self::AUX:
				$this->aux = $this->aux == 0 ? $this->aux: $this->aux - 1; # weak comparator intended to capture NULL and zero
				break;

			case self::PRE:
				$this->preReleaseNumber = $this->preReleaseNumber == 0 ? $this->preReleaseNumber: $this->preReleaseNumber - 1; # weak comparator intended to capture NULL and zero
				break;
		}

		return $this;

	}

	/**
	 * Provides the auxiliary element, if any.
	 *
	 * @return int|null
	 */
	public function getAux(): ?int {
		return $this->aux;
	}

	/**
	 * Provides a combination of all the currently active element identifiers of this version number.
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
	 * Provides the identifier of the element that is considered to be least
	 * significant in the current version number.
	 *
	 * @return int|null
	 */
	public function getLeastSignificantIdentifier(): ?int {

		if (!$this->isStable()) {
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
	 * Provides the major element of the current version number.
	 *
	 * @return int
	 */
	public function getMajor(): int {
		return $this->major;
	}

	/**
	 * Provides the minor element of the current version number.
	 *
	 * @return int|null
	 */
	public function getMinor(): ?int {
		return $this->minor;
	}

	/**
	 * Provides the patch element of the current version number.
	 *
	 * @return int|null
	 */
	public function getPatch(): ?int {
		return $this->patch;
	}

	/**
	 * Provides the major element of the current version number.
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
	 * Indicates whether or not the current version number has a aux segment.
	 *
	 * @return boolean
	 */
	public function hasAux(): bool {
		return $this->aux !== null;
	}

	/**
	 * Indicates whether or not the current version number has a major segment.
	 *
	 * @return boolean
	 */
	public function hasMajor(): bool {
		return $this->major !== null;
	}

	/**
	 * Indicates whether or not the current version number has a minor segment.
	 *
	 * @return boolean
	 */
	public function hasMinor(): bool {
		return $this->minor !== null;
	}

	/**
	 * Indicates whether or not the current version number has a patch segment.
	 *
	 * @return boolean
	 */
	public function hasPatch(): bool {
		return $this->patch !== null;
	}

	/**
	 * Increments the current version number with the value of 1.
	 *
	 * <p>
	 * If an element identifier is given, all lesser elements are reset to zero
	 * with the exception of any pre-release element which is removed. This
	 * means that incrementing the MINOR element of a version number
	 * 5.2.1-beta.2 yields the version number 5.3.0.
	 * </p>
	 *
	 * @param int|null $element VersionNumber::MAJOR|MINOR|PATCH|AUX|PRE, null for the least significant.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function increment($element = null): VersionNumber {

		$element = $element ?? $this->getLeastSignificantIdentifier();

		switch ($element) {
			case self::MAJOR:
				$this->setMajor($this->getMajor() + 1);
				if ($this->hasMinor()) {
					$this->setMinor(0);
				}
				if ($this->hasPatch()) {
					$this->setPatch(0);
				}
				if ($this->hasAux()) {
					$this->setAux(0);
				}
				$this->setStable();
				break;

			case self::MINOR:
				$this->setMinor($this->getMinor() + 1);
				if ($this->hasPatch()) {
					$this->setPatch(0);
				}
				if ($this->hasAux()) {
					$this->setAux(0);
				}
				$this->setStable();
				break;

			case self::PATCH:
				$this->setPatch($this->getPatch() + 1);
				if ($this->hasAux()) {
					$this->setAux(0);
				}
				$this->setStable();
				break;

			case self::AUX:
				$this->setAux($this->getAux() + 1);
				$this->setStable();
				break;

			case self::PRE:
				if ($this->isStable()) {
					$this->setAlpha(1);
				} else {
					$this->setPreReleaseNumber($this->getPreReleaseNumber() + 1);
				}
				break;
		}

		return $this;

	}

	/**
	 * Indicates whether or not this version number signifies an ALPHA
	 * pre-release.
	 *
	 * @return boolean
	 */
	public function isAlpha(): bool {
		return $this->preReleaseType === self::ALPHA;
	}

	/**
	 * Indicates whether or not this version number represents a new aux version.
	 *
	 * @return bool
	 */
	public function isAux(): bool {
		return $this->aux != 0; # weak comparator intended to capture NULL and zero
	}

	/**
	 * Indicates whether or not this version number signifies a BETA
	 * pre-release.
	 *
	 * @return boolean
	 */
	public function isBeta(): bool {
		return $this->preReleaseType === self::BETA;
	}

	/**
	 * Indicates whether or not a given version number is equal to this version
	 * number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|null $element Which element(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isEqualTo($version, $element = null): bool {

		$version = is_string($version) ? new VersionNumber($version) : $version;

		$element = $element ? $element : self::MAJOR | self::MINOR | self::PATCH | self::AUX | self::PRE;

		$isEqual = true;

		if ($element & self::MAJOR && $this->getMajor() != $version->getMajor()) {
			$isEqual = false;
		}

		if ($element & self::MINOR && $this->getMinor() != $version->getMinor()) {
			$isEqual = false;
		}

		if ($element & self::PATCH && $this->getPatch() != $version->getPatch()) {
			$isEqual = false;
		}

		if ($element & self::AUX && $this->getAux() != $version->getAux()) {
			$isEqual = false;
		}

		if ($element & self::PRE) {
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
	 * Indicates whether or not this version number is higher than another given
	 * version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|string|null $element Which element(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isHigherThan($version, $element = null): bool {

		$version = is_string($version) ? new VersionNumber($version) : $version;

		$element = $element ? $element : self::MAJOR | self::MINOR | self::PATCH | self::AUX | self::PRE;

		if ($element & self::MAJOR) {
			if ($this->getMajor() > $version->getMajor()) {
				return true;
			}

			if ($this->getMajor() < $version->getMajor()) {
				return false;
			}

			if ($element === self::MAJOR) {
				return false;
			}
		}

		if ($element & self::MINOR) {
			if ($this->getMinor() > $version->getMinor()) {
				return true;
			}

			if ($this->getMinor() < $version->getMinor()) {
				return false;
			}

			if ($element === self::MINOR) {
				return false;
			}
		}

		if ($element & self::PATCH) {
			if ($this->getPatch() > $version->getPatch()) {
				return true;
			}

			if ($this->getPatch() < $version->getPatch()) {
				return false;
			}

			if ($element === self::PATCH) {
				return false;
			}
		}

		if ($element & self::AUX) {
			if ($this->getAux() > $version->getAux()) {
				return true;
			}

			if ($this->getAux() < $version->getAux()) {
				return false;
			}

			if ($element === self::AUX) {
				return false;
			}
		}

		if ($element & self::PRE) {
			if ($this->isStable()) {
				return !$version->isStable();
			}

			if ($version->isStable()) {
				return $this->isStable();
			}

			if ($this->isAlpha() && $version->isBeta()) {
				return true;
			}

			if ($this->isBeta() < $version->isAlpha()) {
				return true;
			}

			if ($this->getPreReleaseNumber() > $version->getPreReleaseNumber()) {
				return true;
			}

			if ($this->getPreReleaseNumber() < $version->getPreReleaseNumber()) {
				return false;
			}

			if ($element === self::PRE) {
				return false;
			}
		}

		return false;

	}

	/**
	 * Indicates whether or not this version number is lower than another given
	 * version number.
	 *
	 * @param VersionNumber|string $version The other version number to compare this against.
	 * @param int|string|null $element Which element(s) to compare, null for all.
	 * @return boolean
	 * @throws Exception
	 */
	public function isLowerThan($version, $element = null): bool {
		return !$this->isEqualTo($version, $element) && !$this->isHigherThan($version, $element);
	}

	/**
	 * Indicates whether or not this version number represents a new major version.
	 *
	 * @return bool
	 */
	public function isMajor(): bool {
		return $this->minor == 0 && $this->patch == 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether or not this version number represents a new minor version.
	 *
	 * @return bool
	 */
	public function isMinor(): bool {
		return $this->minor != 0 && $this->patch == 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether or not this version number represents a new patch version.
	 *
	 * @return bool
	 */
	public function isPatch(): bool {
		return $this->patch != 0 && $this->aux == 0; # weak comparators intended to capture NULL and zero
	}

	/**
	 * Indicates whether or not this version number is considered stable.
	 *
	 * <p>
	 * This version number is considered to represent a stable version if it
	 * does not have a pre-release element and the major element number is
	 * greater than 0.
	 * </p>
	 *
	 * @return boolean
	 */
	public function isStable(): bool {
		return $this->getPreReleaseType() === null && $this->getMajor() > 0;
	}

	/**
	 * Indicates whether or not this version number is considered to be valid by a given standard.
	 *
	 * @param int|Closure $standard One of the VersionNumber::STANDARD_* constants or a validator closure.
	 * @return boolean True if valid, false otherwise
	 */
	public function isValid($standard): bool {

		if (is_integer($standard)) {
			switch ($standard) {
				case self::STANDARD_SEMVER_2_0_0:
				default:
					$standard = function (VersionNumber $version) {
						return
							$version->hasMajor() &&
							$version->hasMinor() &&
							$version->hasPatch();
					};
					break;
			}
		}

		if ($standard instanceof Closure) {
			return $standard($this);
		}

		return false;

	}

	/**
	 * Indicates whether or not this version number matches a given pattern.
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
	 * Parses a string containing a version number and sets its elements to be the elements of this version number.
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
			'{major}.{minor}.{patch}.{aux}',
			'{major}.{minor}.{patch}',
			'{major}.{minor}',
			'{major}'
		);

		$elements = Substractor::extractMacros($versionString, $supportedFormats);

		foreach ($elements as $name => $value) {
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
	 * Sets this version number to be an ALPHA pre-release.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setAlpha($number = null): VersionNumber {

		$this->preReleaseType = self::ALPHA;

		if ($number === null && $this->preReleaseNumber === null) {
			$number = 1;
		}

		$this->preReleaseNumber = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets the number of the auxiliary element.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setAux($number): VersionNumber {

		$this->aux = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets this version number to be a BETA pre-release.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setBeta($number = null): VersionNumber {

		$this->preReleaseType = self::BETA;

		if ($number === null && $this->preReleaseNumber === null) {
			$number = 1;
		}

		$this->preReleaseNumber = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets the number of the major element.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setMajor($number): VersionNumber {

		$this->major = $this->parseNumber($number) ?? 0;

		return $this;

	}

	/**
	 * Sets the number of the minor element.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setMinor($number): VersionNumber {

		$this->minor = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets the number of the patch element.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setPatch($number): VersionNumber {

		$this->patch = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets the pre-release element number.
	 *
	 * @param int|string|null $number The value of the element number.
	 * @return VersionNumber This instance
	 * @throws Exception
	 */
	public function setPreReleaseNumber($number): VersionNumber {

		$this->preReleaseNumber = $this->parseNumber($number);

		return $this;

	}

	/**
	 * Sets the pre-release element type.
	 *
	 * @param string|null $type One of the VersionNumber::ALPHA|BETA constants or null for none.
	 * @return VersionNumber This instance
	 */
	private function setPreReleaseType(?string $type): VersionNumber {

		$this->preReleaseType = $type;

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

		// If the major number is zero, this version number is considered
		// non-stable (see https://semver.org/#spec-item-4) and the first stable
		// version is always 1.0.0.
		if ($this->getMajor() === 0) {
			$this->increment($this::MAJOR);
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

		if (!$this->isStable()) {
			$string .= "-{$this->getPreReleaseType()}.{$this->getPreReleaseNumber()}";
		}

		return $string;

	}

}

<?php

namespace jeffpacks\semver\exceptions;

use Exception;

/**
 * To be thrown when a value that is not a string on one of the formats supported by this library is given.
 */
class InvalidFormatException extends Exception {

	private string $value;

	/**
	 * InvalidFormatException constructor.
	 *
	 * @param string $value
	 */
	public function __construct(string $value) {
		$this->value = $value;
		parent::__construct("The value [$value] is not on a supported format");
	}

	/**
	 * Provides the value in question.
	 *
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
	}

}
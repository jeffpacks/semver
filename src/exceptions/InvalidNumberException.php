<?php

namespace jeffpacks\semver\exceptions;

use Exception;

/**
 * To be thrown when a value that is not a valid version number segment value is given.
 */
class InvalidNumberException extends Exception {

	private string $value;

	/**
	 * InvalidNumberException constructor.
	 *
	 * @param string $value
	 */
	public function __construct(string $value) {
		$this->value = $value;
		parent::__construct("The value [$value] is not on a valid version number segment");
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
<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/26/2016
 * Time: 9:23 AM
 */
namespace Common\Util;

class Validation {

	/**
	 * Checks for null, '', and empty arrays
	 * Casts objects to arrays before checking
	 * @param mixed $value
	 * @return bool
	 */
	public static function isEmpty($value) {
		$isEmpty = false;
		if(is_object($value)) {
			$value = (array) $value;
		}
		if($value === array() || is_null($value) || $value === '') {
			$isEmpty= true;
		}

		return $isEmpty;
	}

	/**
	 * Checks if a type is of a custom object or simple
	 * @param string $type
	 * @return bool
	 */
	public static function isCustomType(string $type) {
		$result = true;
		$simpleTypes = ['boolean', 'integer', 'double', 'string', 'array', 'object',
			'boolean[]', 'integer[]', 'double[]', 'string[]', '[]', 'object[]'];
		foreach($simpleTypes as $simpleType) {
			if($type == $simpleType) {
				$result = false;
				break;
			}
		}

		return $result;
	}
}
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
		$simpleTypes = ['NULL', 'boolean', 'integer', 'double', 'string', 'array', 'object',
			'boolean[]', 'integer[]', 'double[]', 'string[]', '[]', 'object[]'];
		foreach($simpleTypes as $simpleType) {
			if($type == $simpleType) {
				$result = false;
				break;
			}
		}

		return $result;
	}

    /**
     * Checks if the given source has a property
     * @param object $source
     * @param string $propertyName
     * @return bool
     */
    public static function hasProperty($source, string $propertyName) {
        if(!is_object($source)) {
            throw new \InvalidArgumentException('The source must be an object with accessible properties.');
        }

        $hasProperty = false;
        if(!Validation::isEmpty($propertyName) && isset($source->$propertyName)) {
            $hasProperty = true;
        }

        return $hasProperty;
    }

	/**
	 * @param mixed $value
	 * @return integer|null
	 */
	public static function getInteger($value) {
		$result = null;
		$intValue = filter_var($value, FILTER_VALIDATE_INT);
		if($intValue !== false) {
			$result = $intValue;
		}

		return $result;
	}

	/**
	 * @param mixed $value
	 * @return boolean|null
	 */
	public static function getBoolean($value) {
		$result = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		return $result;
	}
}
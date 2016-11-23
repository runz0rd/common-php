<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/26/2016
 * Time: 9:23 AM
 */
namespace Common\Util;

use Doctrine\Instantiator\Exception\InvalidArgumentException;

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
		$simpleTypes = ['any', 'NULL', 'boolean', 'bool', 'int', 'integer', 'double', 'string', 'array', 'object', 'boolean[]',
			'integer[]', 'double[]', 'string[]', '[]', 'object[]'];
		foreach($simpleTypes as $simpleType) {
			if($type == $simpleType) {
				$result = false;
				break;
			}
		}

		return $result;
	}

    /**
     * Checks if the given source has a property and returns its value
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
     * Returns a casted integer or the original value
	 * @param mixed $value
	 * @return integer|mixed
	 */
	public static function filterInteger($value) {
		$intValue = filter_var($value, FILTER_VALIDATE_INT);
		if($intValue !== false && is_string($value)) {
            $value = $intValue;
		}

		return $value;
	}

	/**
     * Returns a casted boolean or the original value
	 * @param mixed $value
	 * @return boolean|mixed
	 */
	public static function filterBoolean($value) {
		$boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if(!is_null($boolValue) && preg_match('/(true|false)/i', $value)) {
            $value = $boolValue;
        }

		return $value;
	}

    /**
     * Returns a casted float or the original value
     * This one isnt reliable, dont use
     * @param mixed $value
     * @return float|mixed
     */
    public static function filterFloat($value) {
        $floatValue = filter_var($value, FILTER_VALIDATE_FLOAT);
        if($floatValue !== false && is_string($value) && preg_match('[.|,]', $value)) {
            $value = $floatValue;
        }

        return $value;
    }

	public static function validateArray($value) {
		if(!is_array($value)) {
			throw new InvalidArgumentException('Value is not an array.');
		}
	}

	public static function validateBoolean($value) {
		if(!is_bool($value)) {
			throw new InvalidArgumentException('Value is not a boolean.');
		}
	}

	public static function validateDouble($value) {
		if(!is_double($value)) {
			throw new InvalidArgumentException('Value is not a double.');
		}
	}

	public static function validateInteger($value) {
		if(!is_integer($value)) {
			throw new InvalidArgumentException('Value is not an integer.');
		}
	}

	public static function validateObject($value) {
		if(!is_object($value)) {
			throw new InvalidArgumentException('Value is not an object.');
		}
	}

	public static function validateString($value) {
		if(!is_string($value)) {
			throw new InvalidArgumentException('Value is not a string.');
		}
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/30/2016
 * Time: 4:08 PM
 */

namespace Common\Util;


class Iteration {

    /**
     * Retrieves a value from an array or an object by name
     * Returns null if nothing found, or default  value if set
     * @param string $name
     * @param object|array $source
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function findValueByName($name, $source, $defaultValue = null) {
        if(!is_array($source) && !is_object($source)) {
            throw new \InvalidArgumentException('The source must be an array, or an object with accessible properties.');
        }
        $sourceValue = $defaultValue;
        if(is_array($source)) {
            $sourceValue = self::findValueInArray($source, $name, $defaultValue);
        }
        elseif($source instanceof \SimpleXMLElement) {
            $sourceValue = self::findValueInSimpleXmlElement($source, $name, $defaultValue);
        }
        elseif(is_object($source)) {
            $sourceValue = self::findValueInObject($source, $name, $defaultValue);
        }

        return $sourceValue;
    }

    public static function findValueInArray($source, $name, $defaultValue) {
        $value = $defaultValue;
        if(isset($source[$name]) && !Validation::isEmpty($value)) {
            $value = $source[$name];
        }
        return $value;
    }

    public static function findValueInObject($source, $name, $defaultValue) {
        $value = $defaultValue;
        if(isset($source->$name) && !Validation::isEmpty($source->$name)) {
            $value = $source->$name;
        }
        return $value;
    }

    public function findValueInSimpleXmlElement(\SimpleXMLElement $source, $name, $defaultValue) {
        $value = self::findValueInObject($source, $name, $defaultValue);
        if($value instanceof \SimpleXMLElement && $value->children()->count() === 0) {
            $value = (string) $value;
        }
        return $value;
    }

    /**
     * Assigns null to values considered empty
     * @see Validation::isEmpty
     * @param mixed $source
     * @return mixed
     */
    public static function nullifyEmpty($source) {
        if(Validation::isEmpty($source)) {
            $source = null;
        }
        if(is_object($source) || is_array($source)) {
            foreach($source as $key => $value) {
                $nullifiedValue = self::nullifyEmpty($value);
                $source = self::assign($source, $key, $nullifiedValue);
            }
        }

        return $source;
    }

    /**
     * Filters string values to their proper type, if possible
     * @param mixed $source
     * @return mixed
     */
    public static function typeFilter($source) {
        if(is_object($source) || is_array($source)) {
            foreach($source as $key => $value) {
                $filteredValue = self::typeFilter($value);
                $source = self::assign($source, $key, $filteredValue);
            }
        }
        else {
            $source = Validation::filterInteger($source);
            $source = Validation::filterBoolean($source);
        }

        return $source;
    }

    /**
     * Assign a value to an array or object
     * @param array|object $source
     * @param string $key
     * @param mixed $value
     * @return array|object
     */
    public static function assign($source, $key, $value) {
        if(!is_array($source) && !is_object($source)) {
            throw new \InvalidArgumentException('The source must be an array, or an object with accessible properties.');
        }
        if(is_object($source)) {
            $source->$key = $value;
        }
        elseif(is_array($source)) {
            $source[$key] = $value;
        }

        return $source;
    }

    /**
     * Returns the array value matched by needle or null on failure
     * @param array $haystackArray
     * @param string $needle
     * @return string|null
     */
    public static function strposArray(array $haystackArray, $needle) {
        $result = null;
        foreach($haystackArray as $haystack) {
            if(strpos($haystack, $needle) !== false) {
                $result = $haystack;
            }
        }

        return $result;
    }

    /**
     * @param array $haystackArray
     * @param string $regex
     * @return string|null
     */
    public static function regexArray(array $haystackArray, $regex) {
        $result = null;
        foreach($haystackArray as $haystack) {
            if(preg_match($regex, $haystack, $matches)) {
                $result = $haystack;
            }
        }

        return $result;
    }

    /**
     * Push a value into an array inside an object
     * $object->$key becomes an array if not already
     * @param object $object
     * @param string $key
     * @param mixed $value
     * @return object
     */
    public static function pushArrayValue($object, $key, $value) {
        if(!is_object($object)) {
            throw new \InvalidArgumentException('The source must be an object with accessible properties.');
        }
        if(!isset($object->$key) || !is_array($object->$key)) {
            $object->$key = array();
        }
        array_push($object->$key, $value);
    }
}
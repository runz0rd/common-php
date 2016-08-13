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
    public static function findValueByName(string $name, $source, $defaultValue = null) {
        if(!is_array($source) && !is_object($source)) {
            throw new \InvalidArgumentException('The source must be an array, or an object with accessible properties.');
        }
        $sourceValue = $defaultValue;
        foreach($source as $key => $value) {
            if($name == $key && !Validation::isEmpty($value)) {
                $sourceValue = $value;
                break;
            }
        }

        return $sourceValue;
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
        if(is_object($source) && is_array($source)) {
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
    public static function assign($source, string $key, $value) {
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
}
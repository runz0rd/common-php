<?php
/**
 * Created by PhpStorm.
 * User: milosh
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
     * @param array|object $source
     * @return array|object
     */
    public static function nullifyEmptyProperties($source) {
        foreach($source as $key => $value) {
            if(Validation::isEmpty($value)) {
                $value = null;
            }
            if(is_object($value) || is_array($value)) {
                $value = self::nullifyEmptyProperties($value);
            }
            $source->$key = $value;
        }

        return $source;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/20/2016
 * Time: 9:42 AM
 */

namespace Common\Validator;

interface IRule {

    /**
     * Used in your @rule annotation
     * Case insensitive
     * @return string
     */
    function getName();

    /**
     * Define your rule and have your value pass it
     * Should throw an Exception on failure
     * @param mixed $value
     * @throws \Throwable
     */
    function validate($value);
}
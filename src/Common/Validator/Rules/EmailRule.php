<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/20/2016
 * Time: 10:10 AM
 */

namespace Common\Validator\Rules;
use Common\Validator\IRule;
use Common\Validator\ModelValidatorException;

class EmailRule implements IRule {

    function getName() {
        return 'email';
    }

    function validate($value) {
        if(filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new ModelValidatorException('Value is not a valid email.');
        }
    }
}
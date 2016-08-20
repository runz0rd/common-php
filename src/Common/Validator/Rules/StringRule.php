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

class StringRule implements IRule {

    function getName() {
        return 'string';
    }

    function validate($value) {
        if(!is_string($value)) {
            throw new ModelValidatorException('Value is not a string.');
        }
    }
}
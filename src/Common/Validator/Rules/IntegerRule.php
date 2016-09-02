<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/20/2016
 * Time: 10:10 AM
 */

namespace Common\Validator\Rules;
use Common\Models\ModelProperty;
use Common\Util\Validation;
use Common\Validator\IRule;

class IntegerRule implements IRule {

    function getNames() {
        return ['int', 'integer'];
    }

    function validate(ModelProperty $property, array $params = []) {
        Validation::validateInteger($property->getPropertyValue());
    }
}
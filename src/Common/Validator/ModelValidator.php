<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/10/2016
 * Time: 4:09 PM
 */

namespace Common\Validator;
use Common\Models\ModelClass;
use Common\Models\ModelProperty;
use Common\Util\Validation;

class ModelValidator {

	/**
	 * @param object $object
	 * @param string $validationRequiredType
	 * @throws ModelValidatorException
	 * @throws \InvalidArgumentException
	 */
	public function validate($object, string $validationRequiredType = '') {
		if(!is_object($object)) {
			throw new \InvalidArgumentException('Invalid object supplied for validation.');
		}

		$modelClass = new ModelClass($object);
		foreach($modelClass->getProperties() as $property) {
			$this->validateProperty($property, $validationRequiredType);
		}
	}

	/**
	 * @param ModelProperty $property
	 * @param string $requiredType
	 */
	protected function validateProperty(ModelProperty $property, string $requiredType) {
		if($property->isRequired()) {
			$this->validateRequiredProperty($property, $requiredType);
		}
		$this->validatePropertyType($property, $requiredType);

		if($property->getType()->isModel()) {
		    $this->validateModelProperty($property->getPropertyValue(), $requiredType);
		}
	}

	/**
	 * @param array|object $propertyValue
	 * @param string $requiredType
	 */
	protected function validateModelProperty($propertyValue, string $requiredType) {
		if(!Validation::isEmpty($propertyValue)) {
            if(is_array($propertyValue)) {
                foreach($propertyValue as $value) {
                    $this->validate($value, $requiredType);
                }
            }
            if(is_object($propertyValue)) {
                $this->validate($propertyValue, $requiredType);
            }
		}
	}

	/**
	 * @param ModelProperty $property
	 * @param string $requiredType
	 * @throws ModelValidatorException
	 */
	protected function validatePropertyType(ModelProperty $property, string $requiredType) {
		$expectedType = $property->getType()->getActualType();
		$actualType = gettype($property->getPropertyValue());

		if(!$property->isRequired() && $expectedType != 'NULL' && $actualType != 'NULL') {
			$this->assertPropertyType($expectedType, $actualType, $property);
		}
		if($property->isRequired() && $expectedType != 'NULL' && array_search($requiredType, $property->getRequiredTypes()) !== false) {
			$this->assertPropertyType($expectedType, $actualType, $property);
		}
	}

	/**
	 * @param ModelProperty $property
	 * @param string $requiredType
	 * @throws ModelValidatorException
	 */
	protected function validateRequiredProperty(ModelProperty $property, string $requiredType) {
		$expectedRequired = $property->isRequired();
		$actualRequired = !Validation::isEmpty($property->getPropertyValue());

		foreach($property->getRequiredTypes() as $expectedRequiredType) {
			if($expectedRequiredType == $requiredType || $expectedRequiredType == '') {
				$this->assertRequiredProperty($expectedRequired, $actualRequired, $property);
			}
		}
	}

    /**
     * @param string $expected
     * @param string $actual
     * @param ModelProperty $propertyData
     * @throws ModelValidatorException
     */
	protected function assertPropertyType(string $expected, string $actual, ModelProperty $propertyData) {
		if($expected != $actual) {
			throw new ModelValidatorException('Expecting ' . $expected . ' type but got ' . $actual . ' while validating ' . $propertyData->getParentClassName() . '::' . $propertyData->getPropertyName());
		}
	}

	/**
	 * @param bool $expected
	 * @param bool $actual
	 * @param ModelProperty $propertyData
	 * @throws ModelValidatorException
	 */
	protected function assertRequiredProperty(bool $expected, bool $actual, ModelProperty $propertyData) {
		if($expected != $actual) {
			throw new ModelValidatorException('Required property ' . $propertyData->getParentClassName() . '::' . $propertyData->getPropertyName() . ' not set.');
		}
	}
}
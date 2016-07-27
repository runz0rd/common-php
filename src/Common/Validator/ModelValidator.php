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
	 * @var string
	 */
	protected $className;

	/**
	 * @var string
	 */
	protected $propertyName;

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
		$this->className = $modelClass->getClassName();

		foreach($modelClass->getProperties() as $property) {
			$this->propertyName = $property->getPropertyName();
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
			$this->validateCustomTypeValue($property, $requiredType);
		}
	}

	/**
	 * @param ModelProperty $property
	 * @param string $requiredType
	 */
	protected function validateCustomTypeValue(ModelProperty $property, string $requiredType) {
		$propertyValue = $property->getPropertyValue();
		if(!Validation::isEmpty($propertyValue)) {
			if(is_array($propertyValue)) {
				foreach ($propertyValue as $value) {
					$validator = new ModelValidator();
					$validator->validate($value, $requiredType);
				}
			}
			if(is_object($propertyValue)) {
				$validator = new ModelValidator();
				$validator->validate($propertyValue, $requiredType);
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

		if(!$property->isRequired() && $actualType != 'NULL') {
			$this->assertPropertyType($expectedType, $actualType);
		}
		if($property->isRequired() && array_search($requiredType, $property->getRequiredTypes()) !== false) {
			$this->assertPropertyType($expectedType, $actualType);
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
	 * @throws ModelValidatorException
	 */
	protected function assertPropertyType(string $expected, string $actual) {
		if($expected != $actual) {
			throw new ModelValidatorException('Expecting ' . $expected . ' type but got ' . $actual . ' while validating ' . $this->className . '::' . $this->propertyName);
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
			throw new ModelValidatorException('Required property ' . $propertyData->getClassName() . '::' . $propertyData->getPropertyName() . ' not set.');
		}
	}
}
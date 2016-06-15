<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/10/2016
 * Time: 4:09 PM
 */

namespace Common\Validator;
use Common\Models\ModelClassData;
use Common\Models\ModelPropertyData;
use Common\Mapper\ObjectMapper;

class ObjectValidator {

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
	 * @throws ObjectValidatorException
	 * @throws \InvalidArgumentException
	 */
	public function validate($object, string $validationRequiredType = '') {
		if(is_null($object)) {
			throw new \InvalidArgumentException('Invalid object(s) supplied for validation.');
		}
		$modelClassData = new ModelClassData($object);
		$this->className = $modelClassData->className;

		foreach($modelClassData->properties as $property) {
			$this->propertyName = $property->propertyName;
			$this->validateByType($property, $validationRequiredType);
		}
	}

	/**
	 * @param ModelPropertyData $property
	 * @param string $requiredType
	 */
	protected function validateByType(ModelPropertyData $property, string $requiredType) {
		switch($property->type) {
			case 'object':
			case 'array':
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'NULL':
				$this->validateSimpleType($property, $requiredType);
				break;
			default:
				$this->validateCustomType($property, $requiredType);
				break;
		}
	}

	/**
	 * @param ModelPropertyData $property
	 * @param string $requiredType
	 */
	protected function validateCustomType(ModelPropertyData $property, string $requiredType) {
		$this->validateRequired($property, $requiredType);

		if(!empty($property->getPropertyValue()) && strpos($property->type, '[]')) {
			foreach($property->getPropertyValue() as $value) {
				$this->validate($value);
			}
		}
		elseif(!empty($property->getPropertyValue())) {
			$this->validate($property->getPropertyValue());
		}
	}

	/**
	 * @param ModelPropertyData $property
	 * @param string $requiredType
	 */
	protected function validateSimpleType(ModelPropertyData $property, string $requiredType) {
		$this->validateRequired($property, $requiredType);
		$this->validateType($property);
	}

	/**
	 * @param ModelPropertyData $property
	 * @throws ObjectValidatorException
	 */
	protected function validateType(ModelPropertyData $property) {
		$expectedType = $property->type;
		$actualType = gettype($property->getPropertyValue());
		$this->assertType($expectedType, $actualType);
	}

	/**
	 * @param ModelPropertyData $property
	 * @param string $requiredType
	 * @throws ObjectValidatorException
	 */
	protected function validateRequired(ModelPropertyData $property, string $requiredType) {
		if($property->isRequired) {
			$expectedRequired = $property->isRequired;
			$actualRequired = !empty($property->getPropertyValue());
			foreach ($property->requiredTypes as $expectedRequiredType) {
				$this->assertRequired($expectedRequired, $actualRequired, $expectedRequiredType, $requiredType);
			}
		}
	}

	/**
	 * @param string $expected
	 * @param string $actual
	 * @throws ObjectValidatorException
	 */
	protected function assertType(string $expected, string $actual) {
		if($actual != 'NULL' && $expected != $actual) {
			throw new ObjectValidatorException('Expecting ' . $expected . ' type but got ' . $actual . ' while validating ' . $this->className . '::' . $this->propertyName);
		}
	}

	/**
	 * @param bool $expected
	 * @param bool $actual
	 * @param string $expectedType
	 * @param string $actualType
	 * @throws ObjectValidatorException
	 */
	protected function assertRequired(bool $expected, bool $actual, string $expectedType, string $actualType) {
		if($actualType == '' && ($expectedType == '' || $expectedType == $actualType) && $expected != $actual) {
			throw new ObjectValidatorException('Required property ' . $this->className . '::' . $this->propertyName . ' not set.');
		}
	}
}
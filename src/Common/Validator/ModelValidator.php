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
use Common\Util\Iteration;
use Common\Util\Validation;

class ModelValidator {

    /**
     * @var IRule[]
     */
    private $rules;

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
     * Load all the rule classes from the specified folder
     * @param string $location
     */
	public function useRules(string $location = __DIR__ . '\Rules') {
        foreach(glob($location . '\*.php') as $filename) {
            @require_once $filename;
            $className = basename($filename, ".php");
            $autoloaded = get_declared_classes();
            $class = Iteration::strposArray($autoloaded, $className);

            if(!is_null($class)) {
                /** @var IRule $rule */
                $rule = new $class;
                if ($rule instanceof IRule) {
                    $ruleName = strtolower($rule->getName());
                    $this->rules[$ruleName] = $rule;
                }
            }
        }
    }

    /**
     * @param IRule $rule
     */
    public function useRule(IRule $rule) {
        $ruleName = strtolower($rule->getName());
        $this->rules[$ruleName] = $rule;
    }

    /**
     * @param ModelProperty $property
     * @throws ModelValidatorException
     */
    protected function validateRules(ModelProperty $property) {
        if(!is_null($property->getPropertyValue()) && $property->getDocBlock()->hasAnnotation('rule')) {
            $definedRules = $property->getDocBlock()->getAnnotation('rule');
            foreach($definedRules as $definedRule) {
                $ruleName = strtolower($definedRule);
                if(!isset($this->rules[$ruleName])) {
                    continue;
                }
                $rule = $this->rules[strtolower($definedRule)];
                try {
                    $rule->validate($property->getPropertyValue());
                }
                catch(\Exception $ex) {
                    $message = 'Error while validating ' . $property->getParentClassName() . '::' . $property->getPropertyName() . '. ' . $ex->getMessage();
                    throw new ModelValidatorException($message);
                }
            }
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
        $this->validateRules($property);

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
     * @param ModelProperty $property
     * @throws ModelValidatorException
     */
	protected function assertPropertyType(string $expected, string $actual, ModelProperty $property) {
		if($expected != $actual) {
			throw new ModelValidatorException('Expecting ' . $expected . ' type but got ' . $actual . ' while validating ' . $property->getParentClassName() . '::' . $property->getPropertyName());
		}
	}

	/**
	 * @param bool $expected
	 * @param bool $actual
	 * @param ModelProperty $property
	 * @throws ModelValidatorException
	 */
	protected function assertRequiredProperty(bool $expected, bool $actual, ModelProperty $property) {
		if($expected != $actual) {
			throw new ModelValidatorException('Required property ' . $property->getParentClassName() . '::' . $property->getPropertyName() . ' not set.');
		}
	}
}
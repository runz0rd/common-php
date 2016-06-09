<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 3/29/2016
 * Time: 9:27 AM
 */

namespace Common\Mapper;
use ReflectionClass;

class ObjectMapper {

	/**
	 * @var bool
	 */
	protected $isStrict;

	/**
	 * ObjectMapper constructor.
	 * @param bool $isStrict
	 */
	public function __construct(bool $isStrict = false) {
		$this->isStrict = $isStrict;
	}

	/**
	 * @param object $sourceObject
	 * @param object $customObject
	 * @return object
	 * @throws MapperException
	 */
	public function map($sourceObject, $customObject) {
		if(empty($sourceObject) || empty($customObject)) {
			throw new MapperException('Invalid object(s) supplied for mapping.');
		}
		$objectClass = new ObjectClass($customObject);

		if($this->hasRoot($sourceObject, $objectClass->rootName)) {
			$this->validateSourceRoot($sourceObject, $objectClass->rootName);
			$sourceObject = $sourceObject->{$objectClass->rootName};
		}

		foreach($objectClass->properties as $property) {
			$sourcePropertyValue = $this->findObjectValue($property, $sourceObject);
			$this->validateType($property, $sourcePropertyValue);
			$this->validateRequired($property, $sourcePropertyValue);

			$mappedPropertyValue = $this->mapValueByType($property->type, $sourcePropertyValue);
			$property->setPropertyValue($customObject, $mappedPropertyValue);
		}

		return $customObject;
	}

	protected function hasRoot($sourceObject, string $rootName) {
		$hasRoot = false;
		if(!empty($rootName) && isset($sourceObject->$rootName)) {
			$hasRoot = true;
		}

		return $hasRoot;
	}

	/**
	 * @param object $customObject
	 * @return \stdClass
	 * @throws MapperException
	 */
	public function unmap($customObject) {
		if(empty($customObject)) {
			throw new MapperException('Invalid object supplied for unmapping.');
		}

		$objectClass = new ObjectClass($customObject);
		$unmappedObject = new \stdClass();
		foreach($objectClass->properties as $property) {
			$propertyKey = $property->name;
			$propertyValue = $property->getPropertyValue($customObject);
			if(empty($propertyValue)) {
				continue;
			}
			$unmappedObject->$propertyKey = $this->unmapValueByType($property->type, $propertyValue);
		}

		if(!empty($objectClass->rootName)) {
			$unmappedObject = $this->addRootElement($unmappedObject, $objectClass->rootName);
		}

		return $unmappedObject;
	}

	protected function addRootElement($object, string $rootName) {
		$newObject = new \stdClass();
		$newObject->$rootName = $object;

		return $newObject;
	}

	/**
	 * @param string $type
	 * @param mixed $value
	 * @return array|mixed|object
	 * @throws MapperException
	 */
	protected function mapValueByType(string $type, $value) {
		switch(gettype($value)) {
			case 'object':
				$mappedValue = $this->mapObjectValue($type, $value);
				break;
			case 'array':
				$mappedValue = $this->mapArrayValue($type, $value);
				break;
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'NULL':
				$mappedValue = $value;
				break;
			default:
				throw new MapperException('Invalid type ' . gettype($value) .' supplied for property.');
				break;
		}

		return $mappedValue;
	}

	/**
	 * @param string $type
	 * @param object $value
	 * @return object
	 */
	protected function mapObjectValue(string $type, $value) {
		$mappedValue = $value;
		if(self::isCustomType($type)) {
			$customTypeObject = new $type();
			$mappedValue = $this->map($value, $customTypeObject);
		}

		return $mappedValue;
	}

	/**
	 * @param string $type
	 * @param array $value
	 * @return array
	 * @throws MapperException
	 */
	protected function mapArrayValue(string $type, array $value) {
		$mappedValue = [];
		if(strpos($type, '[]')) {
			$type = rtrim($type, '[]');
		}
		foreach($value as $key => $val) {
			$mappedValue[$key] = $this->mapValueByType($type, $val);
		}

		return $mappedValue;
	}

	/**
	 * @param string $type
	 * @param mixed $value
	 * @return mixed
	 */
	protected function unmapValueByType(string $type, $value) {
		switch(gettype($value)) {
			case 'object':
				$unmappedValue = $this->unmapObjectValue($type, $value);
				break;
			case 'array':
				$unmappedValue = $this->unmapArrayValue($type, $value);
				break;
			default:
				$unmappedValue = $value;
				break;
		}

		return $unmappedValue;
	}

	/**
	 * @param string $type
	 * @param object $value
	 * @return object
	 * @throws MapperException
	 */
	protected function unmapObjectValue(string $type, $value) {
		$unmappedValue = $value;
		if(self::isCustomType($type)) {
			$unmappedValue = $this->unmap($value);
		}

		return $unmappedValue;
	}

	/**
	 * @param string $type
	 * @param array $value
	 * @return array
	 */
	protected function unmapArrayValue(string $type, array $value) {
		$mappedValue = [];
		if(strpos($type, '[]')) {
			$type = rtrim($type, '[]');
		}
		foreach($value as $key => $val) {
			$mappedValue[$key] = $this->unmapValueByType($type, $val);
		}

		return $mappedValue;
	}

	protected function validateType(ObjectProperty $objectProperty, $value) {
		if($this->isStrict && isset($value) && $objectProperty->type != gettype($value)) {
			throw new MapperValidationException('Property ' . $objectProperty->name . ' of type ' . $objectProperty->type . ' not matched while mapping object.');
		}
	}

	protected function validateRequired(ObjectProperty $objectProperty, $value) {
		if($this->isStrict && !isset($value) && $objectProperty->required) {
			throw new MapperValidationException('Required property ' . $objectProperty->name . ' not matched while mapping object.');
		}
	}

	protected function validateSourceRoot($sourceObject, string $rootName) {
		if($this->isStrict && !empty($rootName) && !isset($sourceObject->$rootName)) {
			throw new MapperValidationException('Source class doesnt have a root element named ' . $rootName . ' defined.');
		}
	}

	/**
	 * @param ObjectProperty $objectProperty
	 * @param $sourceObject
	 * @return object|null
	 * @throws MapperValidationException
	 */
	protected function findObjectValue(ObjectProperty $objectProperty, $sourceObject) {
		$objectValue = null;
		foreach($sourceObject as $key => $value) {
			if($objectProperty->name == $key) {
				$objectValue = $value;
				break;
			}
		}

		return $objectValue;
	}

	public static function isCustomType($type) {
		$result = true;
		$simpleTypes = ['boolean', 'integer', 'double', 'string', 'array', 'object'];
		foreach($simpleTypes as $simpleType) {
			if($type == $simpleType) {
				$result = false;
				break;
			}
		}

		return $result;
	}
}
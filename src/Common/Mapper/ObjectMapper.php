<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 3/29/2016
 * Time: 9:27 AM
 */

namespace Common\Mapper;
use Common\Models\ModelClass;
use Common\Models\ModelProperty;
use Common\Models\ModelPropertyType;

class ObjectMapper {

	/**
	 * @param object $sourceObject
	 * @param object $customObject
	 * @return object
	 * @throws ObjectMapperException
	 * @throws \InvalidArgumentException
	 */
	public function map($sourceObject, $customObject) {
		if(self::isObjectEmpty($sourceObject)) {
			throw new \InvalidArgumentException('Invalid object(s) supplied for mapping.');
		}
		$modelClass = new ModelClass($customObject);

		if(self::hasRoot($sourceObject, $modelClass->rootName)) {
			$sourceObject = $sourceObject->{$modelClass->rootName};
		}

		foreach($modelClass->properties as $property) {
			$sourcePropertyValue = $this->findObjectValueByName($property, $sourceObject);

			$mappedPropertyValue = $this->mapValueByType($property->type, $sourcePropertyValue);
			$property->setPropertyValue($mappedPropertyValue);
		}

		return $customObject;
	}

	/**
	 * @param object $customObject
	 * @return \stdClass
	 * @throws ObjectMapperException
	 */
	public function unmap($customObject) {
		if(empty($customObject)) {
			throw new ObjectMapperException('Invalid object supplied for unmapping.');
		}

		$modelClass = new ModelClass($customObject);
		$unmappedObject = new \stdClass();
		foreach($modelClass->properties as $property) {
			$propertyKey = $property->getName();
			$propertyValue = $property->getPropertyValue();
			if(empty($propertyValue)) {
				continue;
			}
			$unmappedObject->$propertyKey = $this->unmapValueByType($property->type, $propertyValue);
		}

		if(!empty($modelClass->rootName)) {
			$unmappedObject = $this->addRootElement($unmappedObject, $modelClass->rootName);
		}

		return $unmappedObject;
	}

	protected function addRootElement($object, string $rootName) {
		$newObject = new \stdClass();
		$newObject->$rootName = $object;

		return $newObject;
	}

	/**
	 * @param ModelPropertyType $type
	 * @param mixed $value
	 * @return array|mixed|object
	 * @throws ObjectMapperException
	 */
	protected function mapValueByType(ModelPropertyType $type, $value) {
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
				throw new ObjectMapperException('Invalid type ' . gettype($value) .' supplied for property.');
				break;
		}

		return $mappedValue;
	}

	/**
	 * @param ModelPropertyType $type
	 * @param object $value
	 * @return object
	 */
	protected function mapObjectValue(ModelPropertyType $type, $value) {
		$mappedValue = $value;
		if($type->isCustomType) {
			$customClassName = $type->getCustomClassName();
			$customClass = new $customClassName();
			$mappedValue = $this->map($value, $customClass);
		}

		return $mappedValue;
	}

	/**
	 * @param ModelPropertyType $type
	 * @param array $value
	 * @return array
	 * @throws ObjectMapperException
	 */
	protected function mapArrayValue(ModelPropertyType $type, array $value) {
		if(strpos($type->annotatedType, '[]')) {
			$arrayType = rtrim($type->annotatedType, '[]');
		}

		$mappedValue = [];
		foreach($value as $key => $val) {
			if(empty($arrayType)) {
				$arrayType = gettype($val);
			}
			$newType = new ModelPropertyType(gettype($value), $arrayType, $type->namespace);
			$mappedValue[$key] = $this->mapValueByType($newType, $val);
		}

		return $mappedValue;
	}

	/**
	 * @param ModelPropertyType $type
	 * @param mixed $value
	 * @return mixed
	 */
	protected function unmapValueByType(ModelPropertyType $type, $value) {
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
	 * @param ModelPropertyType $type
	 * @param object $value
	 * @return object
	 * @throws ObjectMapperException
	 */
	protected function unmapObjectValue(ModelPropertyType $type, $value) {
		$unmappedValue = $value;
		if($type->isCustomType) {
			$unmappedValue = $this->unmap($value);
		}

		return $unmappedValue;
	}

	/**
	 * @param ModelPropertyType $type
	 * @param array $value
	 * @return array
	 */
	protected function unmapArrayValue(ModelPropertyType $type, array $value) {
		if(strpos($type->annotatedType, '[]')) {
			$arrayType = rtrim($type->annotatedType, '[]');
		}

		$unmappedValue = [];
		foreach($value as $key => $val) {
			if(empty($arrayType)) {
				$arrayType = gettype($val);
			}
			$newType = new ModelPropertyType(gettype($value), $arrayType, $type->namespace);
			$unmappedValue[$key] = $this->unmapValueByType($newType, $val);
		}

		return $unmappedValue;
	}

	/**
	 * Takes default model value if any set
	 * @param ModelProperty $ModelProperty
	 * @param $sourceObject
	 * @return object|null
	 */
	protected function findObjectValueByName(ModelProperty $ModelProperty, $sourceObject) {
		$objectValue = $ModelProperty->getPropertyValue();
		foreach($sourceObject as $key => $value) {
			if($ModelProperty->getName() == $key) {
				$objectValue = $value;
				break;
			}
		}

		return $objectValue;
	}

	/**
	 * @param object $sourceObject
	 * @param string $rootName
	 * @return bool
	 * @throws ObjectMapperException
	 */
	protected static function hasRoot($sourceObject, string $rootName) {
		$hasRoot = false;
		if(!empty($rootName) && isset($sourceObject->$rootName)) {
			$hasRoot = true;
		}
		if(!empty($rootName) && !isset($sourceObject->$rootName)) {
			throw new ObjectMapperException('The source object has no ' . $rootName . ' root defined.');
		}

		return $hasRoot;
	}

	/**
	 * @param object $object
	 * @return bool
	 */
	public static function isObjectEmpty($object) {
		$isEmpty = true;
		$array = (array) $object;
		foreach($array as $value) {
			if (!empty($value)) {
				$isEmpty = false;
				break;
			}
		}

		return $isEmpty;
	}
}
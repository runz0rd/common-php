<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 3/29/2016
 * Time: 9:27 AM
 */

namespace Common\Mapper;
use Common\Models\ModelClassData;
use Common\Models\ModelPropertyData;

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
		$modelClassData = new ModelClassData($customObject);

		if(self::hasRoot($sourceObject, $modelClassData->rootName)) {
			$sourceObject = $sourceObject->{$modelClassData->rootName};
		}

		foreach($modelClassData->properties as $property) {
			$sourcePropertyValue = $this->findObjectValue($property, $sourceObject);

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

		$modelClassData = new ModelClassData($customObject);
		$unmappedObject = new \stdClass();
		foreach($modelClassData->properties as $property) {
			$propertyKey = $property->name;
			$propertyValue = $property->getPropertyValue();
			if(empty($propertyValue)) {
				continue;
			}
			$unmappedObject->$propertyKey = $this->unmapValueByType($property->type, $propertyValue);
		}

		if(!empty($modelClassData->rootName)) {
			$unmappedObject = $this->addRootElement($unmappedObject, $modelClassData->rootName);
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
	 * @throws ObjectMapperException
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
				throw new ObjectMapperException('Invalid type ' . gettype($value) .' supplied for property.');
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
	 * @throws ObjectMapperException
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
	 * @throws ObjectMapperException
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

	/**
	 * @param ModelPropertyData $ModelPropertyData
	 * @param $sourceObject
	 * @return object|null
	 */
	protected function findObjectValue(ModelPropertyData $ModelPropertyData, $sourceObject) {
		$objectValue = null;
		foreach($sourceObject as $key => $value) {
			if($ModelPropertyData->name == $key) {
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

	/**
	 * @param object $sourceObject
	 * @param string $rootName
	 * @return bool
	 * @throws ObjectMapperException
	 */
	protected static function hasRoot($sourceObject, string $rootName) {
		$hasRoot = true;
		if(empty($rootName)) {
			$hasRoot = false;
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
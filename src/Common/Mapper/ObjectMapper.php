<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 3/29/2016
 * Time: 9:27 AM
 */

namespace Common\Mapper;
use Common\Models\ModelClass;
use Common\Models\ModelPropertyType;
use Common\Validator\ObjectValidator;

class ObjectMapper implements IModelMapper {

	/**
	 * @param object $source
	 * @param object $model
	 * @return object
	 * @throws ObjectMapperException
	 * @throws \InvalidArgumentException
	 */
	public function map($source, $model) {
		if(!is_object($source)) {
			throw new \InvalidArgumentException('Source must be an object.');
		}
		$modelClass = new ModelClass($model);

		if(self::hasRoot($source, $modelClass->getRootName())) {
			$source = $source->{$modelClass->getRootName()};
		}

		foreach($modelClass->getProperties() as $property) {
			$sourceValue = $this->findSourceValueByName($property->getName(), $source, $property->getPropertyValue());
			$mappedValue = $this->mapValueByType($property->getType(), $sourceValue);
			$property->setPropertyValue($mappedValue);
		}

		return $model;
	}

	/**
	 * @param ModelPropertyType $propertyType
	 * @param mixed $value
	 * @return mixed
	 */
	protected function mapValueByType(ModelPropertyType $propertyType, $value) {
		$mappedPropertyValue = $value;

		if($propertyType->isModel()) {
			if($propertyType->getActualType() == 'array' && is_array($value)) {
				$mappedPropertyValue = $this->mapModelArray($propertyType->getModelClassName(), $value);
			}

			elseif($propertyType->getActualType() == 'object' && is_object($value)) {
				$mappedPropertyValue = $this->mapModel($propertyType->getModelClassName(), $value);
			}
		}

		return $mappedPropertyValue;
	}

	/**
	 * @param string $modelClassName
	 * @param array $source
	 * @return array
	 */
	protected function mapModelArray(string $modelClassName, array $source) {
		$mappedModelArray = null;
		foreach($source as $key => $value) {
//			$mappedModelArray[$key] = $value;
			if(is_object($value)) {
				$mappedModelArray[$key] = $this->mapModel($modelClassName, $value);
			}
		}

		return $mappedModelArray;
	}

	/**
	 * @param string $modelClassName
	 * @param object $source
	 * @return object
	 */
	protected function mapModel(string $modelClassName, $source) {
		$model = new $modelClassName();
		$mappedModel = $this->map($source, $model);

		return $mappedModel;
	}

	/**
	 * @param object $model
	 * @return \stdClass
	 * @throws ObjectMapperException
	 */
	public function unmap($model) {
		if(!is_object($model)) {
			throw new \InvalidArgumentException('Model must be an object.');
		}

		$modelClass = new ModelClass($model);
		$unmappedObject = new \stdClass();
		foreach($modelClass->getProperties() as $property) {
			$propertyKey = $property->getName();
			$propertyValue = $property->getPropertyValue();
			if(ObjectValidator::isValueEmpty($propertyValue)) {
				continue;
			}
			$unmappedObject->$propertyKey = $this->unmapValueByType($property->getType(), $propertyValue);
		}

		if(!ObjectValidator::isValueEmpty($modelClass->getRootName())) {
			$unmappedObject = $this->addRootElement($unmappedObject, $modelClass->getRootName());
		}

		return $unmappedObject;
	}

	/**
	 * @param ModelPropertyType $propertyType
	 * @param mixed $value
	 * @return mixed
	 */
	protected function unmapValueByType(ModelPropertyType $propertyType, $value) {
		$unmappedPropertyValue = $value;

		if($propertyType->isModel()) {
			if($propertyType->getActualType() == 'array' && is_array($value)) {
				$unmappedPropertyValue = $this->unmapModelArray($value);
			}

			elseif($propertyType->getActualType() == 'object' && is_object($value)) {
				$unmappedPropertyValue = $this->unmapModel($value);
			}
		}

		return $unmappedPropertyValue;
	}

	/**
	 * @param array $modelArray
	 * @return array
	 */
	protected function unmapModelArray(array $modelArray) {
		$unmappedObjectArray = [];
		foreach($modelArray as $k => $v) {
			$unmappedObjectArray[$k] = $this->unmapModel($v);
		}

		return $unmappedObjectArray;
	}

	/**
	 * @param object $model
	 * @return object
	 */
	protected function unmapModel($model) {
		$unmappedObject = $this->unmap($model);

		return $unmappedObject;
	}

	/**
	 * @param $object
	 * @param string $rootName
	 * @return \stdClass
	 */
	protected function addRootElement($object, string $rootName) {
		$newObject = new \stdClass();
		$newObject->$rootName = $object;

		return $newObject;
	}

	/**
	 * Takes default model value if any set
	 * @param string $name
	 * @param object|array $source
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	protected function findSourceValueByName(string $name, $source, $defaultValue) {
		$sourceValue = $defaultValue;
		foreach($source as $key => $value) {
			if($name == $key) {
				$sourceValue = $value;
				break;
			}
		}

		return $sourceValue;
	}

	/**
	 * @param object $sourceObject
	 * @param string $rootName
	 * @return bool
	 * @throws ObjectMapperException
	 */
	protected static function hasRoot($sourceObject, string $rootName) {
		$hasRoot = false;
		if(!ObjectValidator::isValueEmpty($rootName) && isset($sourceObject->$rootName)) {
			$hasRoot = true;
		}
		if(!ObjectValidator::isValueEmpty($rootName) && !isset($sourceObject->$rootName)) {
			throw new ObjectMapperException('The source object has no ' . $rootName . ' root defined.');
		}

		return $hasRoot;
	}
}
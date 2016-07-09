<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/4/2016
 * Time: 9:05 AM
 */

namespace Common\Models;


class ModelPropertyType {

	/**
	 * @var string
	 */
	private $propertyType;

	/**
	 * @var string
	 */
	private $annotatedType;

	/**
	 * @var bool
	 */
	private $isModel = false;

	/**
	 * @var string
	 */
	private $actualType;

	/**
	 * @var string
	 */
	private $namespace;

	public function __construct(string $propertyType, string $annotatedType, string $namespace) {
		$this->propertyType = $propertyType;
		$this->annotatedType = $annotatedType;
		$this->namespace = $namespace;

		$this->actualType = $this->annotatedType;
		if(self::isCustomType($this->annotatedType)) {
			$this->isModel = true;
			$this->actualType = 'object';
		}
		if(strpos($this->annotatedType, '[]')) {
			$this->actualType = 'array';
		}
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public static function isCustomType(string $type) {
		$result = true;
		$simpleTypes = ['boolean', 'integer', 'double', 'string', 'array', 'object',
			'boolean[]', 'integer[]', 'double[]', 'string[]', '[]', 'object[]'];
		foreach($simpleTypes as $simpleType) {
			if($type == $simpleType) {
				$result = false;
				break;
			}
		}

		return $result;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getModelClassName() {
		if(!$this->isModel) {
			throw new \Exception('Property type is not custom.');
		}
		$customType = $this->annotatedType;
		if(strpos($customType, '[]')) {
			$customType = rtrim($this->annotatedType, '[]');
		}
		if(!strpos($customType, '\\')) {
			$customType = '\\' . $customType;
		}
		$className = $this->namespace . $customType;

		return $className;
	}

	/**
	 * @return string
	 */
	public function getPropertyType()
	{
		return $this->propertyType;
	}

	/**
	 * @return string
	 */
	public function getAnnotatedType()
	{
		return $this->annotatedType;
	}

	/**
	 * @return boolean
	 */
	public function isModel()
	{
		return $this->isModel;
	}

	/**
	 * @return string
	 */
	public function getActualType()
	{
		return $this->actualType;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}
}
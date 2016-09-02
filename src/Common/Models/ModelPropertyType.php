<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/4/2016
 * Time: 9:05 AM
 */

namespace Common\Models;


use Common\Util\Validation;

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
	private $parentNS;

    /**
     * ModelPropertyType constructor.
     * @param string $propertyType
     * @param string $annotatedType
     * @param string $parentNS
     */
	public function __construct(string $propertyType, string $annotatedType, string $parentNS) {
		$this->propertyType = $propertyType;
		$this->annotatedType = $annotatedType;
		$this->parentNS = $parentNS;

		$this->actualType = $this->annotatedType;
		if(Validation::isCustomType($this->annotatedType)) {
			$this->isModel = true;
			$this->actualType = 'object';

			if(strpos($this->annotatedType, '[]') !== false) {
				$this->actualType = 'array';
			}
		}
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getModelClassName() {
		if(!$this->isModel) {
			throw new \Exception('Property is not a model.');
		}
		$modelClassName = $this->annotatedType;
		if(strpos($modelClassName, '[]')) {
            $modelClassName = rtrim($modelClassName, '[]');
		}
		if(strpos($modelClassName, '\\') === false) {
            $modelClassName = $this->parentNS . '\\' . $modelClassName;
		}

		return $modelClassName;
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
}
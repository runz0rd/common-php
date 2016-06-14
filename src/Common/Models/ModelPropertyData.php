<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:50 PM
 */

namespace Common\Models;
use Common\Mapper\ObjectMapper;

class ModelPropertyData {

	/**
	 * @var string
	 */
	public $propertyName;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var bool
	 */
	public $isRequired;

	/**
	 * @var array
	 */
	public $requiredTypes;

	/**
	 * @var \ReflectionProperty
	 */
	private $property;

	/**
	 * @var object
	 */
	private $object;

	/**
	 * @var DocBlock
	 */
	public $docBlock;

	/**
	 * ModelPropertyData constructor.
	 * @param \ReflectionProperty $property
	 * @param object $object
	 * @param string $namespace
	 */
	public function __construct(\ReflectionProperty $property, $object, string $namespace) {
		$this->property = $property;
		$this->object = $object;
		$this->docBlock = new DocBlock($property->getDocComment());
		$this->propertyName = $property->getName();

		$this->name = $property->getName();
		if($this->docBlock->annotationExists('name')) {
			$this->name = $this->docBlock->getFirstAnnotation('name');
		}

		$this->type = 'NULL';
		if($this->docBlock->annotationExists('var')) {
			$type = $this->docBlock->getFirstAnnotation('var');
			if(ObjectMapper::isCustomType($type) && !strpos($type, '\\')) {
				$type = $namespace . '\\' . $type;
			}
			$this->type = $type;
		}

		$this->isRequired = false;
		if($this->docBlock->annotationExists('required')) {
			$this->isRequired = true;
			$this->requiredTypes = $this->docBlock->getAnnotation('required');
		}
	}

	/**
	 * @param mixed $value
	 */
	public function setPropertyValue($value) {
		$this->property->setValue($this->object, $value);
	}

	/**
	 * @return mixed
	 */
	public function getPropertyValue() {
		return $this->property->getValue($this->object);
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:50 PM
 */

namespace Common\Models;
use Common\Mapper\ObjectMapper;

class ModelProperty {

	/**
	 * @var string
	 */
	public $className;

	/**
	 * @var string
	 */
	public $propertyName;

	/**
	 * @var ModelPropertyType
	 */
	public $type;

	/**
	 * @var string
	 */
	public $annotatedName;

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

		$this->className = get_class($object);

		$this->propertyName = $property->getName();
		if($this->docBlock->annotationExists('name')) {
			$this->annotatedName = $this->docBlock->getFirstAnnotation('name');
		}

		$propertyType = gettype($this->property->getValue($object));
		$annotatedType = 'NULL';
		if($this->docBlock->annotationExists('var')) {
			$annotatedType = $this->docBlock->getFirstAnnotation('var');
		}
		$this->type = new ModelPropertyType($propertyType, $annotatedType, $namespace);

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

	/**
	 * Returns the given property name, or @name value, if set
	 * @return string
	 */
	public function getName() {
		$name = $this->propertyName;
		if(!empty($this->annotatedName)) {
			$name = $this->annotatedName;
		}

		return $name;
	}
}
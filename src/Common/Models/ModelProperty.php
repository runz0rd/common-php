<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:50 PM
 */

namespace Common\Models;
use Common\Validator\ObjectValidator;

class ModelProperty {

	/**
	 * @var string
	 */
	private $className;

	/**
	 * @var string
	 */
	private $propertyName;

	/**
	 * @var ModelPropertyType
	 */
	private $type;

	/**
	 * @var string
	 */
	private $annotatedName;

	/**
	 * @var bool
	 */
	private $isRequired;

	/**
	 * @var array
	 */
	private $requiredTypes;

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
	private $docBlock;

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
		if(!ObjectValidator::isValueEmpty($this->annotatedName)) {
			$name = $this->annotatedName;
		}

		return $name;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function getPropertyName()
	{
		return $this->propertyName;
	}

	/**
	 * @return ModelPropertyType
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getAnnotatedName()
	{
		return $this->annotatedName;
	}

	/**
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->isRequired;
	}

	/**
	 * @return array
	 */
	public function getRequiredTypes()
	{
		return $this->requiredTypes;
	}

	/**
	 * @return \ReflectionProperty
	 */
	public function getProperty()
	{
		return $this->property;
	}

	/**
	 * @return object
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @return DocBlock
	 */
	public function getDocBlock()
	{
		return $this->docBlock;
	}
}
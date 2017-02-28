<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:50 PM
 */

namespace Common\ModelReflection;
use Common\Util\Validation;
use Common\ModelReflection\Enum\TypeEnum;
use Common\ModelReflection\Enum\AnnotationEnum;

class ModelProperty {

	/**
	 * @var string
	 */
	private $parentClassName;

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
	private $requiredActions;

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
	 * @param object $parent
	 * @param string $parentNS
	 */
	public function __construct(\ReflectionProperty $property, $parent, $parentNS) {
		$this->property = $property;
		$this->object = $parent;
		$this->docBlock = new DocBlock($property->getDocComment());

		$this->parentClassName = get_class($parent);

		$this->propertyName = $property->getName();
		if($this->docBlock->hasAnnotation(AnnotationEnum::NAME)) {
			$this->annotatedName = $this->docBlock->getFirstAnnotation(AnnotationEnum::NAME);
		}

		$propertyType = gettype($this->property->getValue($parent));
		$annotatedType = TypeEnum::ANY;
		if($this->docBlock->hasAnnotation(AnnotationEnum::VAR) && !Validation::isEmpty($this->docBlock->getFirstAnnotation(AnnotationEnum::VAR))) {
			$annotatedType = $this->docBlock->getFirstAnnotation(AnnotationEnum::VAR);
		}
		$this->type = new ModelPropertyType($propertyType, $annotatedType, $parentNS);

		$this->isRequired = false;
        $this->requiredActions = array();
		if($this->docBlock->hasAnnotation(AnnotationEnum::REQUIRED)) {
			$this->isRequired = true;
			$this->requiredActions = $this->docBlock->getAnnotation(AnnotationEnum::REQUIRED);
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
		if(!Validation::isEmpty($this->annotatedName)) {
			$name = $this->annotatedName;
		}

		return $name;
	}

	/**
	 * @return string
	 */
	public function getParentClassName()
	{
		return $this->parentClassName;
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
	public function getRequiredActions()
	{
		return $this->requiredActions;
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
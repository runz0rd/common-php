<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:50 PM
 */

namespace Common\Mapper;

class ObjectProperty {

	public $name;
	public $type;
	public $required;

	/**
	 * @var \ReflectionProperty
	 */
	private $property;

	/**
	 * @var DocBlock
	 */
	public $docBlock;

	public function __construct(\ReflectionProperty $property, $namespace) {
		$this->property = $property;
		$this->docBlock = new DocBlock($property->getDocComment());

		$this->name = $property->getName();
		if($this->docBlock->annotationExists('name')) {
			$this->name = $this->docBlock->getAnnotation('name');
		}

		$this->type = 'NULL';
		if($this->docBlock->annotationExists('var')) {
			$type = $this->docBlock->getAnnotation('var');
			if(ObjectMapper::isCustomType($type) && !strpos($type, '\\')) {
				$type = $namespace . '\\' . $type;
			}
			$this->type = $type;
		}

		$this->required = false;
		if($this->docBlock->annotationExists('required')) {
			$this->required = true;
		}
	}

	public function setPropertyValue($object, $value) {
		$this->property->setValue($object, $value);
	}

	public function getPropertyValue($object) {
		return $this->property->getValue($object);
	}
}
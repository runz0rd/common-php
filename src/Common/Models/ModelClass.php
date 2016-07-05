<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/9/2016
 * Time: 1:30 PM
 */

namespace Common\Models;

class ModelClass {

	/**
	 * @var string
	 */
	public $className;

	/**
	 * @var string
	 */
	public $namespace;

	/**
	 * @var string
	 */
	public $rootName;

	/**
	 * @var ModelProperty[]
	 */
	public $properties;

	/**
	 * @var DocBlock
	 */
	public $docBlock;

	/**
	 * ModelClassData constructor.
	 * @param object $customObject
	 */
	public function __construct($customObject) {
		$reflectionClass = new \ReflectionClass($customObject);
		$this->docBlock = new DocBlock($reflectionClass->getDocComment());
		$this->className = $reflectionClass->getName();
		$this->namespace = $reflectionClass->getNamespaceName();

		$this->rootName = '';
		if($this->docBlock->annotationExists('root') && !empty($this->docBlock->getAnnotation('root'))) {
			$this->rootName = $this->docBlock->getFirstAnnotation('root');
		}

		$properties = $reflectionClass->getProperties();
		foreach($properties as $property) {
			$this->properties[] = new ModelProperty($property, $customObject, $this->namespace);
		}
	}
}
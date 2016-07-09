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
	private $className;

	/**
	 * @var string
	 */
	private $namespace;

	/**
	 * @var string
	 */
	private $rootName;

	/**
	 * @var ModelProperty[]
	 */
	private $properties;

	/**
	 * @var DocBlock
	 */
	private $docBlock;

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
			$modelProperty = new ModelProperty($property, $customObject, $this->namespace);
			if(!$modelProperty->getDocBlock()->annotationExists('internal')) {
				$this->properties[] = $modelProperty;
			}
		}
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
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @return string
	 */
	public function getRootName()
	{
		return $this->rootName;
	}

	/**
	 * @return ModelProperty[]
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @return DocBlock
	 */
	public function getDocBlock()
	{
		return $this->docBlock;
	}
}
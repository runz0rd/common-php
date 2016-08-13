<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/9/2016
 * Time: 1:30 PM
 */

namespace Common\Models;
use Common\Util\Validation;

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
	 * @param object $model
	 */
	public function __construct($model) {
		$reflectionClass = new \ReflectionClass($model);
		$this->docBlock = new DocBlock($reflectionClass->getDocComment());
		$this->className = $reflectionClass->getName();
		$this->namespace = $reflectionClass->getNamespaceName();

		$this->rootName = $this->className;
		if($this->docBlock->annotationExists('root') && !Validation::isEmpty($this->docBlock->getAnnotation('root'))) {
			$this->rootName = $this->docBlock->getFirstAnnotation('root');
		}

		$properties = $reflectionClass->getProperties();
        if(count($properties) == 0) {
            throw new \InvalidArgumentException('The model class ' . $reflectionClass->getName() . ' has no properties defined.');
        }

		foreach($properties as $property) {
			$modelProperty = new ModelProperty($property, $model, $this->namespace);
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
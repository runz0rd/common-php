<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/9/2016
 * Time: 1:30 PM
 */

namespace Common\Mapper;

class ObjectClass {

	public $namespace;
	public $rootName;

	/**
	 * @var ObjectProperty[]
	 */
	public $properties;

	/**
	 * @var DocBlock
	 */
	public $docBlock;

	/**
	 * ObjectClass constructor.
	 * @param object $customObject
	 */
	public function __construct($customObject) {
		$reflectionClass = new \ReflectionClass($customObject);
		$this->docBlock = new DocBlock($reflectionClass->getDocComment());

		$this->namespace = $reflectionClass->getNamespaceName();

		$this->rootName = '';
		if($this->docBlock->annotationExists('root') && !empty($this->docBlock->getAnnotation('root'))) {
			$this->rootName = $this->docBlock->getAnnotation('root');
		}

		$properties = $reflectionClass->getProperties();
		foreach($properties as $property) {
			$this->properties[] = new ObjectProperty($property, $this->namespace);
		}
	}
}
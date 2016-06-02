<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 3/29/2016
 * Time: 9:27 AM
 */

namespace Common\Mapper;
use ReflectionClass;
use ReflectionProperty;

class ObjectMapper extends \JsonMapper {

	/**
	 * Check required properties exist in json
	 * Overridden version throws MapperValidationException
	 * @param array $providedProperties
	 * @param ReflectionClass $rc
	 * @throws MapperValidationException
	 */
	protected function checkMissingData($providedProperties, ReflectionClass $rc)
	{
		foreach ($rc->getProperties() as $property) {
			$rprop = $rc->getProperty($property->name);
			$docblock = $rprop->getDocComment();
			$annotations = $this->parseAnnotations($docblock);
			if (isset($annotations['required'])
				&& !isset($providedProperties[$property->name])
			) {
				throw new MapperValidationException(
					'Required property "' . $property->name . '" of class '
					. $rc->getName()
					. ' is missing in JSON data'
				);
			}
		}
	}

	/**
	 * Extended this method to to support mapping by "@name" phpdoc.
	 * Useful for mapping fields with illegal characters.
	 *
	 * @param ReflectionClass $rc   Reflection class to check
	 * @param string $name Property name
	 *
	 * @return array First value: if the property exists
	 *               Second value: the accessor to use (
	 *                 ReflectionMethod or ReflectionProperty, or null)
	 *               Third value: type of the property
	 */
	protected function inspectProperty(ReflectionClass $rc, $name) {
		//try setter method first
		$setter = 'set' . str_replace(
				' ', '', ucwords(str_replace('_', ' ', $name))
			);
		if ($rc->hasMethod($setter)) {
			$rmeth = $rc->getMethod($setter);
			if ($rmeth->isPublic()) {
				$rparams = $rmeth->getParameters();
				if (count($rparams) > 0) {
					$pclass = $rparams[0]->getClass();
					if ($pclass !== null) {
						return array(
							true, $rmeth, '\\' . $pclass->getName()
						);
					}
				}

				$docblock    = $rmeth->getDocComment();
				$annotations = $this->parseAnnotations($docblock);

				if (!isset($annotations['param'][0])) {
					return array(true, $rmeth, null);
				}
				list($type) = explode(' ', trim($annotations['param'][0]));
				return array(true, $rmeth, $type);
			}
		}

		//now try to set the property directly
		if ($rc->hasProperty($name)) {
			$rprop = $rc->getProperty($name);
		} else {
			//case-insensitive property matching
			$rprop = null;
			foreach ($rc->getProperties(ReflectionProperty::IS_PUBLIC) as $p) {
				if ((strcasecmp($p->name, $name) === 0)) {
					$rprop = $p;
					break;
				}
			}

			//support "@name annotation"
			foreach ($rc->getProperties(ReflectionProperty::IS_PUBLIC) as $p) {
				$docblock = $p->getDocComment();
				$annotations = $this->parseAnnotations($docblock);

				if(isset($annotations['name'][0]) && $name == $annotations['name'][0]) {
					$rprop = $p;
					break;
				}
			}
		}
		if ($rprop !== null) {
			if ($rprop->isPublic()) {
				$docblock    = $rprop->getDocComment();
				$annotations = $this->parseAnnotations($docblock);

				//support "@var type description"
				if (!isset($annotations['var'][0])) {
					return array(true, $rprop, null);
				}

				list($type) = explode(' ', $annotations['var'][0]);

				return array(true, $rprop, $type);
			} else {
				//no setter, private property
				return array(true, null, null);
			}
		}

		//no setter, no property
		return array(false, null, null);
	}

	/**
	 * Creates a new \stdClass object while taking "@name" annotation values as property names.
	 * Skips properties with null values.
	 * Currently working with public properties only.
	 *
	 * @param object $object
	 * @return \stdClass
	 */
	public function prepare($object) {
		$returnObject = new \stdClass();
		$reflection = new ReflectionClass($object);

		$publicProperties = $reflection->getProperties();
		foreach ($publicProperties as $property) {
			$docBlock = $property->getDocComment();
			$annotations = $this->parseAnnotations($docBlock);

			$propertyName = $property->getName();
			$propertyValue = $property->getValue($object);

			if(is_null($propertyValue)) {
				continue;
			}
			if(isset($annotations['name'][0])) {
				$propertyName = $annotations['name'][0];
			}

			$value = $propertyValue;
			if(is_object($propertyValue) && !($propertyValue instanceof \stdClass)) {
				$value = $this->prepare($propertyValue);
			}
			if(is_array($propertyValue || (is_object($propertyValue) && $propertyValue instanceof \stdClass))) {
				$value = $this->prepareIterable($propertyValue);
			}

			$returnObject->$propertyName = $value;
		}

		return $returnObject;
	}

	/**
	 * @param array|object $iterable
	 * @return array
	 */
	public function prepareIterable($iterable) {
		$returnArray = [];
		foreach($iterable as $iKey => $iValue) {
			$value = $iValue;
			if(is_object($iValue)) {
				$value = $this->prepare($iValue);
			}
			$returnArray[$iKey] = $value;
		}

		return $returnArray;
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/9/2016
 * Time: 7:56 PM
 */

namespace Common\Mapper;
use Common\Util\Iteration;
use Common\Util\Validation;
use Common\Util\Xml;

class XmlModelMapper implements IModelMapper {

    /**
     * @var IModelMapper
     */
    public $modelMapper;

    /**
     * XmlModelMapper constructor.
     */
    public function __construct() {
        $this->modelMapper = new ModelMapper();
    }

    /**
     * @param mixed $source
     * @param object $model
     * @return object
     */
    public function map($source, $model) {
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($source);
        $domElement = $domDocument->documentElement;
        $object = $this->fromXml($domElement);

        $model = $this->modelMapper->map($source, $model);

        return $model;
    }

    /**
     * @param object $model
     * @return string
     */
    public function unmap($model) {
        $object = $this->modelMapper->unmap($model);
        $rootName = $this->modelMapper->getModelRootName($model);
        $xml = $this->toXml($object, $rootName);

        return $xml;
    }

    /**
     * @param \DOMElement $domElement
     * @return \stdClass
     */
    protected function fromXml(\DOMElement $domElement) {
        $object = new \stdClass();

        $attributesKey = '@attributes';
        for($i = 0; $i < $domElement->attributes->length; $i++) {
            $key = $domElement->attributes->item($i)->nodeName;
            $value = $domElement->attributes->item($i)->nodeValue;
            $object->$attributesKey[$key] = $value;
        }

        for($i = 0; $i < $domElement->childNodes->length; $i++) {
            $key = $domElement->childNodes->item($i)->nodeName;
            $type = $domElement->childNodes->item($i)->nodeType;
            $hasAttributes = $domElement->childNodes->item($i)->hasAttributes();
            $hasChildren = $domElement->childNodes->item($i)->hasChildNodes();

            if($type == 1) {
                $value = $domElement->childNodes->item($i)->nodeValue;
                if($hasAttributes) {
                    $value = $this->fromXml($domElement->childNodes->item($i));
                }
            }
            else {
                $key = $domElement->tagName;
                $value = $domElement->childNodes->item($i)->nodeValue;
            }

            if(isset($object->$key)) {
                $valueArray = $object->$key;
                if(!is_array($object->$key)) {
                    $valueArray = [];
                    $valueArray[] = $object->$key;
                }
                $valueArray[] = $value;
                $value = $valueArray;
            }
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * @param object $source
     * @param string $elementName
     * @return string
     * @throws ModelMapperException
     */
    protected function toXml($source, string $elementName) {
        $elementXml = '<'.$elementName.'></'.$elementName.'>';
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($elementXml);
        $domElement = $domDocument->documentElement;

        $attributesKey = '@attributes';
        if(isset($source->$attributesKey) && !Validation::isEmpty($source->$attributesKey)){
            foreach($source->$attributesKey as $attrKey => $attrValue) {
                $domElement->setAttribute($attrKey, $attrValue);
            }
            unset($source->$attributesKey);
        }

        foreach($source as $key => $value) {
            if(is_object($value)) {
                $child = $this->createDomNode($domDocument, $key, $value);
                $domElement->appendChild($child);
            }
            if(is_array($value)) {
                foreach($value as $arrayKey => $arrayValue) {
                    if(is_object($arrayValue) || is_array($arrayValue)) {
                        $child = $this->createDomNode($domDocument, $key, $arrayValue);
                    }
                    else {
                        $child = $this->createDomElement($key, $arrayValue);
                    }
                    $domElement->appendChild($child);
                }
            }
            else {
                $child = $this->createDomElement($key, $value);
                $domElement->appendChild($child);
            }
        }
        $xml = $domElement->ownerDocument->saveXML();
        $xml = str_replace("\n", "", $xml);

        return $xml;
    }

    /**
     * @param $name
     * @param $value
     * @param null $uri
     * @return \DOMElement
     * @throws ModelMapperException
     */
    protected function createDomElement($name, $value, $uri = null) {
        if(!Xml::isValidElementName($name)) {
            throw new ModelMapperException('Property name "' . $name . '" contains invalid xml element characters.');
        }
        if(is_bool($value)) {
            $value = ($value) ? 'true' : 'false';
        }
        $element = new \DOMElement($name, $value, $uri);

        return $element;
    }

    /**
     * @param \DOMDocument $domDocument
     * @param string $name
     * @param object $value
     * @return \DOMNode
     */
    protected function createDomNode(\DOMDocument $domDocument, string $name, $value) {
        $xmlValue = $this->toXml($value, $name);
        $domDoc = new \DOMDocument();
        $domDoc->loadXML($xmlValue);
        $node = $domDocument->importNode($domDoc->documentElement, true);

        return $node;
    }
}

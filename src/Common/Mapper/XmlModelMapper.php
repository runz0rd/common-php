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

        $model = $this->modelMapper->map($object, $model);

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
     * @param \DOMNode $domElement
     * @return \stdClass
     */
    protected function fromXml(\DOMNode $domElement) {
        $object = new \stdClass();
        $result = null;

        $attributesKey = '@attributes';
        for($i = 0; $i < $domElement->attributes->length; $i++) {
            $key = $domElement->attributes->item($i)->nodeName;
            $value = $domElement->attributes->item($i)->nodeValue;
            $object->$attributesKey[$key] = $value;
        }

        for($i = 0; $i < $domElement->childNodes->length; $i++) {
            $element = $domElement->childNodes->item($i);
            $result = $this->mapByDomNodeType($element, $object);
        }

        return $result;
    }

    public function mapByDomNodeType(\DOMNode $element, $object) {
        $isElementArray = $this->isDomElementArray($element->parentNode, $element->nodeName);
        switch($element->nodeType) {
            case XML_ELEMENT_NODE:
                $object = $this->mapDomElement($element, $object, $isElementArray);
                break;
            case XML_TEXT_NODE:
                $object = $this->mapDomText($element, $object, $isElementArray);
                break;
        }

        return $object;
    }

    public function mapDomElement(\DOMNode $element, $object, bool $isElementArray) {
        $value = $this->fromXml($element);
        $key = $element->nodeName;
        if($isElementArray) {
            $result = $this->mapArrayValue($object, $key, $value);
        }
        else {
            $object->$key = $value;
            $result = $object;
        }

        return $result;
    }

    public function mapDomText(\DOMNode $element, $object, bool $isElementArray) {
        /** @var \DOMElement $element->parentNode */
        $key = $element->parentNode->tagName;
        $value = Iteration::typeFilter($element->nodeValue);
        $attributesKey = '@attributes'; //TODO make into const

        $result = $value;
        if(isset($object->$attributesKey)) {
            $result = clone $object;
            $result->$key = $value;
        }

        if($isElementArray) {
            $result = $this->mapArrayValue($object, $key, $result);
        }

        return $result;
    }

    public function isDomElementArray(\DOMNode $parentElement, string $key) {
        $result = false;
        $keyCount = 0;
        for($i = 0; $i < $parentElement->childNodes->length; $i++) {
            $nodeName = $parentElement->childNodes->item($i)->nodeName;
            if($nodeName == $key) {
                $keyCount++;
            }
        }
        if($keyCount > 1) {
            $result = true;
        }

        return $result;
    }

    public function mapArrayValue($object, string $key, $value) {
        if(!isset($object->$key) || !is_array($object->$key)) {
            $object->$key = [];
        }
        array_push($object->$key, $value);

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
            elseif(is_array($value)) {
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

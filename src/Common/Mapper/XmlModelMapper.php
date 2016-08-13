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
        $source = $this->fromXml($source);
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
     * @param string $xml
     * @return object
     */
    protected function fromXml(string $xml) {
        $simpleXml = simplexml_load_string($xml);
        $json = json_encode($simpleXml);
        $object = json_decode($json);
        $source = Iteration::nullifyEmpty($object);

        return $source;
    }

    /**
     * @param object|array $source
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
     * @param mixed $value
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

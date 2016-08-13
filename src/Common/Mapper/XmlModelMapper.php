<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/9/2016
 * Time: 7:56 PM
 */

namespace Common\Mapper;
use Common\Models\ModelClass;
use Common\Util\Iteration;
use Common\Util\Validation;
use Common\Util\Xml;

class XmlModelMapper extends ModelMapper implements IModelMapper {

    public function fromXml(string $xml, $model) {
        $simpleXml = simplexml_load_string($xml);
        $json = json_encode($simpleXml);
        $object = json_decode($json);
        $source = Iteration::nullifyEmptyProperties($object);

        return $this->map($source, $model);
    }

    public function toXml($model) {
        $object = $this->unmap($model);
        $rootName = $this->getModelRootName($model);
        $xml = $this->objectToXml($object, $rootName);

        return $xml;
    }

    public function getModelRootName($model) {
        $modelClass = new ModelClass($model);
        $rootName = $modelClass->getRootName();

        return $rootName;
    }

    /**
     * @param object|array $source
     * @param string $elementName
     * @return string
     * @throws ModelMapperException
     */
    public function objectToXml($source, string $elementName) {
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
        $xmlValue = $this->objectToXml($value, $name);
        $domDoc = new \DOMDocument();
        $domDoc->loadXML($xmlValue);
        $node = $domDocument->importNode($domDoc->documentElement, true);

        return $node;
    }
}

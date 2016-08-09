<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/9/2016
 * Time: 7:56 PM
 */

namespace Common\Mapper;
use Common\Util\Iteration;

class XmlModelMapper extends ModelMapper implements IModelMapper {

    public function mapXml(string $xml, $model) {
        $simpleXml = simplexml_load_string($xml);
        $json = json_encode($simpleXml);
        $object = json_decode($json);
        $source = Iteration::nullifyEmptyProperties($object);

        return $this->map($source, $model);
    }
}
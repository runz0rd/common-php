<?php

/**
 * Created by PhpStorm.
 * User: milosh
 * Date: 8/9/2016
 * Time: 8:02 PM
 */
use Common\Mapper\XmlModelMapper;

class XmlModelMapperTest extends PHPUnit_Framework_TestCase {

    /**
     * @var XmlModelMapper
     */
    public $xmlMapper;

    public function setUp() {
        $this->xmlMapper = new XmlModelMapper();
        parent::setUp();
    }

    public function testMapXml() {
        $expected = new TestModel();
        $expected->attribute1 = 'attribute1';
        $expected->string = 'asdf';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><testModel attribute1="attribute1" attribute2="attribute2"><string>asdf</string><string2>tt</string2><struct1><string3>asdtt</string3></struct1></testModel>';
        $actual = $this->xmlMapper->mapXml($xml, new TestModel());
        $this->assertEquals($expected, $actual);
    }
}

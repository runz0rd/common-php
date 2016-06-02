<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */
use Common\Mapper\ObjectMapper;

class ObjectMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ObjectMapper
	 */
	public $objectMapper;

	public function setUp() {
		$this->objectMapper = new ObjectMapper();
		parent::setUp();
	}

	public function testPrepare() {
		$model = new MapperModel();
		$model->testProperty1 = 'testVal1';
		$model->testProperty2 = 'testVal2';
		$model->testProperty3 = 'testVal3';
		$model->testArray[] = new stdClass();
		$model->testArray[] = 'testVal4';
		$model->testArray[] = 'testVal5';

		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{},"testVal4","testVal5"]}';
		$preparedObject = $this->objectMapper->prepare($model);

		$this->assertEquals($json, json_encode($preparedObject));
	}

	public function testMap() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{},"testVal4","testVal5"]}';
		$mappedModel = $this->objectMapper->map(json_decode($json), new MapperModel());

		$preparedObject = $this->objectMapper->prepare($mappedModel);

		$this->assertEquals($json, json_encode($preparedObject));
	}
}
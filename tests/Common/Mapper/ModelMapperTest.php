<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */
use Common\Mapper\ModelMapper;

class ModelMapperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ModelMapper
	 */
	public $ModelMapper;

	public function setUp() {
		$this->ModelMapper = new ModelMapper();
		parent::setUp();
	}

	public function testUnmap() {
		$model = new MapperModel();
		$model->testProperty1 = 'testVal1';
		$model->testProperty2 = 'testVal2';
		$model->testProperty3 = 'testVal3';
		$model->testArray[] = new stdClass();
		$model->testArray[] = 'testVal4';
		$model->testArray[] = 'testVal5';

		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{},"testVal4","testVal5"]}';
		$preparedObject = $this->ModelMapper->unmap($model);

		$this->assertEquals($json, json_encode($preparedObject));
	}

	public function testMap() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{},"testVal4","testVal5"]}';
		$mappedModel = $this->ModelMapper->map(json_decode($json), new MapperModel());

		$preparedObject = $this->ModelMapper->unmap($mappedModel);

		$this->assertEquals($json, json_encode($preparedObject));
	}
}
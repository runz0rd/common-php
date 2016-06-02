<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */

class ConvertibleTraitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MapperModel
	 */
	private $model;

	public function setUp() {
		$obj = new stdClass();
		$obj->asd = 'asd';
		$this->model = new MapperModel();
		$this->model->testProperty1 = 'testVal1';
		$this->model->testProperty2 = 'testVal2';
		$this->model->testProperty3 = 'testVal3';
		$this->model->testArray[] = $obj;
		$this->model->testArray[] = 'testVal4';
		$this->model->testArray[] = 'testVal5';
		parent::setUp();
	}

	public function testToArray() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';
		$expectedArray = json_decode($json, true);

		$array = $this->model->toArray();

		$this->assertEquals($expectedArray, $array);
	}

	public function testToJson() {
		$expectedJson = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';

		$json = $this->model->toJson();

		$this->assertEquals($expectedJson, $json);
	}
}
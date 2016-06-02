<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */

class MappableTraitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MapperModel
	 */
	public $expectedModel;

	public function setUp() {
		$obj = new stdClass();
		$obj->asd = 'asd';
		$this->expectedModel = new MapperModel();
		$this->expectedModel->testProperty1 = 'testVal1';
		$this->expectedModel->testProperty2 = 'testVal2';
		$this->expectedModel->testProperty3 = 'testVal3';
		$this->expectedModel->testArray[] = $obj;
		$this->expectedModel->testArray[] = 'testVal4';
		$this->expectedModel->testArray[] = 'testVal5';
		parent::setUp();
	}

	public function testMapFromArray() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';
		$array = json_decode($json, true);

		$model = new MapperModel();
		$model->mapFromArray($array);

		$this->assertEquals(json_encode($this->expectedModel), json_encode($model));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMapFromArrayInvalid() {
		$array = [];

		$model = new MapperModel();
		$model->mapFromArray($array);
	}

	/**
	 * @expectedException \Common\Mapper\MapperValidationException
	 */
	public function testMapFromArrayValidationFail() {
		$json = '{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';
		$array = json_decode($json, true);

		$model = new MapperModel();
		$model->mapFromArray($array, true);
	}

	public function testMapFromJson() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';

		$model = new MapperModel();
		$model->mapFromJson($json);

		$this->assertEquals(json_encode($this->expectedModel), json_encode($model));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMapFromJsonInvalid() {
		$json = '{"testProperty1":"}';

		$model = new MapperModel();
		$model->mapFromJson($json);
	}

	/**
	 * @expectedException \Common\Mapper\MapperValidationException
	 */
	public function testMapFromJsonValidationFail() {
		$json = '{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';

		$model = new MapperModel();
		$model->mapFromJson($json, true);
	}

	public function testMapFromObject() {
		$json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';
		$object = json_decode($json);

		$model = new MapperModel();
		$model->mapFromObject($object);

		$this->assertEquals(json_encode($this->expectedModel), json_encode($model));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMapFromObjectInvalid() {
		$object = null;

		$model = new MapperModel();
		$model->mapFromObject($object);
	}

	/**
	 * @expectedException \Common\Mapper\MapperValidationException
	 */
	public function testMapFromObjectValidationFail() {
		$json = '{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}';
		$object = json_decode($json);

		$model = new MapperModel();
		$model->mapFromObject($object, true);
	}
}
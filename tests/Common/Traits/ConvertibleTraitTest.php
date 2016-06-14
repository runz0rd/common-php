<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */

class ConvertibleTraitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider validValues
	 */
	public function testToArray($json, $model) {
		$expectedArray = json_decode($json, true);
		/** @var MapperModel|MapperModelWithRoot $model */
		$actualArray = $model->toArray();

		$this->assertEquals(json_encode($expectedArray), json_encode($actualArray));
	}

	/**
	 * @dataProvider validValues
	 */
	public function testToJson($json, $model) {
		$expectedJson = $json;
		/** @var MapperModel|MapperModelWithRoot $model */
		$actualJson = $model->toJson();

		$this->assertEquals($expectedJson, $actualJson);
	}

	public function validValues() {
		$obj = new stdClass();
		$obj->asd = 'asd';
		$expectedModel = new MapperModel();
		$expectedModel->testProperty1 = 'testVal1';
		$expectedModel->testProperty2 = 'testVal2';
		$expectedModel->testProperty3 = 'testVal3';
		$expectedModel->testArray[] = $obj;
		$expectedModel->testArray[] = 'testVal4';
		$expectedModel->testArray[] = 'testVal5';
		$nestedModel = new MapperModel();
		$nestedModel->testProperty1 = 'testVal1';
		$nestedModel->testProperty2 = 'testVal2';
		$nestedModel->testProperty3 = 'testVal3';
		$nestedModel->testArray[] = $obj;
		$nestedModel->testArray[] = 'testVal4';
		$nestedModel->testArray[] = 'testVal5';
		$nestedModel->testObjectArray = ['a'];
		$expectedModel->testObjectArray[] = $nestedModel;
		$expectedModel->testObjectArray[] = $nestedModel;
		$expectedModel->testObjectArray[] = $nestedModel;

		$obj = new stdClass();
		$obj->asd = 'asd';
		$expectedModelWithRoot = new MapperModelWithRoot();
		$expectedModelWithRoot->testProperty1 = 'testVal1';
		$expectedModelWithRoot->testProperty2 = 'testVal2';
		$expectedModelWithRoot->testProperty3 = 'testVal3';
		$expectedModelWithRoot->testArray[] = $obj;
		$expectedModelWithRoot->testArray[] = 'testVal4';
		$expectedModelWithRoot->testArray[] = 'testVal5';
		$nestedModelWithRoot = new MapperModelWithRoot();
		$nestedModelWithRoot->testProperty1 = 'testVal1';
		$nestedModelWithRoot->testProperty2 = 'testVal2';
		$nestedModelWithRoot->testProperty3 = 'testVal3';
		$nestedModelWithRoot->testArray[] = $obj;
		$nestedModelWithRoot->testArray[] = 'testVal4';
		$nestedModelWithRoot->testArray[] = 'testVal5';
		$nestedModelWithRoot->testObjectArray = ['a'];
		$expectedModelWithRoot->testObjectArray[] = $nestedModelWithRoot;
		$expectedModelWithRoot->testObjectArray[] = $nestedModelWithRoot;
		$expectedModelWithRoot->testObjectArray[] = $nestedModelWithRoot;

		$nestedJson = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"],"testObjectArray":["a"]}';
		$jsonInput = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"],"testObjectArray":['.$nestedJson.','.$nestedJson.','.$nestedJson.']}';

		$nestedJson = '{"my_fancy_root":{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"],"testObjectArray":["a"]}}';
		$jsonInputWithRoot = '{"my_fancy_root":{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"],"testObjectArray":['.$nestedJson.','.$nestedJson.','.$nestedJson.']}}';

		return [
			[$jsonInput, $expectedModel],
			[$jsonInputWithRoot, $expectedModelWithRoot]
		];
	}

	public function invalidValues() {
		return [
			['{}', new MapperModel()],
			['{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}', new MapperModel()],
			['{"unexpected_root":"{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}}', new MapperModelWithRoot()],
			['{"testProperty1":"testVal1","normalName":"testVal3","testArray":[{"asd":"asd"},"testVal4","testVal5"]}', new MapperModelWithRoot()],
			['{"testProperty1":"}', new MapperModel()]
		];
	}
}
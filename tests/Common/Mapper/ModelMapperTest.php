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
	public $modelMapper;

	public function setUp() {
		$this->modelMapper = new ModelMapper();
		parent::setUp();
	}

    /**
     * @param $source
     * @param $actualModel
     * @dataProvider validValues
     */
	public function testMap($source, $actualModel) {
		$expectedModel = $this->modelMapper->map($source, new TestModel());
        $this->assertEquals($expectedModel, $actualModel);
	}

    /**
     * @param $source
     * @param $model
     * @dataProvider invalidValues
     * @expectedException \Exception
     */
    public function testMapFail($source, $model) {
        $this->modelMapper->map($source, $model);
    }

//    public function testUnmap() {
//        $model = new MapperModel();
//        $model->testProperty1 = 'testVal1';
//        $model->testProperty2 = 'testVal2';
//        $model->testProperty3 = 'testVal3';
//        $model->testArray[] = new stdClass();
//        $model->testArray[] = 'testVal4';
//        $model->testArray[] = 'testVal5';
//
//        $json = '{"testProperty1":"testVal1","some?wierd-@ss::name":"testVal2","normalName":"testVal3","testArray":[{},"testVal4","testVal5"]}';
//        $preparedObject = $this->modelMapper->unmap($model);
//
//        $this->assertEquals($json, json_encode($preparedObject));
//    }

    public function invalidValues() {
        return [
            [null, new TestModel()],
            ['', new TestModel()],
            [1, new TestModel()],
            [false, new TestModel()],
            [array(), new TestModel()],
            [new stdClass(), new TestModel()],
            [new TestModel(), 1],
            [new TestModel(), new stdClass()]
        ];
    }

    public function validValues() {
        $nestedJson = '{"null":null,"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1},"model":null,"modelArray":[]}';
        $json = '{"null":null,"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1},"model":'.$nestedJson.',"modelArray":['.$nestedJson.','.$nestedJson.']}';
        $source = json_decode($json);

        $object = new stdClass();
        $object->a = 1;

        $model = new TestModel();
        $model->null = null;
        $model->boolTrue = true;
        $model->boolFalse = false;
        $model->string = 'a';
        $model->named = 'named';
        $model->integer = 5;
        $model->array = [1,'a',3];
        $model->stringArray = ['a','b','c'];
        $model->integerArray = [1,2,3];
        $model->booleanArray = [true,true,false];
        $model->objectArray = [$object,$object,$object];
        $model->object = $object;
        $nestedModel = clone $model;
        $model->model = $nestedModel;
        $model->modelArray = [$nestedModel,$nestedModel];

        return [
            [$source, $model]
        ];
    }
}
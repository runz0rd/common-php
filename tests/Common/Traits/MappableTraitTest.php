<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */

class MappableTraitTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var TestModel
     */
    public $model;

    /**
     * @var TestModel
     */
    public $expectedModel;

    public function setUp() {
        $this->model = new TestModel();

        $object = new stdClass();
        $object->a = 1;

        $this->expectedModel = new TestModel();
        $this->expectedModel->noType = null;
        $this->expectedModel->boolTrue = true;
        $this->expectedModel->boolFalse = false;
        $this->expectedModel->string = 'a';
        $this->expectedModel->namedString = 'named';
        $this->expectedModel->integer = 5;
        $this->expectedModel->array = [1,'a',3];
        $this->expectedModel->stringArray = ['a','b','c'];
        $this->expectedModel->integerArray = [1,2,3];
        $this->expectedModel->booleanArray = [true,true,false];
        $this->expectedModel->objectArray = [$object,$object,$object];
        $this->expectedModel->object = $object;
        $nestedModel = clone $this->expectedModel;
        $this->expectedModel->model = $nestedModel;
        $this->expectedModel->modelArray = [$nestedModel,$nestedModel];

        parent::setUp();
    }

    /**
	 * @dataProvider validArrays
	 */
	public function testMapFromArray($array) {
		$this->model->mapFromArray($array);
		$this->assertEquals($this->expectedModel, $this->model);
	}

	/**
	 * @dataProvider validJsons
	 */
	public function testMapFromJson($json) {
		$this->model->mapFromJson($json);
		$this->assertEquals($this->expectedModel, $this->model);
	}

	/**
	 * @dataProvider validObjects
	 */
	public function testMapFromObject($object) {
		$this->model->mapFromObject($object);
		$this->assertEquals($this->expectedModel, $this->model);
	}

	/**
	 * @dataProvider invalidArrays
	 * @expectedException Exception
	 */
	public function testMapFromArrayFail($array) {
		$this->model->mapFromArray($array);
	}

	/**
	 * @dataProvider invalidValues
	 * @expectedException Exception
	 */
	public function testMapFromJsonFail($json) {
		$this->model->mapFromJson($json);
	}

	/**
	 * @dataProvider invalidValues
	 * @expectedException Exception
	 */
	public function testMapFromObjectFail($object) {
		$this->model->mapFromObject($object);
	}

	public function validArrays() {
        $nestedJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1}}';
        $validJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1},"model":'.$nestedJson.',"modelArray":['.$nestedJson.','.$nestedJson.']}';
	    $validArray = json_decode($validJson);

		return [
			[$validArray]
		];
	}

    public function validJsons() {
        $nestedJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1}}';
        $validJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1},"model":'.$nestedJson.',"modelArray":['.$nestedJson.','.$nestedJson.']}';

        return [
            [$validJson]
        ];
    }

    public function validObjects() {
        $nestedJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1}}';
        $validJson = '{"boolTrue":true,"boolFalse":false,"string":"a","some?wierd-@ss::name":"named","integer":5,"array":[1,"a",3],"stringArray":["a","b","c"],"integerArray":[1,2,3],"booleanArray":[true,true,false],"objectArray":[{"a":1},{"a":1},{"a":1}],"object":{"a":1},"model":'.$nestedJson.',"modelArray":['.$nestedJson.','.$nestedJson.']}';
        $validObject = json_decode($validJson);

        return [
            [$validObject]
        ];
    }

    public function invalidArrays() {
        return [
            [[]],
            [[]]
        ];
    }

    public function invalidJsons() {
        return [
            [''],
            ['']
        ];
    }

    public function invalidObjects() {
        $invalidObject
        return [
            [$jsonInput, new MapperModel(), $expectedModel],
            [$jsonInputWithRoot, new MapperModelWithRoot(), $expectedModelWithRoot]
        ];
    }
}
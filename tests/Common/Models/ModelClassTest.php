<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/5/2016
 * Time: 9:13 AM
 */

use Common\Models\ModelClass;

class ModelClassTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ModelClass
     */
    public $modelClass;

    public function setUp() {
        $object = new stdClass();
        $object->a = 1;

        $model = new TestModel();
        $model->noType = null;
        $model->boolTrue = true;
        $model->boolFalse = false;
        $model->string = 'a';
        $model->namedString = 'named';
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

        $this->modelClass = new ModelClass($model);
        parent::setUp();
    }

    public function testGetClassName() {
        $expected = $this->modelClass->getClassName();
        $this->assertEquals($expected, 'TestModel');
    }

    public function testGetNamespace() {
        $expected = $this->modelClass->getNamespace();
        $this->assertEquals($expected, '');
    }

    public function testGetRootName() {
        $expected = $this->modelClass->getRootName();
        $this->assertEquals($expected, '');
    }

    /**
     * @param $invalidModel
     * @dataProvider invalidModels
     * @expectedException Exception
     */
    public function testConstructFail($invalidModel) {
        $modelClass = new ModelClass($invalidModel);
    }

    public function invalidModels() {
        return [
            [new stdClass()],
            [new DateTime()],
            [null],
            [false]
        ];
    }
}

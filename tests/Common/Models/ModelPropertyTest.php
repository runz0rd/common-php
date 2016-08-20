<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/5/2016
 * Time: 9:13 AM
 */

use Common\Models\ModelClass;
use Common\Models\ModelProperty;

class ModelPropertyTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ModelProperty[]
     */
    public $modelProperties;

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
        $model->requiredString = 'requiredString';
        $model->alwaysRequiredBoolean = false;
        $model->multipleRequiredInteger = 5;
        $nestedModel = clone $model;
        $model->model = $nestedModel;
        $model->modelArray = [$nestedModel,$nestedModel];

        $modelClass = new ModelClass($model);
        $this->modelProperties = $modelClass->getProperties();
        parent::setUp();
    }

    /**
     * @param $index
     * @param $value
     * @dataProvider validValues
     */
    public function testGetPropertyValue($index, $value) {
        $expected = $this->modelProperties[$index]->getPropertyValue();
        $this->assertEquals($expected, $value);
    }

    /**
     * @param $index
     * @dataProvider validValues
     */
    public function testSetPropertyValue($index) {
        $this->modelProperties[$index]->setPropertyValue('testValue');
        $expected = $this->modelProperties[$index]->getPropertyValue();
        $this->assertEquals($expected, 'testValue');
    }

    /**
     * @param $index
     * @param $value
     * @param $name
     * @dataProvider validValues
     */
    public function testGetName($index, $value, $name) {
        $expected = $this->modelProperties[$index]->getName();
        $this->assertEquals($expected, $name);
    }

    /**
     * @param $index
     * @dataProvider validValues
     */
    public function testGetClassName($index) {
        $expected = $this->modelProperties[$index]->getParentClassName();
        $this->assertEquals($expected, 'TestModel');
    }

    /**
     * @param $index
     * @param $value
     * @param $name
     * @param $propertyName
     * @dataProvider validValues
     */
    public function testGetPropertyName($index, $value, $name, $propertyName) {
        $expected = $this->modelProperties[$index]->getPropertyName();
        $this->assertEquals($expected, $propertyName);
    }

    /**
     * @param $index
     * @param $value
     * @param $name
     * @param $propertyName
     * @param $annotatedName
     * @dataProvider validValues
     */
    public function testGetAnnotatedName($index, $value, $name, $propertyName, $annotatedName) {
        $expected = $this->modelProperties[$index]->getAnnotatedName();
        $this->assertEquals($expected, $annotatedName);
    }

    /**
     * @param $index
     * @param $value
     * @param $name
     * @param $propertyName
     * @param $annotatedName
     * @param $isRequired
     * @dataProvider validValues
     */
    public function testIsRequired($index, $value, $name, $propertyName, $annotatedName, $isRequired) {
        $expected = $this->modelProperties[$index]->isRequired();
        $this->assertEquals($expected, $isRequired);
    }

    /**
     * @param $index
     * @param $value
     * @param $name
     * @param $propertyName
     * @param $annotatedName
     * @param $isRequired
     * @param $requiredTypes
     * @dataProvider validValues
     */
    public function testGetRequiredTypes($index, $value, $name, $propertyName, $annotatedName, $isRequired, $requiredTypes) {
        $expected = $this->modelProperties[$index]->getRequiredTypes();
        $this->assertEquals($expected, $requiredTypes);
    }

    public function validValues() {
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
        $model->requiredString = 'requiredString';
        $model->alwaysRequiredBoolean = false;
        $model->multipleRequiredInteger = 5;
        $nestedModel = clone $model;
        $model->model = $nestedModel;
        $model->modelArray = [$nestedModel,$nestedModel];

        return [
            [0, $model->noType, 'noType', 'noType', '', false, []],
            [1, $model->boolTrue, 'boolTrue', 'boolTrue', '', false, []],
            [2, $model->boolFalse, 'boolFalse', 'boolFalse', '', false, []],
            [3, $model->string, 'string', 'string', '', false, []],
            [4, $model->namedString, 'namedString123', 'namedString', 'namedString123', false, []],
            [5, $model->integer, 'integer', 'integer', '', false, []],
            [6, $model->array, 'array', 'array', '', false, []],
            [7, $model->stringArray, 'stringArray', 'stringArray', '', false, []],
            [8, $model->integerArray, 'integerArray', 'integerArray', '', false, []],
            [9, $model->booleanArray, 'booleanArray', 'booleanArray', '', false, []],
            [10, $model->objectArray, 'objectArray', 'objectArray', '', false, []],
            [11, $model->object, 'object', 'object', '', false, []],
            [12, $model->model, 'model', 'model', '', false, []],
            [13, $model->modelArray, 'modelArray', 'modelArray', '', false, []],
            [14, $model->requiredString, 'requiredString', 'requiredString', '', true, ['requiredString']],
            [15, $model->alwaysRequiredBoolean, 'alwaysRequiredBoolean', 'alwaysRequiredBoolean', '', true, ['']],
            [16, $model->multipleRequiredInteger, 'multipleRequiredInteger', 'multipleRequiredInteger', '', true, ['requiredInteger', 'testRequired']],
        ];
    }
}

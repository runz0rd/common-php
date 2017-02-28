<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/5/2016
 * Time: 9:13 AM
 */

use Common\ModelReflection\ModelClass;
use Common\ModelReflection\ModelProperty;

class ModelPropertyTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ModelProperty[]
     */
    public $modelProperties;

    public function setUp() {
        $object = new stdClass();
        $object->a = 1;

        $model = new \TestModel();
        $model->noType = null;
        $model->boolTrue = true;
        $model->boolFalse = false;
        $model->string = 'a';
        $model->namedString = 'named';
        $model->integer = 5;
        $model->array = array(1,'a',3);
        $model->stringArray = array('a','b','c');
        $model->integerArray = array(1,2,3);
        $model->booleanArray = array(true,true,false);
        $model->objectArray = array($object,$object,$object);
        $model->object = $object;
        $model->requiredString = 'requiredString';
        $model->alwaysRequiredBoolean = false;
        $model->multipleRequiredInteger = 5;

        $nestedModel = new \NestedTestModel();
        $nestedModel->noType = null;
        $nestedModel->boolTrue = true;
        $nestedModel->boolFalse = false;
        $nestedModel->string = 'a';
        $nestedModel->namedString = 'named';
        $nestedModel->integer = 5;
        $nestedModel->array = array(1,'a',3);
        $nestedModel->stringArray = array('a','b','c');
        $nestedModel->integerArray = array(1,2,3);
        $nestedModel->booleanArray = array(true,true,false);
        $nestedModel->objectArray = array($object,$object,$object);
        $nestedModel->object = $object;
        $nestedModel->requiredString = 'requiredString';
        $nestedModel->alwaysRequiredBoolean = false;
        $nestedModel->multipleRequiredInteger = 5;

        $model->model = $nestedModel;
        $model->modelArray = array($nestedModel,$nestedModel);

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
    public function testGetRequiredActions($index, $value, $name, $propertyName, $annotatedName, $isRequired, $requiredTypes) {
        $expected = $this->modelProperties[$index]->getRequiredActions();
        $this->assertEquals($expected, $requiredTypes);
    }

    public function validValues() {
        $object = new stdClass();
        $object->a = 1;

        $model = new \TestModel();
        $model->noType = null;
        $model->boolTrue = true;
        $model->boolFalse = false;
        $model->string = 'a';
        $model->namedString = 'named';
        $model->integer = 5;
        $model->array = array(1,'a',3);
        $model->stringArray = array('a','b','c');
        $model->integerArray = array(1,2,3);
        $model->booleanArray = array(true,true,false);
        $model->objectArray = array($object,$object,$object);
        $model->object = $object;
        $model->requiredString = 'requiredString';
        $model->alwaysRequiredBoolean = false;
        $model->multipleRequiredInteger = 5;

        $nestedModel = new \NestedTestModel();
        $nestedModel->noType = null;
        $nestedModel->boolTrue = true;
        $nestedModel->boolFalse = false;
        $nestedModel->string = 'a';
        $nestedModel->namedString = 'named';
        $nestedModel->integer = 5;
        $nestedModel->array = array(1,'a',3);
        $nestedModel->stringArray = array('a','b','c');
        $nestedModel->integerArray = array(1,2,3);
        $nestedModel->booleanArray = array(true,true,false);
        $nestedModel->objectArray = array($object,$object,$object);
        $nestedModel->object = $object;
        $nestedModel->requiredString = 'requiredString';
        $nestedModel->alwaysRequiredBoolean = false;
        $nestedModel->multipleRequiredInteger = 5;

        $model->model = $nestedModel;
        $model->modelArray = array($nestedModel,$nestedModel);

        return [
            array(0, $model->noType, 'noType', 'noType', '', false, array()),
            array(1, $model->boolTrue, 'boolTrue', 'boolTrue', '', false, array()),
            array(2, $model->boolFalse, 'boolFalse', 'boolFalse', '', false, array()),
            array(3, $model->string, 'string', 'string', '', false, array()),
            array(4, $model->namedString, 'namedString123', 'namedString', 'namedString123', false, array()),
            array(5, $model->integer, 'integer', 'integer', '', false, array()),
            array(6, $model->array, 'array', 'array', '', false, array()),
            array(7, $model->stringArray, 'stringArray', 'stringArray', '', false, array()),
            array(8, $model->integerArray, 'integerArray', 'integerArray', '', false, array()),
            array(9, $model->booleanArray, 'booleanArray', 'booleanArray', '', false, array()),
            array(10, $model->objectArray, 'objectArray', 'objectArray', '', false, array()),
            array(11, $model->object, 'object', 'object', '', false, array()),
            array(12, $model->model, 'model', 'model', '', false, array()),
            array(13, $model->modelArray, 'modelArray', 'modelArray', '', false, array()),
            array(14, $model->requiredString, 'requiredString', 'requiredString', '', true, array('requiredString', 'testRequired')),
            array(15, $model->alwaysRequiredBoolean, 'alwaysRequiredBoolean', 'alwaysRequiredBoolean', '', true, array('')),
            array(16, $model->multipleRequiredInteger, 'multipleRequiredInteger', 'multipleRequiredInteger', '', true, array('requiredInteger', 'testRequired')),
        ];
    }
}

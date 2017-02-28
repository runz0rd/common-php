<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/5/2016
 * Time: 9:13 AM
 */

use Common\ModelReflection\ModelClass;

class ModelClassTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ModelClass
     */
    public $modelClass;

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
        $this->assertEquals($expected, 'testModel');
    }

    /**
     * @param $invalidModel
     * @dataProvider invalidModels
     * @expectedException Exception
     */
    public function testConstructFail($invalidModel) {
        $modelClass = new ModelClass($invalidModel);
    }

    public function testInstantiate() {
        $expected = new \TestModel();
        $actual = ModelClass::instantiate('\TestModel');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Exception
     */
    public function testInstantiateFail() {
        $expected = new \TestModel();
        $actual = ModelClass::instantiate('Asd\TestModel');
    }

    public function invalidModels() {
        return array(
            array(new stdClass()),
            array(new DateTime()),
            array(null),
            array(false)
        );
    }
}

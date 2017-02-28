<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/5/2016
 * Time: 9:13 AM
 */

use Common\ModelReflection\ModelClass;
use Common\ModelReflection\ModelProperty;

class ModelPropertyTypeTest extends PHPUnit_Framework_TestCase {

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
     * @param $isModel
     * @dataProvider validValues
     */
    public function testIsModel($index, $isModel) {
        $expected = $this->modelProperties[$index]->getType()->isModel();
        $this->assertEquals($expected, $isModel);
    }

    /**
     * @param $index
     * @param $isModel
     * @param $propertyType
     * @dataProvider validValues
     */
    public function testGetPropertyType($index, $isModel, $propertyType) {
        $expected = $this->modelProperties[$index]->getType()->getPropertyType();
        $this->assertEquals($expected, $propertyType);
    }

    /**
     * @param $index
     * @param $isModel
     * @param $propertyType
     * @param $annotatedType
     * @dataProvider validValues
     */
    public function testGetAnnotatedType($index, $isModel, $propertyType, $annotatedType) {
        $expected = $this->modelProperties[$index]->getType()->getAnnotatedType();
        $this->assertEquals($expected, $annotatedType);
    }

    /**
     * @param $index
     * @param $isModel
     * @param $propertyType
     * @param $annotatedType
     * @param $actualType
     * @dataProvider validValues
     */
    public function testGetActualType($index, $isModel, $propertyType, $annotatedType, $actualType) {
        $expected = $this->modelProperties[$index]->getType()->getActualType();
        $this->assertEquals($expected, $actualType);
    }

    /**
     * @param $index
     * @param $isModel
     * @param $propertyType
     * @param $annotatedType
     * @param $actualType
     * @param $modelClassName
     * @dataProvider validValues
     */
    public function testGetModelClassName($index, $isModel, $propertyType, $annotatedType, $actualType, $modelClassName) {
        if($isModel) {
            $expected = $this->modelProperties[$index]->getType()->getModelClassName();
            $this->assertEquals($expected, $modelClassName);
        }
    }

    public function validValues() {
        return [
            array(0, false, 'NULL', 'any', 'any', ''),
            array(1, false, 'boolean', 'boolean', 'boolean', ''),
            array(2, false, 'boolean', 'boolean', 'boolean', ''),
            array(3, false, 'string', 'string', 'string', ''),
            array(4, false, 'string', 'string', 'string', ''),
            array(5, false, 'integer', 'integer', 'integer', ''),
            array(6, false, 'array', 'array', 'array', ''),
            array(7, false, 'array', 'string[]', 'array', ''),
            array(8, false, 'array', 'integer[]', 'array', ''),
            array(9, false, 'array', 'boolean[]', 'array', ''),
            array(10, false, 'array', 'object[]', 'array', ''),
            array(11, false, 'object', 'object', 'object', ''),
            array(12, true, 'object', 'NestedTestModel', 'object', '\NestedTestModel'),
            array(13, true, 'array', 'NestedTestModel[]', 'array', '\NestedTestModel'),
            array(14, false, 'string', 'string', 'string', ''),
            array(15, false, 'boolean', 'boolean', 'boolean', ''),
            array(16, false, 'integer', 'integer', 'integer', '')
        ];
    }
}

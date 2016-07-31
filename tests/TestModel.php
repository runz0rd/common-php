<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/31/2016
 * Time: 9:49 AM
 */

class TestModel {

    /**
     * @var
     */
    public $null;

    /**
     * @var boolean
     */
    public $boolTrue;

    /**
     * @var boolean
     */
    public $boolFalse;

    /**
     * @var string
     */
    public $string;

    /**
     * @name some?wierd-@ss::name
     * @var string
     */
    public $named;

    /**
     * @var integer
     */
    public $integer;

    /**
     * @var array
     */
    public $array;

    /**
     * @var string[]
     */
    public $stringArray;

    /**
     * @var integer[]
     */
    public $integerArray;

    /**
     * @var boolean[]
     */
    public $booleanArray;

    /**
     * @var object[]
     */
    public $objectArray;

    /**
     * @var object
     */
    public $object;

    /**
     * @var TestModel
     */
    public $model;

    /**
     * @var TestModel[]
     */
    public $modelArray;
}
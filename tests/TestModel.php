<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 7/31/2016
 * Time: 9:49 AM
 */
use Common\Traits\MappableTrait;
use Common\Traits\ConvertibleTrait;
use Common\Traits\ValidatableTrait;

/**
 * @root testModel
 * Class TestModel
 */
class TestModel {
    use MappableTrait;
    use ConvertibleTrait;
    use ValidatableTrait;

    /**
     * @attribute
     * @var string
     */
    public $attribute1;

    /**
     * @var
     */
    public $noType;

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
     * @name namedString123
     * @var string
     */
    public $namedString;

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

    /**
     * @required requiredString
     * @var string
     */
    public $requiredString;

    /**
     * @required
     * @var boolean
     */
    public $alwaysRequiredBoolean;

    /**
     * @required requiredInteger
     * @required testRequired
     * @var integer
     */
    public $multipleRequiredInteger;
}
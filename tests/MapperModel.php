<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:27 PM
 */

/**
 * Class MapperModel
 */
class MapperModel {
	use \Common\Traits\MappableTrait;
	use \Common\Traits\ConvertibleTrait;
	use \Common\Traits\ValidatableTrait;

	/**
	 * @required
	 * @var string
	 */
	public $testProperty1;

	/**
	 * @required create
	 * @required update
	 * @name some?wierd-@ss::name
	 * @var string
	 */
	public $testProperty2;

	/**
	 * @required read
	 * @required delete
	 * @name normalName
	 * @var string
	 */
	public $testProperty3;

	/**
	 * @var array
	 */
	public $testArray;

	/**
	 * @var MapperModel[]
	 */
	public $testObjectArray;

}
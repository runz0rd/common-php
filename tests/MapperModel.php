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

	/**
	 * @required
	 * @var string
	 */
	public $testProperty1;

	/**
	 * @required
	 * @name some?wierd-@ss::name
	 * @var string
	 */
	public $testProperty2;

	/**
	 * @required
	 * @name normalName
	 * @var string
	 */
	public $testProperty3;

	/**
	 * @required
	 * @var array
	 */
	public $testArray;

	/**
	 * @required
	 * @var MapperModel[]
	 */
	public $testObjectArray;

}
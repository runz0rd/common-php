<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:27 PM
 */

/**
 * Class MapperModelWithRoot
 * @root my_fancy_root
 */
class MapperModelWithRoot {
	use \Common\Traits\MappableTrait;
	use \Common\Traits\ConvertibleTrait;
	use \Common\Traits\ValidatableTrait;

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
	 * @var MapperModelWithRoot[]
	 */
	public $testObjectArray;

}
<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 4:14 PM
 */

namespace Common\Mapper;


class ValidationOptions {

	public $types;
	public $required;

	/**
	 * ValidationOptions constructor.
	 * @param bool $types
	 * @param bool $required
	 */
	public function __construct(bool $types, bool $required) {
		$this->types = $types;
		$this->required = $required;
	}
}
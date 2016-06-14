<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/31/2016
 * Time: 2:42 PM
 */

namespace Common\Traits;

use Common\Validator\ObjectValidator;

trait ValidatableTrait {

	/**
	 * @param string $validationType
	 */
	public function validate(string $validationType = '') {
		$validator = new ObjectValidator();
		$validator->validate($this, $validationType);
	}
}
<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/1/2016
 * Time: 11:23 PM
 */

class ValidatableTraitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider validValues
	 */
	public function testValidate($model, $validationType) {
		/** @var MapperModel|MapperModelWithRoot $model */
		$model->validate($validationType);
	}

	/**
	 * @dataProvider invalidValues
	 * @expectedException Exception
	 */
	public function testValidateFail($model, $validationType) {
		/** @var MapperModel|MapperModelWithRoot $model */
		$model->validate($validationType);
	}

	public function validValues() {
		$model = new MapperModel();
		$model->testProperty1 = 'testVal1';
		$model->testProperty2 = 'testVal2';
		$model->testProperty3 = 'testVal3';

		return [
			[$model, 'create'],
			[$model, 'update'],
			[$model, 'read'],
			[$model, 'delete'],
			[$model, '']
		];
	}

	public function invalidValues() {
		$model = new MapperModel();
		$model->testProperty2 = new stdClass();
		$model->testArray = ["asd"];

		return [
			[$model, 'create'],
			[$model, 'update'],
			[$model, 'read'],
			[$model, 'delete'],
			[$model, '']
		];
	}
}
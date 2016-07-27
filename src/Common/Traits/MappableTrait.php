<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/31/2016
 * Time: 2:42 PM
 */

namespace Common\Traits;
use Common\Mapper\ModelMapperException;
use Common\Mapper\ModelMapper;
use Common\Util\Validation;

trait MappableTrait {

	/**
	 * @param array $data
	 * @throws \InvalidArgumentException
	 * @throws ModelMapperException
	 */
	public function mapFromArray(array $data) {
		$json = json_encode($data);
		$object = json_decode($json);

		$this->mapFromObject($object);
	}

	/**
	 * @param string $data
	 * @throws \InvalidArgumentException
	 * @throws ModelMapperException
	 */
	public function mapFromJson(string $data) {
		$object = json_decode($data);

		$this->mapFromObject($object);
	}

	/**
	 * @param $object
	 * @throws ModelMapperException
	 */
	public function mapFromObject($object) {
		if(Validation::isEmpty((array) $object)) {
			throw new \InvalidArgumentException('Invalid json string supplied.');
		}
		$mapper = new ModelMapper();
		$mapper->map($object, $this);
	}
}
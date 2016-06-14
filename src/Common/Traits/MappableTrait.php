<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/31/2016
 * Time: 2:42 PM
 */

namespace Common\Traits;
use Common\Mapper\ObjectMapperException;
use Common\Mapper\ObjectMapper;

trait MappableTrait {

	/**
	 * @param array $data
	 * @throws \InvalidArgumentException
	 * @throws ObjectMapperException
	 */
	public function mapFromArray(array $data) {
		$json = json_encode($data);
		$object = json_decode($json);

		if(empty($object)) {
			throw new \InvalidArgumentException('Invalid array supplied.');
		}

		$this->mapFromObject($object);
	}

	/**
	 * @param string $data
	 * @throws \InvalidArgumentException
	 * @throws ObjectMapperException
	 */
	public function mapFromJson(string $data) {
		$object = json_decode($data);

		if(empty($object)) {
			throw new \InvalidArgumentException('Invalid json string supplied.');
		}

		$this->mapFromObject($object);
	}

	/**
	 * @param $object
	 * @throws ObjectMapperException
	 */
	public function mapFromObject($object) {
		$mapper = new ObjectMapper();
		$mapper->map($object, $this);
	}
}
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
	 * @param bool $useRoot
	 * @throws \InvalidArgumentException
	 * @throws ObjectMapperException
	 */
	public function mapFromArray(array $data, bool $useRoot = true) {
		$json = json_encode($data);
		$object = json_decode($json);

		if(empty($object)) {
			throw new \InvalidArgumentException('Invalid array supplied.');
		}

		$this->mapFromObject($object, $useRoot);
	}

	/**
	 * @param string $data
	 * @param bool $useRoot
	 * @throws \InvalidArgumentException
	 * @throws ObjectMapperException
	 */
	public function mapFromJson(string $data, bool $useRoot = true) {
		$object = json_decode($data);

		if(empty($object)) {
			throw new \InvalidArgumentException('Invalid json string supplied.');
		}

		$this->mapFromObject($object, $useRoot);
	}

	/**
	 * @param $object
	 * @param bool $useRoot
	 * @throws ObjectMapperException
	 */
	public function mapFromObject($object, bool $useRoot = true) {
		$mapper = new ObjectMapper();
		$mapper->map($object, $this, $useRoot);
	}
}
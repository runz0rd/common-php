<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/31/2016
 * Time: 2:42 PM
 */

namespace Common\Traits;

use Common\Mapper\ObjectMapper;
use Common\Mapper\MapperValidationException;

trait MappableTrait {

	/**
	 * @param array $data
	 * @param bool $validate
	 * @throws \InvalidArgumentException
	 * @throws MapperValidationException
	 */
	public function mapFromArray(array $data, bool $validate = false) {
		$object = json_decode(json_encode($data));

		if(is_null($object)) {
			throw new \InvalidArgumentException('Invalid array supplied.');
		}

		$this->mapFromObject($object, $validate);
	}

	/**
	 * @param string $data
	 * @param bool $validate
	 * @throws \InvalidArgumentException
	 * @throws MapperValidationException
	 */
	public function mapFromJson(string $data, bool $validate = false) {
		$object = json_decode($data);

		if(is_null($object)) {
			throw new \InvalidArgumentException('Invalid json string supplied.');
		}

		$this->mapFromObject($object, $validate);
	}

	/**
	 * @param object $object
	 * @param bool $validate
	 * @throws \InvalidArgumentException
	 * @throws MapperValidationException
	 */
	public function mapFromObject($object, bool $validate = false) {
		$mapper = new ObjectMapper();
		$mapper->bExceptionOnMissingData = $validate;
		$mapper->map($object, $this);
	}
}
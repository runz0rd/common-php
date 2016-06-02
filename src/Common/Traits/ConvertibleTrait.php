<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/31/2016
 * Time: 2:42 PM
 */

namespace Common\Traits;
use Common\Mapper\ObjectMapper;

trait ConvertibleTrait {

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function toArray() {
		$preparedJson = $this->toJson();
		$preparedArray = json_decode($preparedJson, true);

		if(is_null($preparedArray)) {
			throw new \InvalidArgumentException('Cannot convert to array.');
		}

		return $preparedArray;
	}

	/**
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function toJson() {
		$mapper = new ObjectMapper();
		$preparedObject = $mapper->prepare($this);
		$preparedJson = json_encode($preparedObject);

		if(!$preparedJson) {
			throw new \InvalidArgumentException('Cannot convert to json.');
		}

		return $preparedJson;
	}
}
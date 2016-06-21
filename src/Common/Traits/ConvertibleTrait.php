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
	 * @param bool $useRoot
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function toArray(bool $useRoot = true) {
		$json = $this->toJson($useRoot);
		$array = json_decode($json, true);

		if(is_null($array)) {
			throw new \InvalidArgumentException('Cannot convert to array.');
		}

		return $array;
	}

	/**
	 * @param bool $useRoot
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function toJson(bool $useRoot = true) {
		$mapper = new ObjectMapper();
		$unmappedObject = $mapper->unmap($this, $useRoot);
		$json = json_encode($unmappedObject);

		if(!$json) {
			throw new \InvalidArgumentException('Cannot convert to json.');
		}

		return $json;
	}
}
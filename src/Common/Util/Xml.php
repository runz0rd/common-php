<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 8/12/2016
 * Time: 1:48 PM
 */

namespace Common\Util;

class Xml {

	/**
	 * @param string $name
	 * @return bool
	 */
	public static function isValidElementName(string $name) {
		try {
			new \DOMElement($name, null);
			$result = true;
		} catch(\DOMException $e) {
			$result = false;
		}

		return $result;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	public static function loadFromFile(string $filename) {
		$contents = file_get_contents($filename);
		$contents = str_replace("\n", '', $contents);
		$contents = str_replace("\r", '', $contents);
		$xml = str_replace('    ', '', $contents);

		return $xml;
	}
}
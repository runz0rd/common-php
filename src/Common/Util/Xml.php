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

    /**
     * Checks if a key is used more than once in DOMNode children
     * @param \DOMNode $parentElement
     * @param string $key
     * @return bool
     */
    public static function isDomNodeArray(\DOMNode $parentElement, string $key) {
        $result = false;
        $keyCount = 0;
        for($i = 0; $i < $parentElement->childNodes->length; $i++) {
            $nodeName = $parentElement->childNodes->item($i)->nodeName;
            if($nodeName == $key) {
                $keyCount++;
            }
        }
        if($keyCount > 1) {
            $result = true;
        }

        return $result;
    }

	/**
	 * Removes extra spaces, tabs and line breaks between tags
	 * @param string $xml
	 * @return string
	 */
	public static function removeWhitespace(string $xml) {
		return preg_replace('/>\s*</', '><', $xml);
	}
}
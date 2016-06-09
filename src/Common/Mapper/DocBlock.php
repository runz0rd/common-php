<?php
/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 6/7/2016
 * Time: 3:56 PM
 */

namespace Common\Mapper;


class DocBlock {

	/**
	 * @var array
	 */
	private $annotations;

	/**
	 * DocBlock constructor.
	 * Copied from PHPUnit 3.7.29 Util/Test.php
	 * @param string $docBlock
	 */
	public function __construct(string $docBlock) {
		$annotations = array();
		// Strip away the docblock header and footer
		// to ease parsing of one line annotations
		$docBlock = substr($docBlock, 3, -2);

		$re = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';
		if (preg_match_all($re, $docBlock, $matches)) {
			$numMatches = count($matches[0]);

			for ($i = 0; $i < $numMatches; ++$i) {
				$annotations[$matches['name'][$i]][] = $matches['value'][$i];
			}
		}

		$this->annotations = $annotations;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function annotationExists($name) {
		$result = true;
		if(!isset($this->annotations[$name][0])) {
			$result = false;
		}

		return $result;
	}

	/**
	 * @param $name
	 * @param int $index
	 * @return string
	 */
	public function getAnnotation($name, $index = 0) {
		if(!isset($this->annotations[$name][$index])) {
			throw new \InvalidArgumentException('Annotation ' . $name . ' index ' . $index . ' not found in docBlock.');
		}

		return $this->annotations[$name][$index];
	}

	/**
	 * @return array
	 */
	public function getAnnotations() {
		return $this->annotations;
	}
}
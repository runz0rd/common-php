<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 9/6/2016
 * Time: 8:48 PM
 */
class DirectoryTest extends PHPUnit_Framework_TestCase {

    public function testScan() {
        $expected = [
            __DIR__.DIRECTORY_SEPARATOR.'testDir'.DIRECTORY_SEPARATOR.'new'.DIRECTORY_SEPARATOR.'testFile1.asd',
            __DIR__.DIRECTORY_SEPARATOR.'testDir'.DIRECTORY_SEPARATOR.'new'.DIRECTORY_SEPARATOR.'testFile2.asd',
            __DIR__.DIRECTORY_SEPARATOR.'testDir'.DIRECTORY_SEPARATOR.'testFile1.asd',
            __DIR__.DIRECTORY_SEPARATOR.'testDir'.DIRECTORY_SEPARATOR.'testFile2.asd',
        ];
        $actual = \Common\Util\Directory::scan(__DIR__.DIRECTORY_SEPARATOR.'testDir', 'asd');
        $this->assertEquals($expected, $actual);
    }
}

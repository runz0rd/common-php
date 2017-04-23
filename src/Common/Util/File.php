<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 5/3/2016
 * Time: 12:01 AM
 */
namespace Common\Util;

class File {

    /**
     * @param $filePath
     * @param $filename
     * @param $data
     */
    public static function write($filePath, $filename, $data) {
        if(!is_dir($filePath)) {
            Directory::make($filePath, 0755);
        }
        $fh = fopen($filePath . $filename, "w") or die("Unable to open file!");
        fwrite($fh, $data);
        fclose($fh);
    }

    /**
     * @param string $filename
     * @param Closure $processContent
     * @return string
     */
    public static function read($filename, $processContent) {
        $content = file_get_contents($filename);
        return processContent(file_get_contents($filename));
    }
}
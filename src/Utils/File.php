<?php


namespace App\Utils;


use Exception;

class File
{
    /**
     * @param string $base64
     * @return string
     * @throws Exception
     */
    public static function b64ToFile(string $base64): string {

        if(!($path = tempnam(sys_get_temp_dir(), 'pdffiller'))) {
            throw new Exception('Unable to create a tempfile');
        }

        list(, $data) = explode(',', $base64);

        if(!file_put_contents($path, base64_decode($base64), LOCK_EX)) {
            throw new Exception('Unable to write data:' . $path);
        }

        return $path;
    }



}
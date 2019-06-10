<?php


namespace App\Utils;


use Exception;

class File
{

    private $path;

    /**
     * File constructor.
     * @param string $pdf base64 or url
     * @throws Exception
     */
    public function __construct(string $pdf)
    {
        if(!($this->path = tempnam(sys_get_temp_dir(), 'pdffiller'))) {
            throw new Exception('Unable to create a tempfile');
        }

        if (filter_var($pdf, FILTER_VALIDATE_URL)) {
            $this->urlToFile($pdf);
        } else {
            $this->b64ToFile($pdf);
        }

    }

    /**
     * @param string $base64
     * @return string
     * @throws Exception
     */
    public function b64ToFile(string $base64): string
    {

        if(!file_put_contents($this->path, base64_decode($base64), LOCK_EX)) {
            throw new Exception('Unable to write data:' . $this->path);
        }

        return $this->path;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function urlToFile(string $url): string
    {
        if(!copy($url, $this->path)) {
            throw new Exception('Unable to write data:' . $this->path);
        }

        return $this->path;
    }

    /**
     * @return bool|string
     */
    public function getPath()
    {
        return $this->path;
    }



    /**
     * Clean the temp file
     */
    public function clear()
    {
        unlink($this->path);
    }


}
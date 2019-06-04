<?php


namespace App\Contracts;


interface IPdfProvider
{

    /**
     * @param string $path
     * @param array $fields
     * @return string
     */
    public function fillPdf(string $path, array $fields) : string;

    /**
     * @param string $path
     * @return array
     */
    public function listFields(string $path): array;

}
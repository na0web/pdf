<?php


namespace App\Placeholders;


use App\Contracts\Placeholder;

final class Curl extends Placeholder
{

    protected $name = 'curl';

    protected $arguments_name = [
        'url'
    ];


    protected function validate(array $args): bool
    {
        return filter_var($args['url'], FILTER_VALIDATE_URL);
    }

    /**
     * @param array $args
     * @return string
     */
    protected function handle(?array $args): string
    {
        return file_get_contents($args['url']);
    }
}
<?php


namespace App\Placeholders;


use App\Contracts\Placeholder;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

final class CurrentDate extends Placeholder
{

    protected $name = 'placeholder_currentdate';

    protected $arguments_name = [
        'timezone', 'format'
    ];

    protected function validate(array $args): bool
    {
        try {
            if (isset($args['timezone'])) {
                new CarbonTimeZone($args['timezone']);
            }
            if (isset($args['format'])) {
                (new Carbon())->format($args['format']);
            }
        } catch (\Exception $_) {
            return false;
        }
        return true;
    }

    /**
     * @param array $args
     * @return string
     * @throws \Exception
     */
    function handle(?array $args): string
    {
        $carbon = new Carbon();

        if (isset($args['timezone'])) {
            $carbon->setTimezone(new CarbonTimeZone($args['timezone']));
        }

        if (isset($args['format'])) {
            return $carbon->format($args['format']);
        } else {
            return $carbon->format(Carbon::RFC3339);
        }


    }
}
<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

final class Datas
{
    public static function addMonths($date, string $months): Carbon
    {
        $init = clone $date;
        $modifier = $months . ' months';
        $back_modifier = -$months . ' months';

        $date->modify($modifier);
        $back_to_init = clone $date;
        $back_to_init->modify($back_modifier);

        while ($init->format('m') !== $back_to_init->format('m')) {
            $date->modify('-1 day');
            $back_to_init = clone $date;
            $back_to_init->modify($back_modifier);
        }

        return $date;
    }
}

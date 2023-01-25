<?php

use Carbon\Carbon;

if (! function_exists('clamp')) {
    /**
     * Clamp a date to a given interval.
     * Defaults to rounding up to the nearest 15 minutes.
     *
     * @param \Carbon\Carbon $date The date in question to round.
     * @return \Carbon\Carbon
     */
    function clamp(Carbon $date): Carbon
    {
        $offset = 5;
        $interval = 15;

        if ($date->minute % $interval === 0) {
            return $date->second(0);
        }

        $date->subMinutes($interval + $offset);
        $date->addMinutes($interval - ($date->minute % $interval));

        return $date->second(0);
    }
}

<?php

namespace App\Contracts;

interface Distance
{
    /**
     * Get the amount for the mileage (km/miles).
     *
     * @return int
     */
    public function value(): int;
}

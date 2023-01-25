<?php

namespace App\Contracts;

interface Result
{
    /**
     * Get the amount for this result, before tax.
     *
     * @return int
     */
    public function value(): int;

    /**
     * Get an object representing the price
     * of the distance we've travelled.
     *
     * @return \App\Contracts\Distance
     */
    public function distance(): Distance;
}

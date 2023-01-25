<?php

namespace App\Rates;

use App\Contracts\Distance as DistanceContract;
use App\Contracts\Result as ResultContract;

class Result implements ResultContract
{
    /**
     * The value of this trip, as an integer (base currency, e.g. pennies).
     *
     * @var int
     */
    protected $value;
    /**
     * Get an object representing distance.
     *
     * @var \App\Contracts\Distance
     */
    protected $distance;

    /**
     * Create a new Result.
     *
     * @param int $value
     * @param \App\Contracts\Distance $distance
     */
    public function __construct(int $value, DistanceContract $distance)
    {
        $this->value = $value;
        $this->distance = $distance;
    }

    /**
     * Get the amount for this result, before tax.
     *
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Get an object representing the price
     * of the distance we've travelled.
     *
     * @return \App\Contracts\Distance
     */
    public function distance(): DistanceContract
    {
        return $this->distance;
    }
}

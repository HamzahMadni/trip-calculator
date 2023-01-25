<?php

namespace App\Rates;

use App\Contracts\Distance as DistanceContract;

class Distance implements DistanceContract
{
    /**
     * The value of this trip, as an integer (base currency, e.g. pennies).
     *
     * @var int
     */
    protected $value;

    /**
     * Create a new Result.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
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
}

<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class Calculator implements CalculatorContract
{
    /**
     * Calculate our rates.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @param int $distance
     *
     * @return \App\Contracts\Result
     */
    public function calculate(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        return new Result(0, new Distance(0));
    }
}

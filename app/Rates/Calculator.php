<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Collection;

class Calculator implements CalculatorContract
{
    protected Collection $rates;

    public function __construct(Collection $rates)
    {
        $this->rates = $rates;
    }

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
        echo "\nNEW DATA SET\n\n\n\n\n";
        $timeCost = 0;
        $distanceCost = 0;
        $fifteenMinuteChunks = collect(new DatePeriod($start, new DateInterval('PT15M'), $end));

        $fifteenMinuteChunks->each(function ($chunk) use (&$timeCost) {
            echo "\n-------------------------\n";
            echo "\nCURRENT TIME: {$chunk->toTimeString()}\n";
            echo "\nTIME COST: {$timeCost}\n";
            if ($chunk->isWeekday()) {
                $this->rates
                    ->filter(fn ($rate) => !$rate->is_weekend && $rate->rate_type === 'time')
                    ->each(function ($rate) use ($chunk, &$timeCost) {
                        if ($chunk->hour >= $rate->start_hour && $chunk->hour < 24 || $chunk->hour <= $rate->end_hour) {
                            echo "\nRATE BEING ADDED: {$rate->default_rate}\n";
                            $timeCost  = $timeCost  + $rate->default_rate;
                            echo "\nTIME COST AFTER: {$timeCost}\n";
                        }
                    });
            } else {
                $this->rates
                    ->filter(fn ($rate) => $rate->is_weekend && $rate->rate_type === 'time')
                    ->each(function ($rate) use ($chunk, &$timeCost) {
                        if ($chunk->hour >= $rate->start_hour && $chunk->hour < 24 || $chunk->hour <= $rate->end_hour) {
                            echo "\nRATE BEING ADDED: {$rate->default_rate}\n";
                            $timeCost  = $timeCost  + $rate->default_rate;
                            echo "\nTIME COST AFTER: {$timeCost}\n";
                        }
                    });
            }
        });

        return new Result($timeCost, new Distance(0));
    }
}

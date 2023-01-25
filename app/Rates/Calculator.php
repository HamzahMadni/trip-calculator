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
        $minutesElapsed = 0;
        $twentyFourHourTotal = 0;
        $twentyFourHourTotals = collect();
        $fifteenMinuteChunks = collect(new DatePeriod($start, new DateInterval('PT15M'), $end));

        $fifteenMinuteChunks->each(function ($chunk) use (&$timeCost, &$minutesElapsed, &$twentyFourHourTotal, &$twentyFourHourTotals, $start, $end) {
            $minutesElapsed += 15;
            echo "\n-------------------------\n";
            echo "\nCURRENT TIME: {$chunk->toTimeString()}\n";
            echo "\nTIME COST: {$timeCost}\n";
            if ($chunk->isWeekday()) {
                $this->rates
                    ->filter(fn ($rate) => !$rate->is_weekend && $rate->rate_type === 'time')
                    ->each(function ($rate) use ($chunk, &$timeCost, &$twentyFourHourTotal) {
                        if ($chunk->hour >= $rate->start_hour && $chunk->hour < 24 || $chunk->hour <= $rate->end_hour) {
                            echo "\nRATE BEING ADDED: {$rate->default_rate}\n";
                            $timeCost = $timeCost  + $rate->default_rate;
                            $twentyFourHourTotal += $rate->default_rate;
                            echo "\nTIME COST AFTER: {$timeCost}\n";
                        }
                    });
            } else {
                $this->rates
                    ->filter(fn ($rate) => $rate->is_weekend && $rate->rate_type === 'time')
                    ->each(function ($rate) use ($chunk, &$timeCost, &$twentyFourHourTotal) {
                        if ($chunk->hour >= $rate->start_hour && $chunk->hour < 24 || $chunk->hour <= $rate->end_hour) {
                            echo "\nRATE BEING ADDED: {$rate->default_rate}\n";
                            $timeCost = $timeCost  + $rate->default_rate;
                            $twentyFourHourTotal += $rate->default_rate;
                            echo "\nTIME COST AFTER: {$timeCost}\n";
                        }
                    });
            }

            if ($minutesElapsed === min($start->diffInMinutes($end), 1440)) {
                echo "\nMINUTES ELAPSED: {$minutesElapsed}\n";
                $twentyFourHourTotals->push($twentyFourHourTotal);
                $twentyFourHourTotal = 0;
                $minutesElapsed = 0;
            }
        });

        $dailyMaxRate = $this->rates->filter(fn ($rate) => $rate->daily_max)->first();
        if ($dailyMaxRate) {
            echo "\nAPPLYING DAILY MAX\n";
            $totalOverChargedAmount = 0;
            $twentyFourHourTotals->each(function ($total) use (&$totalOverChargedAmount, $dailyMaxRate) {
                if ($total > $dailyMaxRate->default_rate) {
                    $totalOverChargedAmount += $total - $dailyMaxRate->default_rate;
                }
            });

            echo "\nTIME COST BEFORE: {$timeCost}\n";
            $timeCost -= $totalOverChargedAmount;
            echo "\nTIME COST AFTER: {$timeCost}\n";
        }

        $distanceRate = $this->rates->filter(fn ($rate) => $rate->rate_type === 'distance')->first();
        if (isset($distanceRate->special_rate)) {
            $distanceToBeChargedAtDefaultRate = $distance - $distanceRate->special_rate_limit;

            $distanceCost = $distanceToBeChargedAtDefaultRate > 0
                ? $distanceRate->special_rate * $distanceRate->special_rate_limit + $distanceRate->default_rate * $distanceToBeChargedAtDefaultRate
                : $distanceRate->special_rate * $distance;
        }

        return new Result($timeCost, new Distance($distanceCost));
    }
}

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
        $timeCost = $this->calculateTimeCost($start, $end);
        $distanceCost = $this->calculateDistanceCost($distance);
        
        return new Result($timeCost, new Distance($distanceCost));
    }

    protected function calculateTimeCost(Carbon $start, Carbon $end): int
    {
        echo "\nNEW DATA SET\n\n\n\n\n";
        $timeCost = 0;
        $minutesElapsed = 0;
        $nonDailyMaxRate = $this->rates
            ->filter(fn ($rate) => $rate->rate_type === 'max' && !$rate->daily_max)
            ->first();
        $minutesElapsedDuringNonDailyMaxRate = 0;
        $nonDailyMaxRateTotal = 0;
        $nonDailyMaxRateTotals = collect();
        $twentyFourHourTotal = 0;
        $twentyFourHourTotals = collect();
        $fifteenMinuteChunks = collect(new DatePeriod($start, new DateInterval('PT15M'), $end));

        $fifteenMinuteChunks->each(function ($chunk) use (&$timeCost, &$minutesElapsed, &$minutesElapsedDuringNonDailyMaxRate, &$twentyFourHourTotal, &$twentyFourHourTotals, $start, $end, $nonDailyMaxRate, &$nonDailyMaxRateTotal, $nonDailyMaxRateTotals) {
            $minutesElapsed += 15;
            echo "\n-------------------------\n";
            echo "\nCURRENT TIME: {$chunk->toTimeString()}\n";
            echo "\nTIME COST: {$timeCost}\n";
            $this->rates
                ->filter(fn ($rate) => $rate->rate_type === 'time')
                ->each(function ($rate) use ($chunk, &$timeCost, &$twentyFourHourTotal, $nonDailyMaxRate, &$nonDailyMaxRateTotal) {
                    $chunkIsWithinRateStartAndEnd = $rate->start_hour < $rate->end_hour
                            ? ($chunk->hour >= $rate->start_hour && $chunk->hour < $rate->end_hour)
                            : ($chunk->hour >= $rate->start_hour || $chunk->hour < $rate->end_hour);
                    $chunkIsSameDayType = $chunk->isWeekend() === $rate->is_weekend;

                    if ($chunkIsSameDayType && $chunkIsWithinRateStartAndEnd) {
                        echo "\nRATE BEING ADDED: {$rate->default_rate}\n";
                        $timeCost = $timeCost  + $rate->default_rate;
                        echo "\nTIME COST AFTER: {$timeCost}\n";
                        $twentyFourHourTotal += $rate->default_rate;

                        $chunkIsWithinNonDailyMaxRateStartAndEnd = isset($nonDailyMaxRate)
                            && (
                                $chunk->isWeekend() === $nonDailyMaxRate->is_weekend
                                && $chunk->hour >= $nonDailyMaxRate->start_hour
                                && $chunk->hour < $nonDailyMaxRate->end_hour
                            );

                        if ($chunkIsWithinNonDailyMaxRateStartAndEnd) {
                            $nonDailyMaxRateTotal += $rate->default_rate;
                        }
                    }
                });

            if ($minutesElapsed === min($start->diffInMinutes($end), 1440)) {
                echo "\nMINUTES ELAPSED: {$minutesElapsed}\n";
                $twentyFourHourTotals->push($twentyFourHourTotal);
                $twentyFourHourTotal = 0;
                $minutesElapsed = 0;
            }

            $tripHasEndedOrNonDailyMaxRateHasEnded = isset($nonDailyMaxRate)
                && $minutesElapsedDuringNonDailyMaxRate === min(
                    $start->diffInMinutes($end),
                    abs($nonDailyMaxRate->start_hour - $nonDailyMaxRate->end_hour) * 60
                );

            if ($tripHasEndedOrNonDailyMaxRateHasEnded) {
                echo "\nMINUTES ELAPSED NON DAILY: {$minutesElapsedDuringNonDailyMaxRate}\n";
                $nonDailyMaxRateTotals->push($nonDailyMaxRateTotals);
                $nonDailyMaxRateTotal = 0;
                $minutesElapsedDuringNonDailyMaxRate = 0;
            }
        });

        $dailyMaxRate = $this->rates->filter(fn ($rate) => $rate->daily_max)->first();
        if ($dailyMaxRate) {
            $this->applyMaxRate($timeCost, $dailyMaxRate, $twentyFourHourTotals);
        }

        if ($nonDailyMaxRate) {
            $this->applyMaxRate($timeCost, $nonDailyMaxRate, $nonDailyMaxRateTotals);
        }

        return $timeCost;
    }

    protected function applyMaxRate(&$timeCost, $maxRate, $maxRateTotals)
    {
        $maxRateType = $maxRate->daily_max ? 'DAILY' : 'NON DAILY';
        echo "\nAPPLYING {$maxRateType} MAX RATE\n";
        $totalOverChargedAmount = 0;
        $maxRateTotals->each(function ($total) use (&$totalOverChargedAmount, $maxRate) {
            if ($total > $maxRate->default_rate) {
                $totalOverChargedAmount += $total - $maxRate->default_rate;
            }
        });

        echo "\nTIME COST BEFORE: {$timeCost}\n";
        $timeCost -= $totalOverChargedAmount;
        echo "\nTIME COST AFTER: {$timeCost}\n";
    }

    protected function calculateDistanceCost(int $distance): int
    {
        $distanceCost = 0;
        $distanceRate = $this->rates->filter(fn ($rate) => $rate->rate_type === 'distance')->first();
        if (isset($distanceRate->special_rate)) {
            $distanceToBeChargedAtDefaultRate = $distance - $distanceRate->special_rate_limit;

            $distanceCost = $distanceToBeChargedAtDefaultRate > 0
                ? $distanceRate->special_rate * $distanceRate->special_rate_limit + $distanceRate->default_rate * $distanceToBeChargedAtDefaultRate
                : $distanceRate->special_rate * $distance;
        }

        return $distanceCost;
    }
}

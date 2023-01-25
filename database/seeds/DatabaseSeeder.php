<?php

use App\Models\Rate;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Company A
        // 400/hr weekdays
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 100,
        ]);
        // 400/hr weekends
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 100,
            'is_weekend' => true,
        ]);
        // 15/mile
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'distance',
            'default_rate' => 15,
        ]);

        // Company B
        // 1500/hr weekdays
        Rate::create([
            'company_name' => 'B',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 375,
        ]);
        // 1500/hr weekends
        Rate::create([
            'company_name' => 'B',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 375,
            'is_weekend' => true,
        ]);
        // 8500 max per 24hr
        Rate::create([
            'company_name' => 'B',
            'rate_type' => 'max',
            'default_rate' => 8500,
            'daily_max' => true,
        ]);
        // 50/km first 50 free
        Rate::create([
            'company_name' => 'B',
            'rate_type' => 'distance',
            'default_rate' => 50,
            'special_rate' => 0,
            'special_rate_limit' => 50,
        ]);
    }
}

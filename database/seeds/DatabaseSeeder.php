<?php

use App\Models\Rate;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Company A
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 100,
        ]);
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'time',
            'start_hour' => 0,
            'end_hour' => 24,
            'default_rate' => 100,
            'is_weekend' => true,
        ]);
        Rate::create([
            'company_name' => 'A',
            'rate_type' => 'distance',
            'default_rate' => 15,
        ]);
    }
}

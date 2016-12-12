<?php

use Illuminate\Database\Seeder;

class PayFrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\PayFrequency::insert([
            ['id' => 1, 'name' => 'Weekly'],
            ['id' => 2, 'name' => 'Bi-weekly'],
            ['id' => 3, 'name' => 'Semi-monthly'],
            ['id' => 4, 'name' => 'Monthly'],
        ]);
    }
}

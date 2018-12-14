<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airports = [
            'NYCA-sky',
            'SFOA-sky',
            'AUS-sky',
            'SEAA-sky',
            'LAXA-sky',
            'BOS-sky',
            'HOUA-sky',
            'CHIA-sky',
            'PHLA-sky',
        ];

        foreach (range(1, 50) as $time) {
            DB::table('users')->insert([
                'email' => "danny_{$time}@weekendr.io",
                'airport_code' => array_random($airports),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

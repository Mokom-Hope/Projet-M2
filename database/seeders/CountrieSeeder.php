<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            [
                'name' => 'Cameroun',
                'code' => 'CMR',
                'currency' => 'XAF',
                'phone_prefix' => '+237',
                'is_active' => true,
                'supported_payment_methods' => json_encode(['mtn_mobile_money', 'orange_money']),
                'min_transfer_amount' => 200,
                'max_transfer_amount' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'France',
                'code' => 'FRA',
                'currency' => 'EUR',
                'phone_prefix' => '+33',
                'is_active' => true,
                'supported_payment_methods' => json_encode(['credit_card', 'paypal']),
                'min_transfer_amount' => 500,
                'max_transfer_amount' => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nigeria',
                'code' => 'NGA',
                'currency' => 'NGN',
                'phone_prefix' => '+234',
                'is_active' => true,
                'supported_payment_methods' => json_encode(['bank_transfer', 'flutterwave']),
                'min_transfer_amount' => 100,
                'max_transfer_amount' => 750000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

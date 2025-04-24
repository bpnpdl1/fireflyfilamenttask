<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); 

     

        $descriptions = [
            'Salary Payment',
            'Freelance Project',
            'Groceries',
            'Electricity Bill',
            'Dining Out',
            'Rent Payment',
            'Gym Membership',
            'Fuel',
            'Internet Bill',
            'Software Subscription',
            'Medical Expenses',
            'Travel Expenses',
            'Gift Purchase',
            'Home Repair',
            'Clothing Purchase',
            'Entertainment',
            'Charity Donation',
            'Education Expenses',
            'Insurance Premium',
            'Car Maintenance',
            'Household Supplies',
            'Pet Care',
            'Personal Care',
            'Bank Fees',
        ];

        foreach (range(1, 90) as $i) {
            $type = fake()->randomElement(['income', 'expense']);
            $amount = $type === 'income'
                ? fake()->numberBetween(1000, 5000)
                : fake()->numberBetween(200, 2000);

            Transaction::create([
                'description' => fake()->randomElement($descriptions),
                'amount' => $amount,
                'type' => $type,
                'transaction_date' => Carbon::now()->subDays(rand(1, 90))->toDateString(),
                'user_id' => $user->id,
            ]);
        }


    }
}

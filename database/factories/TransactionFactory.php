<?php

namespace Database\Factories;

use App\Enums\TransactionTypeEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Common transaction descriptions from the seeder
     */
    protected array $descriptions = [
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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([
            TransactionTypeEnum::INCOME->value,
            TransactionTypeEnum::EXPENSE->value
        ]);
        
        // Generate amount based on transaction type (same as in seeder)
        $amount = $type === TransactionTypeEnum::INCOME->value
            ? $this->faker->numberBetween(1000, 5000)
            : $this->faker->numberBetween(200, 2000);

        return [
            'description' => $this->faker->randomElement($this->descriptions),
            'amount' => $amount,
            'type' => $type,
            'transaction_date' => Carbon::now()->subDays($this->faker->numberBetween(1, 90))->toDateString(),
            'user_id' => User::factory(),
        ];
    }
}

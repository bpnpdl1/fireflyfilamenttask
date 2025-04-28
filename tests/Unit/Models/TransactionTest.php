<?php

namespace Tests\Unit\Models;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Test for creating a transaction with valid data.
     *
     * @test
     */
    public function it_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'description' => 'Test transaction',
            'amount' => 100.50,
            'type' => TransactionTypeEnum::INCOME,
            'transaction_date' => '2025-04-27',
            'user_id' => $user->id,
        ]);

        // Remove transaction_date from the database check
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Test transaction',
            'amount' => 100.50,
            'type' => TransactionTypeEnum::INCOME->value,
            'user_id' => $user->id,
        ]);

        // Then check the transaction_date separately
        $dbTransaction = Transaction::find($transaction->id);
        $this->assertEquals('2025-04-27', $dbTransaction->transaction_date->format('Y-m-d'));
    }
}

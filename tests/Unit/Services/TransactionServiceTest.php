<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $service;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->service = new TransactionService;
    }

    public function test_create_transaction(): void
    {
        $data = [
            'description' => 'Test transaction',
            'amount' => 100.50,
            'type' => TransactionTypeEnum::INCOME,
            'transaction_date' => '2025-04-20',
        ];

        $transaction = $this->service->createTransaction($data);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Test transaction',
            'amount' => 100.50,
            'type' => TransactionTypeEnum::INCOME->value,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_get_transaction_by_id(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->getTransactionById($transaction->id);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($transaction->id, $result->id);
    }

    public function test_update_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'Original description',
        ]);

        $updated = $this->service->updateTransaction($transaction->id, [
            'description' => 'Updated description',
        ]);

        $this->assertEquals('Updated description', $updated->description);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Updated description',
        ]);
    }

    public function test_delete_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $result = $this->service->deleteTransaction($transaction->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_get_transactions_by_date_range(): void
    {
        // Create transactions on different dates
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-10',
            'type' => TransactionTypeEnum::INCOME,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-15',
            'type' => TransactionTypeEnum::EXPENSE,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-20',
            'type' => TransactionTypeEnum::INCOME,
        ]);

        // Test date range filter
        $transactions = $this->service->getTransactionsByDateRange('2025-04-14', '2025-04-21');
        $this->assertCount(2, $transactions);

        // Test date range with type filter
        $incomeTransactions = $this->service->getTransactionsByDateRange('2025-04-01', '2025-04-30', TransactionTypeEnum::INCOME->value);
        $this->assertCount(2, $incomeTransactions);
    }

    public function test_get_monthly_summary(): void
    {
        // Create income transactions
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-10',
            'type' => TransactionTypeEnum::INCOME,
            'amount' => 1000,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-20',
            'type' => TransactionTypeEnum::INCOME,
            'amount' => 500,
        ]);

        // Create expense transactions
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-15',
            'type' => TransactionTypeEnum::EXPENSE,
            'amount' => 300,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-25',
            'type' => TransactionTypeEnum::EXPENSE,
            'amount' => 200,
        ]);

        $summary = $this->service->getMonthlySummary('2025-04');

        $this->assertEquals(1500, $summary['income']);
        $this->assertEquals(500, $summary['expense']);
        $this->assertEquals(1000, $summary['balance']);
    }

    public function test_weekly_breakdown(): void
    {
        // Create transactions on different weeks
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-01', // First week
            'type' => TransactionTypeEnum::INCOME,
            'amount' => 500,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-10', // Second week
            'type' => TransactionTypeEnum::INCOME,
            'amount' => 300,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'transaction_date' => '2025-04-20', // Third week
            'type' => TransactionTypeEnum::INCOME,
            'amount' => 200,
        ]);

        $breakdown = $this->service->getWeeklyBreakdown('2025-04', TransactionTypeEnum::INCOME->value);

        // Should have entries for each week of April 2025
        foreach ($breakdown as $week) {
            if ($week['week'] === 1) {
                $this->assertEquals(500, $week['amount']);
            } elseif ($week['week'] === 2) {
                $this->assertEquals(300, $week['amount']);
            } elseif ($week['week'] === 3) {
                $this->assertEquals(200, $week['amount']);
            }
        }
    }
}

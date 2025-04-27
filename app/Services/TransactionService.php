<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    /**
     * Get all transactions.
     */
    public function getAllTransactions(): Collection
    {
        return Transaction::where('user_id', Auth::id())->get();
    }

    /**
     * Get a specific transaction by ID.
     */
    public function getTransactionById(int $id): ?Transaction
    {
        return Transaction::where('user_id', Auth::id())->find($id);
    }

    /**
     * Create a new transaction.
     */
    public function createTransaction(array $data): Transaction
    {
        $data['user_id'] = Auth::id();

        return Transaction::create($data);
    }

    /**
     * Update an existing transaction.
     */
    public function updateTransaction(int $id, array $data): ?Transaction
    {
        $transaction = $this->getTransactionById($id);

        if (! $transaction) {
            return null;
        }

        $transaction->update($data);

        return $transaction->fresh();
    }

    /**
     * Delete a transaction.
     */
    public function deleteTransaction(int $id): bool
    {
        $transaction = $this->getTransactionById($id);

        if (! $transaction) {
            return false;
        }

        return $transaction->delete();
    }

    /**
     * Get transactions for a specific date range.
     */
    public function getTransactionsByDateRange(?string $startDate = null, ?string $endDate = null, ?string $type = null): Collection
    {
        $query = Transaction::where('user_id', Auth::id());

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    /**
     * Get total income for a date range.
     */
    public function getTotalIncome(?string $startDate = null, ?string $endDate = null): float
    {
        return $this->getTotalByType(TransactionTypeEnum::INCOME, $startDate, $endDate);
    }

    /**
     * Get total expenses for a date range.
     */
    public function getTotalExpenses(?string $startDate = null, ?string $endDate = null): float
    {
        return $this->getTotalByType(TransactionTypeEnum::EXPENSE, $startDate, $endDate);
    }

    /**
     * Get balance (income - expenses) for a date range.
     */
    public function getBalance(?string $startDate = null, ?string $endDate = null): float
    {
        return $this->getTotalIncome($startDate, $endDate) - $this->getTotalExpenses($startDate, $endDate);
    }

    /**
     * Get total amount by transaction type.
     */
    protected function getTotalByType(TransactionTypeEnum $type, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = Transaction::where('user_id', Auth::id())
            ->where('type', $type);

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        return $query->sum('amount') ?? 0;
    }

    /**
     * Get transactions by month.
     *
     * @param  string  $yearMonth  Format: 'Y-m' (e.g., '2025-04')
     */
    public function getTransactionsByMonth(string $yearMonth, ?string $type = null): Collection
    {
        $date = Carbon::parse($yearMonth);

        $query = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    /**
     * Get monthly summary data (income, expense, balance).
     *
     * @param  string  $yearMonth  Format: 'Y-m' (e.g., '2025-04')
     */
    public function getMonthlySummary(string $yearMonth): array
    {
        $date = Carbon::parse($yearMonth);
        $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $date->copy()->endOfMonth()->format('Y-m-d');

        $income = $this->getTotalIncome($startDate, $endDate);
        $expense = $this->getTotalExpenses($startDate, $endDate);
        $balance = $income - $expense;

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'month' => $yearMonth,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Get weekly breakdown of transactions within a month.
     *
     * @param  string  $yearMonth  Format: 'Y-m' (e.g., '2025-04')
     */
    public function getWeeklyBreakdown(string $yearMonth, ?string $type = null): array
    {
        $date = Carbon::parse($yearMonth);
        $weeks = [];
        $currentDay = $date->copy()->startOfMonth();
        $weekNumber = 1;

        while ($currentDay->month === $date->month) {
            $weekStart = $currentDay->copy();
            $weekEnd = min($weekStart->copy()->endOfWeek(), $date->copy()->endOfMonth());

            $weeks[] = [
                'week' => $weekNumber,
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d'),
                'label' => "Week {$weekNumber} ({$weekStart->format('M d')} - {$weekEnd->format('M d')})",
                'amount' => $this->getAmountForDateRange($weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d'), $type),
            ];

            $currentDay = $weekEnd->copy()->addDay();
            $weekNumber++;
        }

        return $weeks;
    }

    /**
     * Get total amount for a specific date range and transaction type.
     */
    protected function getAmountForDateRange(string $startDate, string $endDate, ?string $type = null): float
    {
        $query = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->sum('amount') ?? 0;
    }

    /**
     * Get query builder for transactions.
     */
    public function getTransactionsQuery(): Builder
    {
        return Transaction::where('user_id', Auth::id())->orderBy('transaction_date', 'desc');
    }
}

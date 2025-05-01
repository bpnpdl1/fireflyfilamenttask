<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionPieChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Transaction Summary';

    public ?string $filter = 'this_week';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'all_time' => 'All Time',
        ];
    }

    protected function filterQuery()
    {
        return match ($this->filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'yesterday' => [
                'start' => now()->subDay()->startOfDay(),
                'end' => now()->subDay()->endOfDay(),
            ],
            'this_week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
            'last_week' => [
                'start' => now()->subWeek()->startOfWeek(),
                'end' => now()->subWeek()->endOfWeek(),
            ],
            'this_month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
            'last_month' => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            'this_quarter' => [
                'start' => now()->startOfQuarter(),
                'end' => now()->endOfQuarter(),
            ],
            'this_year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            'all_time' => [
                'start' => null,
                'end' => null,
            ],
            default => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
        };
    }

    protected function getData(): array
    {
        $dateRange = $this->filterQuery();

        $query = Transaction::where('user_id', auth()->id());

        if ($dateRange['start']) {
            $query->where('transaction_date', '>=', $dateRange['start']);
        }

        if ($dateRange['end']) {
            $query->where('transaction_date', '<=', $dateRange['end']);
        }

        $totalIncomes = (clone $query)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpenses = (clone $query)
            ->where('type', 'expense')
            ->sum('amount');

        // Calculate the percentage of each
        $total = $totalIncomes + $totalExpenses;
        $incomePercentage = $total > 0 ? round(($totalIncomes / $total) * 100) : 0;
        $expensePercentage = $total > 0 ? round(($totalExpenses / $total) * 100) : 0;

        return [
            'labels' => ['Income ('.$incomePercentage.'%)', 'Expense ('.$expensePercentage.'%)'],
            'datasets' => [
                [
                    'data' => [$totalIncomes, $totalExpenses],
                    'backgroundColor' => ['#4CAF50', '#F44336'],
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

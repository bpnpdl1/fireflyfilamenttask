<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionLineChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Transaction Totals';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'All Time',
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_7_days' => 'Last 7 Days',
            'last_14_days' => 'Last 14 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
        ];
    }
    
    protected function filterQuery(): array
    {
        $activeFilter = $this->filter ?? 'last_14_days';
        
        return match ($activeFilter) {
            'all' => [
                'startDate' => now()->subYears(1)->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'today' => [
                'startDate' => now()->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'yesterday' => [
                'startDate' => now()->subDay()->startOfDay(),
                'endDate' => now()->subDay()->endOfDay(),
            ],
            'last_7_days' => [
                'startDate' => now()->subDays(6)->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'last_14_days' => [
                'startDate' => now()->subDays(13)->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'last_30_days' => [
                'startDate' => now()->subDays(29)->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'this_month' => [
                'startDate' => now()->startOfMonth()->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
            'last_month' => [
                'startDate' => now()->subMonth()->startOfMonth()->startOfDay(),
                'endDate' => now()->subMonth()->endOfMonth()->endOfDay(),
            ],
            default => [
                'startDate' => now()->subDays(13)->startOfDay(),
                'endDate' => now()->endOfDay(),
            ],
        };
    }

    protected function getData(): array
    {
        // Get date range based on filter
        $dateRange = $this->filterQuery();
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];

        // Query to get daily transaction totals
        $transactions = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Generate all dates in range (including ones with no transactions)
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $balanceData = [];

        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('M d');

            $dayData = $transactions->firstWhere('date', $formattedDate);

            $income = $dayData ? $dayData->income : 0;
            $expense = $dayData ? $dayData->expense : 0;
            $balance = $income - $expense;

            $incomeData[] = $income;
            $expenseData[] = $expense;
            $balanceData[] = $balance;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                    'pointBackgroundColor' => '#4CAF50',
                    'pointBorderColor' => '#fff',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Expense',
                    'data' => $expenseData,
                    'backgroundColor' => '#F44336',
                    'borderColor' => '#F44336',
                    'pointBackgroundColor' => '#F44336',
                    'pointBorderColor' => '#fff',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Balance',
                    'data' => $balanceData,
                    'backgroundColor' => '#2196F3',
                    'borderColor' => '#2196F3',
                    'pointBackgroundColor' => '#2196F3',
                    'pointBorderColor' => '#fff',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

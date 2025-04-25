<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionLineChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Daily Transaction Totals';
    
    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get data for the last 14 days
        $startDate = now()->subDays(13)->startOfDay();
        $endDate = now()->endOfDay();
        
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

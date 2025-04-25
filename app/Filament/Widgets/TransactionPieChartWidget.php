<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionPieChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Transaction Summary';

    protected function getData(): array
    {
        $totalIncomes = Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->sum('amount');
            
        $totalExpenses = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->sum('amount');
       
        return [
            'labels' => ['Income', 'Expense'],
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

<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ExpenseBarChartWidget extends ChartWidget
{
    public $selectedMonth;

    protected static ?string $heading = 'Monthly Expense Breakdown';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        // Using the parent mount method
        parent::mount();

        // Default to current month if not provided
        if (! $this->selectedMonth) {
            $this->selectedMonth = now()->format('Y-m');
        }
    }

    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        // Update the selectedMonth property
        $this->selectedMonth = $month;

        // Force chart to refresh with new data
        $this->dispatch('chartjs-update');
    }

    protected function getData(): array
    {
        // Parse selected month
        $date = Carbon::parse($this->selectedMonth);
        $daysInMonth = $date->daysInMonth;

        // Calculate week ranges for the month
        $weeks = [];
        $currentDay = $date->copy()->startOfMonth();
        $weekNumber = 1;

        while ($currentDay->month === $date->month) {
            $weekStart = $currentDay->copy();
            $weekEnd = min($weekStart->copy()->endOfWeek(), $date->copy()->endOfMonth());

            $weeks[] = [
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d'),
                'label' => "Week $weekNumber ({$weekStart->format('M d')} - {$weekEnd->format('M d')})",
            ];

            $currentDay = $weekEnd->copy()->addDay();
            $weekNumber++;
        }

        // Get expense data for each week of the month
        $weeklyExpenses = [];
        $weekLabels = [];

        foreach ($weeks as $week) {
            $expense = Transaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$week['start'], $week['end']])
                ->sum('amount');

            $weeklyExpenses[] = $expense;
            $weekLabels[] = $week['label'];
        }

        // Get the month name for display
        $monthName = $date->format('F Y');

        return [
            'labels' => $weekLabels,
            'datasets' => [
                [
                    'label' => "Expenses for $monthName",
                    'data' => $weeklyExpenses,
                    'backgroundColor' => '#FFA500',
                    'borderColor' => '#FFA500',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'hoverBackgroundColor' => '#FFA500',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '₹' + value.toLocaleString(); }",
                    ],
                ],
            ],
        ];
    }
}

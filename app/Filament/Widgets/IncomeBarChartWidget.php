<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Traits\HasMonth;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class IncomeBarChartWidget extends ChartWidget
{
   
    public $selectedMonth;
    
    protected static ?string $heading = 'Monthly Income Breakdown';
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    
    public function mount(): void
    {
        parent::mount();

        if (!$this->selectedMonth) {
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
                'label' => "Week $weekNumber ({$weekStart->format('M d')} - {$weekEnd->format('M d')})"
            ];
            
            $currentDay = $weekEnd->copy()->addDay();
            $weekNumber++;
        }
        
        // Get income data for each week of the month
        $weeklyIncomes = [];
        $weekLabels = [];
        
        foreach ($weeks as $week) {
            $income = Transaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$week['start'], $week['end']])
                ->sum('amount');
                
            $weeklyIncomes[] = $income;
            $weekLabels[] = $week['label'];
        }
        
        // Get the month name for display
        $monthName = $date->format('F Y');
        
        return [
            'labels' => $weekLabels,
            'datasets' => [
                [
                    'label' => "Income for $monthName",
                    'data' => $weeklyIncomes,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'hoverBackgroundColor' => '#45a049',
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

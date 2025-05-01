<?php

namespace App\Filament\Widgets;

use App\Services\TransactionService;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Livewire\Attributes\On;

class TransactionCountWidget extends BaseWidget
{
    public $selectedMonth;


    protected TransactionService $transactionService;

    public function boot()
    {

        $this->transactionService = app(TransactionService::class);

        // Initialize selectedMonth in boot method if not set
        if (! $this->selectedMonth) {
            $this->selectedMonth = now()->format('Y-m');
        }
    }

    public function __construct()
    {

         $this->description="Monthly Transaction Overview for ".now()->format('M Y');
    }

    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        $this->selectedMonth = $month;
        $this->dispatch('reload');
    }

    protected function getStats(): array
    {
        // Ensure we have a default value for selectedMonth
        $monthToUse = $this->selectedMonth ?? now()->format('Y-m');

        // Get current month data using the service
        $currentMonthSummary = $this->transactionService->getMonthlySummary($monthToUse);
        $currentMonthIncome = $currentMonthSummary['income'];
        $currentMonthExpense = $currentMonthSummary['expense'];
        $currentMonthBalance = $currentMonthSummary['balance'];

        // Get previous month data using the service
        $previousMonthDate = Carbon::parse($monthToUse)->subMonth()->format('Y-m');
        $previousMonthSummary = $this->transactionService->getMonthlySummary($previousMonthDate);
        $previousMonthIncome = $previousMonthSummary['income'];
        $previousMonthExpense = $previousMonthSummary['expense'];
        $previousMonthBalance = $previousMonthSummary['balance'];

        // Calculate percentage changes
        $incomeChange = $previousMonthIncome > 0
            ? round((($currentMonthIncome - $previousMonthIncome) / $previousMonthIncome) * 100, 1)
            : ($currentMonthIncome > 0 ? 100 : 0);

        $expenseChange = $previousMonthExpense > 0
            ? round((($currentMonthExpense - $previousMonthExpense) / $previousMonthExpense) * 100, 1)
            : ($currentMonthExpense > 0 ? 100 : 0);

        $balanceChange = $previousMonthBalance != 0
            ? round((($currentMonthBalance - $previousMonthBalance) / abs($previousMonthBalance)) * 100, 1)
            : ($currentMonthBalance > 0 ? 100 : ($currentMonthBalance < 0 ? -100 : 0));

        return [
            Stat::make('Total Income', 'NPR '.number_format($currentMonthIncome, 2))
                ->icon('heroicon-o-banknotes')
                ->label('Total Income')
                ->progress(min(100, max(0, $incomeChange + 50)))
                ->progressBarColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconBackgroundColor('success')
                ->chartColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($incomeChange, 'income'))
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down', 'before')
                ->descriptionColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconColor('success'),

            Stat::make('Total Expense', 'NPR '.number_format($currentMonthExpense, 2))
                ->icon('heroicon-o-credit-card')
                ->label('Total Expense')
                ->progress(min(100, max(0, 100 - $expenseChange)))
                ->progressBarColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconBackgroundColor('danger')
                ->chartColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($expenseChange, 'expense', true))
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up', 'before')
                ->descriptionColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconColor('danger'),

            Stat::make('Current Balance', 'NPR '.number_format($currentMonthBalance, 2))
                ->icon('heroicon-o-scale')
                ->label('Current Balance')
                ->progress(min(100, max(0, $balanceChange + 50)))
                ->progressBarColor($balanceChange >= 0 ? 'success' : 'danger')
                ->iconBackgroundColor('primary')
                ->chartColor($balanceChange >= 0 ? 'success' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($balanceChange, 'balance'))
                ->descriptionIcon($balanceChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down', 'before')
                ->descriptionColor($balanceChange >= 0 ? 'success' : 'danger')
                ->iconColor('primary'),
        ];
    }

    /**
     * Get a descriptive text about the change compared to previous month
     */
    protected function getChangeDescription(float $percentageChange, string $type, bool $inversePositivity = false): string
    {
        $absChange = abs($percentageChange);
        $isIncrease = $percentageChange > 0;

        // For zero change
        if ($percentageChange == 0) {
            return 'No change from last month';
        }

        // Create description based on magnitude of change
        if ($absChange < 5) {
            $magnitude = 'Slight';
        } elseif ($absChange < 15) {
            $magnitude = 'Moderate';
        } elseif ($absChange < 30) {
            $magnitude = 'Significant';
        } else {
            $magnitude = 'Major';
        }

        $direction = $isIncrease ? 'increase' : 'decrease';

        return "$magnitude $direction of $absChange% from last month";
    }
}

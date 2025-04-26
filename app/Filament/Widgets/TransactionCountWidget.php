<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class TransactionCountWidget extends BaseWidget
{
    public $selectedMonth;

    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        $this->selectedMonth = $month;

        $this->dispatch('reload');
    }

    protected function getStats(): array
    {
        $currentMonthIncome = $this->getIncome();
        $currentMonthExpense = $this->getExpense();
        $currentMonthBalance = $this->getBalance();
        
        $previousMonthDate = Carbon::parse($this->selectedMonth)->subMonth()->format('Y-m');
        $previousMonthIncome = $this->getIncomeForMonth($previousMonthDate);
        $previousMonthExpense = $this->getExpenseForMonth($previousMonthDate);
        $previousMonthBalance = $previousMonthIncome - $previousMonthExpense;
        
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
            Stat::make('Total Income',  number_format($currentMonthIncome, 2))
                ->icon('heroicon-o-banknotes')
                ->label('Total Income')
                ->progress(min(100, max(0, $incomeChange + 50))) // Normalize to be between 0-100
                ->progressBarColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconBackgroundColor('success')
                ->chartColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($incomeChange, 'income'))
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down', 'before')
                ->descriptionColor($incomeChange >= 0 ? 'success' : 'danger')
                ->iconColor('success'),

            Stat::make('Total Expense', number_format($currentMonthExpense, 2))
                ->icon('heroicon-o-credit-card')
                ->label('Total Expense')
                ->progress(min(100, max(0, 100 - $expenseChange))) // Inverse scale for expenses (less is better)
                ->progressBarColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconBackgroundColor('danger')
                ->chartColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($expenseChange, 'expense', true))
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up', 'before')
                ->descriptionColor($expenseChange <= 0 ? 'success' : 'danger')
                ->iconColor('danger'),

            Stat::make('Current Balance', number_format($currentMonthBalance, 2))
                ->icon('heroicon-o-scale')
                ->label('Current Balance')
                ->progress(min(100, max(0, $balanceChange + 50))) // Normalize to be between 0-100
                ->progressBarColor($balanceChange >= 0 ? 'primary' : 'danger')
                ->iconBackgroundColor('primary')
                ->chartColor($balanceChange >= 0 ? 'primary' : 'danger')
                ->iconPosition('start')
                ->description($this->getChangeDescription($balanceChange, 'balance'))
                ->descriptionIcon($balanceChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down', 'before')
                ->descriptionColor($balanceChange >= 0 ? 'primary' : 'danger')
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
            return "No change from last month";
        }
        
        // Create description based on magnitude of change
        if ($absChange < 5) {
            $magnitude = "Slight";
        } elseif ($absChange < 15) {
            $magnitude = "Moderate";
        } elseif ($absChange < 30) {
            $magnitude = "Significant";
        } else {
            $magnitude = "Major";
        }
        
        $direction = $isIncrease ? "increase" : "decrease";
        
        return "$magnitude $direction of $absChange% from last month";
    }
    
    /**
     * Get income for a specific month
     */
    protected function getIncomeForMonth(string $month): float
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->whereMonth('transaction_date', Carbon::parse($month)->month)
            ->whereYear('transaction_date', Carbon::parse($month)->year)
            ->sum('amount');
    }
    
    /**
     * Get expense for a specific month
     */
    protected function getExpenseForMonth(string $month): float
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::parse($month)->month)
            ->whereYear('transaction_date', Carbon::parse($month)->year)
            ->sum('amount');
    }

    public function getIncome()
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->whereMonth('transaction_date', Carbon::parse($this->selectedMonth)->month)
            ->whereYear('transaction_date', Carbon::parse($this->selectedMonth)->year)
            ->sum('amount');
    }

    public function getExpense()
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::parse($this->selectedMonth)->month)
            ->whereYear('transaction_date', Carbon::parse($this->selectedMonth)->year)
            ->sum('amount');
    }

    public function getBalance()
    {
        return $this->getIncome() - $this->getExpense();
    }
}

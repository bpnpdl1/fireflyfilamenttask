<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class TransactionCountWidget extends BaseWidget
{

    public $selectedMonth;



    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        $this->selectedMonth = $month;
        
        // Force refresh of the widget stats to show updated numbers
        $this->dispatch('reload');
    }
    
    protected function getStats(): array
    {
        return [
            Stat::make('Income',$this->getIncome())
                ->label('Total Income')
                ->description('Total income for the month')
                ->color('success'),
                
            Stat::make('Expense',$this->getExpense())
                ->label('Total Expense')
                ->description('Total expense for the month')
                ->color('danger'),
                
            Stat::make('Balance',$this->getBalance())
                ->label('Current Balance')
                ->description('Current balance for the month')
                ->color('primary'),
        ];
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

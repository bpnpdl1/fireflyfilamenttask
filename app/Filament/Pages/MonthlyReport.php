<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TransactionCountWidget;
use App\Filament\Widgets\TransactionLineChartWidget;
use App\Filament\Widgets\TransactionPieChartWidget;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MonthlyReport extends Page 
{


    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.monthly-report';

    public $selectedMonth;

    public $activeTab='income';

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionCountWidget::class,
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('month')
                ->label('Select Month')
                ->action('selectMonth')
                ->view('filament.fields.datepicker',[
                    'selectedMonth' => $this->selectedMonth,
                ])
                ->color('primary'),
            Action::make('export')
                ->label('Export Report in PDF'),  
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

    public function updateSelectedMonth($month)
    {
        // Update the selectedMonth property
        $this->selectedMonth = $month;
        
        // Refresh all widgets to reflect the new month - using the correct method
        $this->dispatch('filament.refreshWidgets');
        
        // Dispatch event for any components that might be listening
        $this->dispatch('update-selected-month', month: $month);
    }


}

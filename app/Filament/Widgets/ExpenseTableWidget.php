<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ExpenseTableWidget extends BaseWidget
{
    public $selectedMonth;

    protected static ?string $heading = 'Monthly Expense Transactions';

    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        $this->selectedMonth = $month;

        // This will force a complete re-render of the table
        $this->resetTable();
    }

    public function mount(): void
    {
        // Default to current month if not provided
        if (! $this->selectedMonth) {
            $this->selectedMonth = now()->format('Y-m');
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->where('type', TransactionTypeEnum::EXPENSE)
                    ->where('user_id', Auth::id())
                    ->whereMonth('transaction_date', Carbon::parse($this->selectedMonth)->month)
                    ->whereYear('transaction_date', Carbon::parse($this->selectedMonth)->year)
            )
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('NPR')
                    ->label('Amount'),
                TextColumn::make('transaction_date')
                    ->label('Transaction Date')
                    ->date(),
            ]);
    }
}

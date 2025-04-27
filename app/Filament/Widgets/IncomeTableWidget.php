<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class IncomeTableWidget extends BaseWidget
{
    public $selectedMonth;

    protected TransactionService $transactionService;

    protected static ?string $heading = 'Monthly Income Transactions';

    public function boot()
    {
        $this->transactionService = app(TransactionService::class);
    }

    public function mount(): void
    {
        // Default to current month if not provided
        if (! $this->selectedMonth) {
            $this->selectedMonth = now()->format('Y-m');
        }
    }

    #[On('update-selected-month')]
    public function updateSelectedMonth($month): void
    {
        $this->selectedMonth = $month;

        // This will force a complete re-render of the table
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        // Get monthly income transactions from the service
        $incomeTransactionIds = $this->transactionService->getTransactionsByMonth($this->selectedMonth, TransactionTypeEnum::INCOME->value)
            ->pluck('id');

        return $table
            ->query(
                Transaction::query()->whereIn('id', $incomeTransactionIds)
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

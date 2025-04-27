<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionTableWidget extends BaseWidget
{
    protected TransactionService $transactionService;

    public function boot()
    {
        $this->transactionService = app(TransactionService::class);
    }

    public function table(Table $table): Table
    {
        // Get recent transactions from the service
        $recentTransactionIds = $this->transactionService->getAllTransactions()
            ->sortByDesc('transaction_date')
            ->take(8)
            ->pluck('id');

        return $table
            ->query(
                // Query only the transactions returned by the service
                Transaction::query()->whereIn('id', $recentTransactionIds)
            )
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('NPR')
                    ->label('Amount'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn ($record) => $record->type->getColor()),
                TextColumn::make('transaction_date')
                    ->label('Transaction Date')
                    ->date(),
            ])
            ->paginated(false)
            ->headerActions([
                Action::make('View All')
                    ->action(function () {
                        $this->redirect(route('filament.user.resources.transactions.index'));
                    }),
            ]);
    }
}

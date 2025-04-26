<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentTransactionTableWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->where('user_id', Auth::id())
                    ->latest('transaction_date')
                    ->take(8)
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

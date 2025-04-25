<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class IncomeTableWidget extends BaseWidget
{

    protected static ?string $heading = 'Monthly Income Transactions';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                ->where('type', TransactionTypeEnum::INCOME )
                ->where('user_id', Auth::id())
                ->whereMonth('transaction_date', Carbon::parse(now()->format('Y-m'))->month)
                ->whereYear('transaction_date', Carbon::parse(now()->format('Y-m'))->year)
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

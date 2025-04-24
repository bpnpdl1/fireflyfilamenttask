<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric(),
                Radio::make('type')
                    ->label('Transaction Type')
                    ->options(TransactionTypeEnum::class)->inline()
                    ->required(),
                DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->required()
                    ->default(now()->format('Y-m-d')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
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
            ->filters([
                SelectFilter::make('transaction_date')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_week' => 'Last Week',
                        'this_month' => 'This Month',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'today') {
                            return $query->whereDate('transaction_date', now());
                        } elseif ($state === 'yesterday') {
                            return $query->whereDate('transaction_date', now()->subDay());
                        } elseif ($state === 'this_week') {
                            return $query->whereBetween('transaction_date', [
                                now()->startOfWeek(),
                                now()->endOfWeek(),
                            ]);
                        } elseif ($state === 'last_week') {
                            return $query->whereBetween('transaction_date', [
                                now()->subWeek()->startOfWeek(),
                                now()->subWeek()->endOfWeek(),
                            ]);
                        } elseif ($state === 'this_month') {
                            return $query->whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year);
                        }
                        
                        return $query;
                    }),

            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}

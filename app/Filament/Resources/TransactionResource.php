<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                SelectFilter::make('type')
                    ->options(TransactionTypeEnum::class)
                    ->label('Transaction Type')
                    ->placeholder('All Types'),
                SelectFilter::make('transaction_date')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_week' => 'Last Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'last_3_months' => 'Last 3 Months',
                        'last_6_months' => 'Last 6 Months',
                        'this_year' => 'This Year',
                        'last_year' => 'Last Year',
                    ])
                    ->label('Date Range')
                    ->placeholder('All Time')
                    ->query(function ($query, $state) {
                        return match ($state) {
                            'today' => $query->whereDate('transaction_date', now()),
                            'yesterday' => $query->whereDate('transaction_date', now()->subDay()),
                            'this_week' => $query->whereBetween('transaction_date', [
                                now()->startOfWeek(),
                                now()->endOfWeek(),
                            ]),
                            'last_week' => $query->whereBetween('transaction_date', [
                                now()->subWeek()->startOfWeek(),
                                now()->subWeek()->endOfWeek(),
                            ]),
                            'this_month' => $query->whereMonth('transaction_date', now()->month)
                                ->whereYear('transaction_date', now()->year),
                            'last_month' => $query->whereMonth('transaction_date', now()->subMonth()->month)
                                ->whereYear('transaction_date', now()->subMonth()->year),
                            'last_3_months' => $query->where('transaction_date', '>=', now()->subMonths(3)->startOfDay()),
                            'last_6_months' => $query->where('transaction_date', '>=', now()->subMonths(6)->startOfDay()),
                            'this_year' => $query->whereYear('transaction_date', now()->year),
                            'last_year' => $query->whereYear('transaction_date', now()->subYear()->year),
                            default => $query,
                        };
                    }),
                TrashedFilter::make(),
            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function ($record) {
                        $transactionService = app(TransactionService::class);

                        return $transactionService->deleteTransaction($record->id);
                    }),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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

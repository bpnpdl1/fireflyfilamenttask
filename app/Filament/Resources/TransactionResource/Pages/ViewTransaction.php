<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Services\TransactionService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    /**
     * Define header actions for the view page
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->using(function ($record) {
                    $transactionService = app(TransactionService::class);
                    return $transactionService->deleteTransaction($record->id);
                }),
        ];
    }
    
    /**
     * Override record retrieval to use TransactionService
     */
    protected function resolveRecord(int|string $key): Model
    {
        $transactionService = app(TransactionService::class);
        
        return $transactionService->getTransactionById($key);
    }
}
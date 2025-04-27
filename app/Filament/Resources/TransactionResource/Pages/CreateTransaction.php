<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Services\TransactionService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * Override the create method to use TransactionService
     */
    protected function handleRecordCreation(array $data): Model
    {
        $transactionService = app(TransactionService::class);

        return $transactionService->createTransaction($data);
    }
}

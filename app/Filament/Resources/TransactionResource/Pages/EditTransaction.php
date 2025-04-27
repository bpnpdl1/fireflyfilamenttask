<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Services\TransactionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function (Model $record) {
                    $transactionService = app(TransactionService::class);

                    return $transactionService->deleteTransaction($record->id);
                }),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    /**
     * Override the save method to use TransactionService
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $transactionService = app(TransactionService::class);

        return $transactionService->updateTransaction($record->id, $data);
    }
}

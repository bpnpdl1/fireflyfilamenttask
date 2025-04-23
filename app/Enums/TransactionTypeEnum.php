<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionTypeEnum: string implements HasColor, HasLabel
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INCOME => 'success',
            self::EXPENSE => 'danger',
        };
    }
}

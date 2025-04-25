<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TransactionCountWidget;
use App\Filament\Widgets\TransactionLineChartWidget;
use App\Filament\Widgets\TransactionPieChartWidget;
use App\Models\Transaction;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionCountWidget::class,
            TransactionLineChartWidget::class,
            TransactionPieChartWidget::class,
        ];
    }



}

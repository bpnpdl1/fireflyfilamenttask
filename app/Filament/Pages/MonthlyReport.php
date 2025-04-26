<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TransactionCountWidget;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MonthlyReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.monthly-report';

    public $selectedMonth;

    public $activeTab = 'income';

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionCountWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {

        $month = $this->selectedMonth;

        return [
            Action::make('month')
                ->label('Select Month')
                ->action('selectMonth')
                ->view('filament.fields.datepicker', [
                    'selectedMonth' => $this->selectedMonth,
                ])
                ->color('primary'),
            Action::make('export')
                ->label('Export Report in PDF')
                ->action(function () use ($month) {

                    return to_route('monthly-report.download', [
                        'month' => $month,
                    ]);
                }),
        ];
    }

    public function getIncome()
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->whereMonth('transaction_date', Carbon::parse($this->selectedMonth)->month)
            ->whereYear('transaction_date', Carbon::parse($this->selectedMonth)->year)
            ->sum('amount');
    }

    public function getExpense()
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::parse($this->selectedMonth)->month)
            ->whereYear('transaction_date', Carbon::parse($this->selectedMonth)->year)
            ->sum('amount');
    }

    public function getBalance()
    {
        return $this->getIncome() - $this->getExpense();
    }

    public function updateSelectedMonth($month)
    {
        // Update the selectedMonth property
        $this->selectedMonth = $month;

        // Refresh all widgets to reflect the new month - using the correct method
        $this->dispatch('filament.refreshWidgets');

        // Dispatch event for any components that might be listening
        $this->dispatch('update-selected-month', month: $month);
    }

    public function exportReport()
    {
        $transactions = Transaction::whereMonth('transaction_date', $this->selectedMonth)
            ->where('user_id', auth()->id())
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        $pdf = Pdf::loadView('month-report', [
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'month' => $this->selectedMonth,
        ]);

        return response()->streamDownload(fn () => print ($pdf->stream()), 'month-report', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

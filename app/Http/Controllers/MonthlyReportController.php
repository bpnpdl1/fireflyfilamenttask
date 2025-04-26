<?php

namespace App\Http\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Services\TransactionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    protected TransactionService $transactionService;
    
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function downloadReport($month)
    {
        // Get all transactions for the month using the service
        $transactions = $this->transactionService->getTransactionsByMonth($month);
        
        // Get the monthly summary data using the service
        $summary = $this->transactionService->getMonthlySummary($month);
        $transactionTypes = TransactionTypeEnum::cases();
        
        $pdf = Pdf::loadView('month-report', [
            'transactions' => $transactions,
            'income' => $summary['income'],
            'expense' => $summary['expense'],
            'balance' => $summary['balance'],
            'month' => $month,
            'transactionTypes' => $transactionTypes,
        ]);

        // Generate a proper filename with the month and year
        $filename = 'monthly-report-' . Carbon::parse($month)->format('Y-m') . '.pdf';
        
        // Return the PDF as a download response
        return $pdf->download($filename);
    }
}

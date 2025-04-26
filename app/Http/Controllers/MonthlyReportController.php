<?php

namespace App\Http\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    public function downloadReport($month)
    {

        $transactions = Transaction::where('user_id', Auth::id())
            ->whereMonth('transaction_date', Carbon::parse($month)->month)
            ->whereYear('transaction_date', Carbon::parse($month)->year)
            ->get();

        $income = $transactions->where('type', TransactionTypeEnum::INCOME)->sum('amount');
        $expense = $transactions->where('type', TransactionTypeEnum::EXPENSE)->sum('amount');
        $balance = $income - $expense;
        $transactionTypes = TransactionTypeEnum::cases();

        $pdf = Pdf::loadView('month-report', [
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'month' => $month,
            'transactionTypes' => $transactionTypes,
        ]);

        // Generate a proper filename with the month and year
        $filename = 'monthly-report-'.date('Y-m', strtotime($month)).'.pdf';

        // Return the PDF as a download response
        return $pdf->stream($filename);
    }
}

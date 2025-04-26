<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Monthly Report - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            margin: 1cm;
            line-height: 1.3;
            color: #333;
            background-color: #fff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        
        .header h1 {
            color: #2E7D32;
            font-size: 24pt;
            margin: 0;
            padding: 0;
        }
        
        .user-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        .report-date {
            color: #555;
            font-style: italic;
            margin-top: 5px;
        }
        
        .summary-cards {
            width: 100%;
            margin-bottom: 30px;
            display: table;
            table-layout: fixed;
            border-spacing: 10px;
            border-collapse: separate;
        }
        
        .card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .income-card {
            background-color: #E8F5E9;
            border: 1px solid #A5D6A7;
        }
        
        .expense-card {
            background-color: #FFEBEE;
            border: 1px solid #FFCDD2;
        }
        
        .balance-card {
            background-color: #E3F2FD;
            border: 1px solid #90CAF9;
        }
        
        .card h2 {
            font-size: 14pt;
            margin: 0 0 10px 0;
        }
        
        .card .amount {
            font-size: 18pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .section-title {
            font-size: 16pt;
            color: #333;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px 0;
        }
        
        th {
            background-color: #f5f5f5;
            color: #333;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .income {
            color: #2E7D32;
        }
        
        .expense {
            color: #C62828;
        }
        
        .amount-column {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .date-column {
            width: 100px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #777;
            text-align: center;
        }
        
        .disclaimer {
            font-style: italic;
            float: right;
            margin-top: 20px;
            font-size: 9pt;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Monthly Report</h1>
        <div class="report-date">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</div>
    </div>
    
    <div class="user-info">
        <strong>Generated for:</strong> {{ auth()->user()->name }}<br>
        <strong>Date generated:</strong> {{ now()->format('M d, Y H:i') }}
    </div>

    <div class="summary-cards">
        <div class="card income-card">
            <h2 class="income">Total Income</h2>
            <div class="amount income">NPR {{ number_format($income, 2) }}</div>
        </div>
        <div class="card expense-card">
            <h2 class="expense">Total Expenses</h2>
            <div class="amount expense">NPR {{ number_format($expense, 2) }}</div>
        </div>
        <div class="card balance-card">
            <h2>Current Balance</h2>
            <div class="amount" style="color: {{ $balance >= 0 ? '#2E7D32' : '#C62828' }}">
                NPR {{ number_format($balance, 2) }}
            </div>
        </div>
    </div>

    @foreach($transactionTypes as $transactionType)
    <h2 class="section-title">{{ $transactionType->getLabel() }} Transactions</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th class="date-column">Transaction Date</th>
                <th class="amount-column">Amount (NPR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions->where('type', $transactionType) as $transaction)
                <tr>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                    <td class="amount-column">{{ number_format($transaction->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="2" style="text-align: right;">Total {{ $transactionType->getLabel() }}:</td>
                <td class="amount-column {{ $transactionType == 'income' ? 'income' : 'expense' }}">
                    NPR {{ number_format($transactions->where('type', $transactionType)->sum('amount'), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <div class="disclaimer">
        <strong>Disclaimer:</strong> This is a system-generated report. No signature is required.
    </div>
    
    <div class="footer">
        Generated by {{env('APP_NAME')}} &copy; {{ date('Y') }}
    </div>
</body>
</html>

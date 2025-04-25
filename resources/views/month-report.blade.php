<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-sizing: border-box;
        }
        .card h2 {
            font-size: 18px;
            margin: 10px 0;
        }
        .card p {
            font-size: 14px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f2f2f2;
        }
        .income {
            color: green;
        }
        .expense {
            color: red;
        }
    </style>
</head>
<body>

    <h1>Monthly Report - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h1>

    <div class="summary-cards">
        <div class="card">
            <h2 class="income">Total Income</h2>
            <p><strong>NPR {{ number_format($income, 2) }}</strong></p>
           
        </div>
        <div class="card">
            <h2 class="expense">Total Expenses</h2>
            <p><strong>NPR {{ number_format($expense, 2) }}</strong></p>
            
        </div>
        <div class="card">
            <h2>Current Balance</h2>
            <p><strong>NPR {{ number_format($balance, 2) }}</strong></p>
           
        </div>
    </div>

   <div>
    <h2>Incomes</h2>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th>Amount (NPR)</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->description }}</td>
                    <td class="{{ $transaction->type == 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($transaction->type) }}
                    </td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
   </div>

   <div>
    <h2>Expenses</h2>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th>Amount (NPR)</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->description }}</td>
                    <td class="{{ $transaction->type == 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($transaction->type) }}
                    </td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
   </div>

</body>
</html>

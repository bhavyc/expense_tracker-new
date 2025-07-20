<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expense Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>
    <h2>Udhyaar - Expense Report</h2>
    <p>Date Range: {{ request('from') ?? 'All' }} to {{ request('to') ?? 'All' }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Group</th>
                <th>Category</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td>{{ $expense->created_at->format('d M Y') }}</td>
                <td>{{ $expense->user->name }}</td>
                <td>{{ $expense->group->name ?? 'N/A' }}</td>
                <td>{{ $expense->category ?? 'N/A' }}</td>
                <td>{{ $expense->amount }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">No data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>{{ $group->name }} - Weekly Expenses</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #4CAF50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>
    <h2>{{ $group->name }} - Last 7 Days Expenses</h2>

    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Total Expense (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyData as $data)
                <tr>
                    <td>{{ $data['name'] }}</td>
                    <td>{{ $data['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

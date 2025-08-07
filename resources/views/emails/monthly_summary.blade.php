<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Expense Summary</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hello {{ $user->name }},</h2>
    <p>Here is your expense summary for <strong>{{ $month }}</strong>:</p>

    <ul>
        <li><strong>Total Expenses:</strong> ₹{{ $totalExpense }}</li>
    </ul>

    <p>Thank you for using <strong>SplitEase</strong>!</p>
</body>
</html>

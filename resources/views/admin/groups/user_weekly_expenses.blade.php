<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $group->name }} - User Weekly Expenses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container">
  <h3 class="mb-3 text-success">{{ $group->name }} - User Weekly Expenses</h3>
  <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary mb-3">← Back</a>

  <div class="card shadow-sm p-3">
    <table class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>User</th>
          <!-- <th>Week</th> -->
          <th>Total Expense</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($weeklyExpenses as $expense)
          <tr>
            <td>{{ $expense->user_name }}</td>
            <!-- <td>Week {{ $expense->year_week }}</td> -->
            <td>₹{{ number_format($expense->total_weekly_expense, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center text-muted">No expenses found for this group.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

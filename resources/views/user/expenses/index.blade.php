<!DOCTYPE html>
<html>
<head>
    <title>My Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">My Expenses</h2>

    <!-- Add Expense Button -->
    <a href="{{ route('user.expenses.create') }}" class="btn btn-success mb-3">+ Add Expense</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Expense Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Group</th>
                <th>Description</th>
                <th>Amount (₹)</th>
                <th>Date</th>
                <th>Category</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $index => $expense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $expense->group->name ?? 'No Group' }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->expense_date }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>
                        <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                    </td>
                    <td>{{ $expense->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No expenses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>

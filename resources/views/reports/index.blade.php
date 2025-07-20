<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa">
    <div class="container py-4">
        <h2 class="mb-4">🧾 Expense Reports</h2>

        <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
            </div>
        </form>

        <div class="mt-4">
            <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger me-2">📄 Export as PDF</a>
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-success">📥 Export as CSV</a>
        </div>

        <div class="mt-4 bg-white p-3 rounded shadow-sm">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
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
                            <td colspan="5" class="text-center">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

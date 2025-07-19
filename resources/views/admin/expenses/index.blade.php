<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>All Expenses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .table-hover tbody tr:hover {
      background-color: #f1f5f9;
      transition: background-color 0.3s ease;
    }
    .btn-custom {
      background-color: #198754;
      color: #fff;
      transition: background-color 0.2s ease;
    }
    .btn-custom:hover {
      background-color: #146c43;
    }
    .splits {
      font-size: 0.9em;
      color: #495057;
      padding-left: 0;
    }
    .splits li {
      list-style: none;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
      overflow-x: auto;
    }
    .table-responsive {
      border-radius: 10px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-primary">All Expenses</h2>
      <a href="{{ route('admin.expenses.create') }}" class="btn btn-custom">+ Add New Expense</a>
    </div>

    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Paid By</th>
            <th>Group</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Category</th>
            <th>Date</th>
            <th>Status</th>
            <th>Splits</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @foreach($expenses as $expense)
          <tr>
            <td>{{ $expense->id }}</td>
            <td>{{ $expense->user->name ?? 'N/A' }}</td>
            <td>{{ $expense->group->name ?? 'Personal' }}</td>
            <td>{{ $expense->description }}</td>
            <td>₹{{ number_format($expense->amount, 2) }}</td>
            <td>{{ $expense->category ?? 'N/A' }}</td>
            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
            <td>
              <span class="badge {{ $expense->status === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ ucfirst($expense->status) }}
              </span>
            </td>
            <td>
              @if($expense->splits && $expense->splits->count())
                <ul class="splits text-start">
                  @foreach($expense->splits as $split)
                    <li>
                      <strong>{{ $split->user->name ?? 'N/A' }}</strong> - 
                      {{ ucfirst($split->type) }} ₹{{ number_format($split->amount, 2) }}
                    </li>
                  @endforeach
                </ul>
              @else
                N/A
              @endif
            </td>
            <td>
              <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary mb-1">Edit</a>
              <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>

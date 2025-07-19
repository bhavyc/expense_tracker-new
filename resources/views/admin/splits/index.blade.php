<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Splits List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .animated-title {
            animation: slideIn 1s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-hover:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f9ff;
        }

        .btn-sm {
            padding: 4px 10px;
        }

        .add-btn {
            float: right;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="animated-title">All Splits</h2>
        <a href="{{ route('admin.splits.create') }}" class="btn btn-success btn-sm add-btn">
            ➕ Add New Split
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-hover shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Expense</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($splits as $split)
                    <tr>
                        <td>{{ $split->id }}</td>
                        <td>{{ $split->user->name }}</td>
                        <td>{{ $split->expense->description ?? 'N/A' }}</td>
                        <td>₹{{ number_format($split->amount, 2) }}</td>
                        <td><span class="badge bg-{{ $split->type === 'lent' ? 'primary' : 'info' }}">{{ ucfirst($split->type) }}</span></td>
                        <td>
                            <a href="{{ route('admin.splits.edit', $split->id) }}" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <form action="{{ route('admin.splits.destroy', $split->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

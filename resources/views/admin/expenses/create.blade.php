<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1s ease-in-out;
        }

        .card {
            width: 100%;
            max-width: 600px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-floating > label {
            padding-left: 0.75rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="card p-4">
    <h3 class="text-center mb-3">Add New Expense</h3>
    
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.expenses.store') }}" method="POST">
        @csrf

        <div class="form-floating mb-3">
            <select name="user_id" class="form-select" required>
                <option value="">Select User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <label>User</label>
        </div>

        <div class="form-floating mb-3">
            <select name="group_id" class="form-select">
                <option value="">None</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            <label>Group (optional)</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="description" placeholder="Description" required>
            <label>Description</label>
        </div>

        <div class="form-floating mb-3">
            <input type="number" step="0.01" class="form-control" name="amount" placeholder="Amount" required>
            <label>Amount</label>
        </div>

        <div class="form-floating mb-3">
            <input type="date" class="form-control" name="expense_date" placeholder="Date" required>
            <label>Date</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="category" placeholder="Category">
            <label>Category</label>
        </div>

        <div class="form-floating mb-3">
            <select name="status" class="form-select">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
            </select>
            <label>Status</label>
        </div>

        <div class="form-floating mb-3">
            <textarea class="form-control" name="notes" placeholder="Notes" style="height: 100px;"></textarea>
            <label>Notes</label>
        </div>

        <button type="submit" class="btn btn-success w-100">Save Expense</button>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-link d-block mt-2 text-center">← Back to Expenses</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

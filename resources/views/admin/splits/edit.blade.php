<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Split</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 60px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        .form-card {
            max-width: 600px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px;
            background-color: white;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-submit {
            float: right;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <h3 class="text-center mb-4">Edit Split</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.splits.update', $split->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">User</label>
                <select class="form-select" name="user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $split->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Expense</label>
                <select class="form-select" name="expense_id" required>
                    @foreach($expenses as $expense)
                        <option value="{{ $expense->id }}" {{ $split->expense_id == $expense->id ? 'selected' : '' }}>
                            {{ $expense->description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" class="form-control" name="amount" value="{{ $split->amount }}" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Type</label>
                <select class="form-select" name="type" required>
                    <option value="lent" {{ $split->type == 'lent' ? 'selected' : '' }}>Lent</option>
                    <option value="owned" {{ $split->type == 'owned' ? 'selected' : '' }}>Owned</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-submit">Update Split</button>
        </form>
    </div>
</div>

</body>
</html>

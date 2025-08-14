<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.01);
        }

        label {
            font-weight: 600;
        }

        .form-control, .form-select {
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: #00bfa5;
            box-shadow: 0 0 0 0.2rem rgba(0, 191, 165, 0.25);
        }

        .btn-success {
            background-color: #00bfa5;
            border: none;
        }

        .btn-success:hover {
            background-color: #009e88;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="p-4 bg-light fade-in">

<div class="container mt-4">
    <div class="top-actions mb-3">
        <h2>Add New Expense</h2>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">← Back to Expenses</a>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" id="user_id" class="form-select" required>
    <option value="">Select User</option>
    @foreach ($users as $user)
        @if($user->role !== 'admin') {{--  Exclude admin users --}}
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endif
    @endforeach
</select>

            </div>

            <div class="mb-3">
                <label for="group_id" class="form-label">Group</label>
                <select name="group_id" id="group_id" class="form-select">
                    <option value="">None</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" name="description" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="expense_date" class="form-label">Expense Date</label>
                <input type="date" name="expense_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" name="category" class="form-control">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100"> Save Expense</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('user_id').addEventListener('change', function () {
        const userId = this.value;
        const groupSelect = document.getElementById('group_id');
        groupSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/get-groups-by-user/${userId}`)
            .then(res => res.json())
            .then(data => {
                groupSelect.innerHTML = '<option value="">None</option>';
                if (data.length === 0) {
                    groupSelect.innerHTML += '<option disabled>No groups found</option>';
                } else {
                    data.forEach(group => {
                        groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                    });
                }
            })
            .catch(() => {
                groupSelect.innerHTML = '<option disabled>Error loading groups</option>';
            });
    });
</script>

</body>
</html>

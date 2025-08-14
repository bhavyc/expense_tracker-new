<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Create New Expense</h2>
   <h1>Hello</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   

    <form action="{{ route('user.expenses.store') }}" method="POST" id="expense-form">
        @csrf

        <!-- User -->
        <div class="mb-3">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="{{ Auth::id() }}" selected>{{ Auth::user()->name }}</option>
            </select>
        </div>

        <!-- Group -->
        <div class="mb-3">
            <label for="group_id" class="form-label">Group</label>
            <select name="group_id" id="group_id" class="form-control">
                <option value="">-- Select Group --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Budget Left Info -->
        <div id="budget-left-info" class="mb-3" style="font-weight: 600; display: none;">
            Budget Left for this Group: ₹<span id="budget-left-value">0.00</span>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input
                type="text"
                name="description"
                id="description"
                class="form-control"
                required
                maxlength="255"
            />
        </div>

        <!-- Amount -->
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input
                type="number"
                name="amount"
                id="amount"
                class="form-control"
                step="0.01"
                min="0"
                required
            />
        </div>

        <!-- Expense Date -->
        <div class="mb-3">
            <label for="expense_date" class="form-label">Expense Date</label>
            <input
                type="date"
                name="expense_date"
                id="expense_date"
                class="form-control"
                required
            />
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input
                type="text"
                name="category"
                id="category"
                class="form-control"
                maxlength="100"
                required
            />
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pending" selected>Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea
                name="notes"
                id="notes"
                class="form-control"
                rows="3"
                maxlength="500"
            ></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Expense</button>
        <a href="{{ route('user.expenses.index') }}" class="btn btn-secondary">Back to Expenses</a>
    </form>
</div>

<script>
    document.getElementById('group_id').addEventListener('change', function() {
        const groupId = this.value;
        const budgetInfo = document.getElementById('budget-left-info');
        const budgetValue = document.getElementById('budget-left-value');

        if (!groupId) {
            budgetInfo.style.display = 'none';
            budgetValue.textContent = '0.00';
            return;
        }

        fetch('/account/group-budget-left/' + groupId)
            .then(response => response.json())
            .then(data => {
                budgetValue.textContent = parseFloat(data.budgetLeft).toFixed(2);
                budgetInfo.style.display = 'block';
            })
            .catch(() => {
                budgetInfo.style.display = 'none';
                budgetValue.textContent = '0.00';
            });
    });
</script>

</body>
</html>

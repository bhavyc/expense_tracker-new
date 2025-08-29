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
                <option value="">-- None --</option>
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
            <input type="text" name="description" id="description" class="form-control" required maxlength="255" />
        </div>

        <!-- Amount -->
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required />
            <!-- Budget Warning -->
            <div id="budget-warning" style="font-weight:600; margin-top:5px; display:none;"></div>
        </div>

        <!-- Expense Date -->
        <div class="mb-3">
            <label for="expense_date" class="form-label">Expense Date</label>
            <input type="date" name="expense_date" id="expense_date" class="form-control" required />
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" id="category" class="form-control" maxlength="100" required />
        </div>

        <!-- Split Method (only visible if group selected) -->
        <div class="mb-3" id="method-container" style="display:none;">
            <label for="method" class="form-label">Split Method</label>
            <select name="method" id="method" class="form-control">
                <option value="equal" selected>Equal</option>
                <option value="unequal">Unequal</option>
                <option value="percentage">Percentage</option>
                <option value="shares">Shares</option>
                <option value="adjustment">Adjustment</option>
            </select>
        </div>

        <!-- User-wise Splits -->
        <div id="user-splits-container" class="mb-3" style="display:none;">
            <label class="form-label">Splits</label>
            <div id="user-splits-fields"></div>
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
            <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="500"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Expense</button>
        <a href="{{ route('user.expenses.index') }}" class="btn btn-secondary">Back to Expenses</a>
    </form>
</div>

<script>
const methodSelect = document.getElementById('method');
const groupSelect = document.getElementById('group_id');
const methodContainer = document.getElementById('method-container');
const splitsContainer = document.getElementById('user-splits-container');
const splitsFields = document.getElementById('user-splits-fields');
const amountInput = document.getElementById('amount');
const budgetWarning = document.getElementById('budget-warning');
let groupUsers = [];

// Show/hide Split Method and fetch group users
groupSelect.addEventListener('change', function() {
    const groupId = this.value;
    if (!groupId) {
        methodContainer.style.display = 'none';
        splitsContainer.style.display = 'none';
        groupUsers = [];
        budgetWarning.style.display = 'none';
        return;
    }

    methodContainer.style.display = 'block';

    // Fetch group users
    fetch('/account/group/' + groupId + '/users')
        .then(res => res.json())
        .then(users => {
            groupUsers = users;
            generateSplits();
        });

    // Also check budget when group changes
    checkBudget();
});

// Generate user split inputs based on method
function generateSplits() {
    const method = methodSelect.value;
    splitsFields.innerHTML = '';

    if (method === 'equal' || groupUsers.length === 0) {
        splitsContainer.style.display = 'none';
        return;
    }

    groupUsers.forEach(user => {
        const div = document.createElement('div');
        div.classList.add('mb-2');
        div.innerHTML = `
            <label>${user.name}:</label>
            <input type="number" step="0.01" min="0" name="splits[${user.id}]" class="form-control" required>
        `;
        splitsFields.appendChild(div);
    });
    splitsContainer.style.display = 'block';
}

// Listen for method change
methodSelect.addEventListener('change', generateSplits);

// ===== Budget Checking =====
function checkBudget() {
    const groupId = groupSelect.value;
    const amount = parseFloat(amountInput.value);

    if (!groupId || !amount) {
        budgetWarning.style.display = "none";
        return;
    }

    fetch(`/account/group/${groupId}/check-budget/${amount}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                budgetWarning.style.display = "none";
                return;
            }

            if (data.new_total > data.budget) {
                let msg = `⚠️ Budget exceeded! (Budget: ₹${data.budget}, Spent: ₹${data.spent}, After this: ₹${data.new_total})`;

                if (data.carry_forward > 0) {
                    msg += `<br>✅ Carry Forward available: ₹${data.carry_forward}`;
                }

                budgetWarning.innerHTML = msg;
                budgetWarning.style.color = "red";
                budgetWarning.style.display = "block";
            } else {
                budgetWarning.innerHTML = `✅ Within Budget. Budget Left: ₹${(data.budget - data.new_total).toFixed(2)}`;
                budgetWarning.style.color = "green";
                budgetWarning.style.display = "block";
            }
        });
}

amountInput.addEventListener('input', checkBudget);
</script>
</body>
</html>

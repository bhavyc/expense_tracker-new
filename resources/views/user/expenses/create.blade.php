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
                <option value="">-- None (Personal Expense) --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Budget Info -->
        <div id="budget-left-info" class="mb-3" style="font-weight: 600; display: none;">
            <span id="budget-label"></span>: ₹<span id="budget-left-value">0.00</span>
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
const budgetLeftInfo = document.getElementById('budget-left-info');
const budgetLabel = document.getElementById('budget-label');
const budgetLeftValue = document.getElementById('budget-left-value');
let groupUsers = [];

// Show/hide Split Method and fetch group users
groupSelect.addEventListener('change', function() {
    const groupId = this.value;
    if (!groupId) {
        methodContainer.style.display = 'none';
        splitsContainer.style.display = 'none';
        groupUsers = [];
        budgetWarning.style.display = 'none';

        // Show personal budget info
        budgetLabel.textContent = "Personal Budget Left";
        budgetLeftInfo.style.display = "block";
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

    // Update label for group
    budgetLabel.textContent = "Group Budget Left";
    budgetLeftInfo.style.display = "block";

    // Check immediately
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

    if (!amount || isNaN(amount)) {
        budgetWarning.style.display = "none";
        budgetLeftValue.textContent = "0.00";
        return;
    }

    // ✅ If group selected → check group budget
    if (groupId) {
        fetch(`/account/group/${groupId}/check-budget/${amount}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    budgetWarning.style.display = "none";
                    return;
                }

                const totalAvailable = (parseFloat(data.budget) || 0) + (parseFloat(data.carry_forward) || 0);
                const left = totalAvailable - (parseFloat(data.new_total) || 0);

                if (left < 0) {
                    budgetWarning.innerHTML = `⚠️ Group Budget exceeded! (Budget: ₹${data.budget}, Carry Forward: ₹${data.carry_forward}, Spent: ₹${data.spent}, After this: ₹${data.new_total})`;
                    budgetWarning.style.color = "red";
                } else {
                    budgetWarning.innerHTML = `✅ Within Group Budget. Budget Left: ₹${left.toFixed(2)}`;
                    budgetWarning.style.color = "green";
                }
                budgetWarning.style.display = "block";

                // update top info
                budgetLeftValue.textContent = left.toFixed(2);
            });

    } else {
        //  No group → check personal budget
        fetch(`/account/personal/check-budget/${amount}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    budgetWarning.style.display = "none";
                    return;
                }

                const totalAvailable = (parseFloat(data.budget) || 0) + (parseFloat(data.carry_forward) || 0);
                const left = totalAvailable - (parseFloat(data.new_total) || 0);

                if (left < 0) {
                    budgetWarning.innerHTML = `⚠️ Personal Budget exceeded! (Budget: ₹${data.budget}, Carry Forward: ₹${data.carry_forward}, Spent: ₹${data.spent}, After this: ₹${data.new_total})`;
                    budgetWarning.style.color = "red";
                } else {
                    budgetWarning.innerHTML = `✅ Within Personal Budget. Budget Left: ₹${left.toFixed(2)}`;
                    budgetWarning.style.color = "green";
                }
                budgetWarning.style.display = "block";

                // update top info
                budgetLeftValue.textContent = left.toFixed(2);
            });
    }
}

//   Debounce wrapper
let budgetTimeout = null;
amountInput.addEventListener('input', function () {
    clearTimeout(budgetTimeout);
    budgetTimeout = setTimeout(() => {
        checkBudget();
    }, 3000); // 3 sec delay
});

//  Check immediately on blur
amountInput.addEventListener('blur', checkBudget);
</script>
</body>
</html>

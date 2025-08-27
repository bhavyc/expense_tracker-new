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
        .card { border: none; border-radius: 20px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .btn-success { background-color: #00bfa5; border: none; }
        .btn-success:hover { background-color: #009e88; }
        label { font-weight: 600; }
    </style>
</head>
<body class="p-4 bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h2>Add New Expense</h2>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf

            <!-- User -->
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        @if($user->role !== 'admin')
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Group -->
            <div class="mb-3">
                <label for="group_id" class="form-label">Group</label>
                <select name="group_id" id="group_id" class="form-select">
                    <option value="">None</option>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" required>
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" required>
            </div>

            <!-- Date -->
            <div class="mb-3">
                <label class="form-label">Expense Date</label>
                <input type="date" name="expense_date" class="form-control" required>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" required>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Split Section (Hidden initially) -->
            <div id="split-section" style="display: none;">
                <!-- Split Method -->
                <div class="mb-3">
                    <label for="method" class="form-label">Split Method</label>
                    <select name="method" id="method" class="form-select">
                        <option value="">Select Method</option>
                        <option value="equal">Equally</option>
                        <option value="unequal">Unequally</option>
                        <option value="shares">By Shares</option>
                        <option value="percentage">By Percentage</option>
                        <option value="adjustment">By Adjustment</option>
                    </select>
                </div>

                <!-- Members Splits -->
                <div id="splits-container" class="mt-3"></div>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3">Save Expense</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let groupMembers = [];
    const splitSection = document.getElementById('split-section');
    const methodSelect = document.getElementById('method');

    // Fetch groups by selected user
    document.getElementById('user_id').addEventListener('change', function () {
        const userId = this.value;
        const groupSelect = document.getElementById('group_id');
        groupSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/get-groups-by-user/${userId}`)
            .then(res => res.json())
            .then(data => {
                groupSelect.innerHTML = '<option value="">None</option>';
                data.forEach(group => {
                    groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                });
            })
            .catch(() => {
                groupSelect.innerHTML = '<option disabled>Error loading groups</option>';
            });
    });

    // Fetch group members & toggle split section
    document.getElementById('group_id').addEventListener('change', function () {
        const groupId = this.value;

        if (!groupId) {
            // Hide split section if "None" is selected
            splitSection.style.display = 'none';
            methodSelect.removeAttribute('required');
            groupMembers = [];
            document.getElementById('splits-container').innerHTML = '';
            return;
        }

        // Show split section
        splitSection.style.display = 'block';
        methodSelect.setAttribute('required', 'required');

        // Fetch members
        fetch(`/admin/get-users-by-group/${groupId}`)
            .then(res => res.json())
            .then(data => {
                groupMembers = data;
                document.getElementById('splits-container').innerHTML = '<p class="text-muted">Now select split method</p>';
            })
            .catch(() => {
                groupMembers = [];
                document.getElementById('splits-container').innerHTML = '<p class="text-danger">Error loading members</p>';
            });
    });

    // Generate split inputs based on method
    methodSelect.addEventListener('change', function () {
        const method = this.value;
        const container = document.getElementById('splits-container');
        container.innerHTML = '';

        if (!groupMembers.length) {
            container.innerHTML = '<p class="text-warning">Please select a group first.</p>';
            return;
        }

        if (method === 'equal') {
            container.innerHTML = '<p>Expense will be split equally among all members.</p>';
            groupMembers.forEach(user => {
                container.innerHTML += `<input type="hidden" name="splits[${user.id}]" value="0">`;
            });
        } else {
            groupMembers.forEach(user => {
                container.innerHTML += `
                    <div class="mb-2">
                        <label>${user.name}</label>
                        <input type="number" step="0.01" class="form-control" name="splits[${user.id}]"
                               placeholder="Enter ${method === 'adjustment' ? 'adjusted amount' : method}">
                    </div>
                `;
            });
        }
    });
});
</script>

</body>
</html>

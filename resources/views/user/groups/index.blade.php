<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Groups</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
body { background: #f8f9fa; }
.card { transition: all 0.3s ease; border-radius: 10px; padding: 15px; }
.card:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
.btn-animated { transition: all 0.3s ease; }
.btn-animated:hover { transform: scale(1.05); }
.header-btn { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.group-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
.budget-info { margin-top: 10px; font-weight: 600; }
.budget-left { color: green; }
.budget-over { color: red; }
.badge-permanent { font-size: 0.75rem; background-color: #198754; color: white; padding: 0.2em 0.5em; border-radius: 0.25rem; margin-left: 0.5rem; vertical-align: middle; user-select: none; }
.budget-input { width: 100px; display: inline-block; }
.inline-message { font-size: 0.85rem; margin-left: 10px; }
.carry-forward { font-weight: 600; color: #ff9800; margin-bottom: 5px; }
</style>
</head>
<body class="p-4">

<div class="header-btn">
    <h1>Your Groups</h1>
    <a href="{{ route('user.groups.create') }}" class="btn btn-success btn-animated">+ Add New Group</a>
</div>

@if($groups->isEmpty())
<div class="alert alert-warning">You are not part of any group yet.</div>
@else
@foreach($groups as $group)
@php
    $spent = $group->totalSpent ?? 0;
    $budget = $group->budget ?? 0;
    $carryForward = $group->carry_forward_balance ?? 0;

    // Budget left does NOT include carry forward
    $budgetLeft = $budget - $spent;
    $exceeded = $budgetLeft < 0 ? abs($budgetLeft) : 0;
@endphp

<div class="card mb-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="mb-1">
                {{ $group->name }}
                @if($group->permanent)
                <span class="badge-permanent">Permanent</span>
                @endif
                <span class="badge bg-secondary ms-2">{{ $group->category }}</span>
            </h5>
            <p class="text-muted mb-1">{{ $group->description }}</p>

            <div class="budget-info mt-2">
                @if($carryForward > 0)
                    <div class="carry-forward">Excess Balance: ₹{{ number_format($carryForward, 2) }}</div>
                @endif

                @if($group->created_by === auth()->id())
                <form class="update-budget-form" data-group-id="{{ $group->id }}">
                    <div>
                        Budget: ₹
                        <input type="number" name="budget" value="{{ $group->budget }}" min="0" step="0.01" class="form-control form-control-sm budget-input d-inline-block">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        <span class="inline-message text-success d-none"></span>
                        <span class="inline-message text-danger d-none"></span>
                    </div>
                </form>
                @else
                    Budget: ₹{{ number_format($group->budget, 2) }}
                @endif

                <div>Total Spent: ₹{{ number_format($spent, 2) }}</div>
                <div class="{{ $budgetLeft >= 0 ? 'budget-left' : 'budget-over' }}">
                    Budget Left: ₹{{ number_format(max($budgetLeft, 0), 2) }}
                </div>
                @if($exceeded > 0)
                    <small class="text-danger">Budget exceeded by ₹{{ number_format($exceeded, 2) }}</small>
                @endif
            </div>
        </div>

        <div class="group-actions">
            <a href="{{ route('user.groups.analytics', $group->id) }}" class="btn btn-success btn-sm btn-animated">Weekly</a>
            <a href="{{ route('groups.monthlyAnalytics', $group->id) }}" class="btn btn-info btn-sm btn-animated">Monthly</a>
            <a href="{{ route('user.groups.edit', $group->id) }}" class="btn btn-primary btn-sm btn-animated">Edit</a>
            <form action="{{ route('user.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-animated">Delete</button>
            </form>
        </div>
    </div>

    @if($group->created_by === auth()->id())
    <div class="mt-3">
        <form action="{{ route('user.groups.members.add', $group->id) }}" method="POST" class="d-flex gap-2">
            @csrf
            <select name="phone_number" class="form-select form-select-sm" style="max-width:200px;" required>
                <option value="">Select User</option>
                @foreach($users as $user)
                    @if($user->id !== $group->created_by && $user->role !=='admin')
                        <option value="{{ $user->phone_number }}">{{ $user->phone_number }}</option>
                    @endif
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-success btn-animated">Add Member</button>
        </form>
    </div>
    @endif
</div>
@endforeach
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('submit', '.update-budget-form', function(e) {
    e.preventDefault();
    let form = $(this);
    let groupId = form.data('group-id');
    let budget = form.find('input[name="budget"]').val();
    let successMsg = form.find('.text-success');
    let errorMsg = form.find('.text-danger');

    $.ajax({
        url: `/account/groups/${groupId}/budget`,
        method: 'PATCH',
        data: {
            budget: budget,
            _token: '{{ csrf_token() }}'
        },
        success: function(res) {
            successMsg.addClass('d-none');
            errorMsg.addClass('d-none');

            if (res.success) {
                // Update Budget Left
                form.closest('.budget-info').find('.budget-left-value')
                    .text(parseFloat(res.budgetLeft).toFixed(2));

                // Update Carry Forward
                if(res.carryForward > 0){
                    if(form.closest('.budget-info').find('.carry-forward').length){
                        form.closest('.budget-info').find('.carry-forward').text('Carry Forward: ₹'+parseFloat(res.carryForward).toFixed(2));
                    } else {
                        form.closest('.budget-info').prepend('<div class="carry-forward">Carry Forward: ₹'+parseFloat(res.carryForward).toFixed(2)+'</div>');
                    }
                } else {
                    form.closest('.budget-info').find('.carry-forward').remove();
                }

                // Update budget input
                form.find('input[name="budget"]').val(parseFloat(res.budget).toFixed(2));
                successMsg.removeClass('d-none').text(res.message).fadeOut(3000);
            }
        },
        error: function(err) {
            successMsg.addClass('d-none');
            errorMsg.removeClass('d-none').text('Error updating budget').fadeOut(3000);
        }
    });
});
</script>

</body>
</html>

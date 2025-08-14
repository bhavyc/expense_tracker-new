<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Expense</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f2f5;
    }

     
    .card {
      transition: all 0.3s ease-in-out, box-shadow 0.5s cubic-bezier(0.23, 1, 0.32, 1);
      border: none;
      border-radius: 16px;
      opacity: 0;
      transform: translateY(40px) scale(0.95);
      animation: fadeInUp 0.7s 0.2s forwards;
    }
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    .card:hover {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(33, 150, 243, 0.09);
    }

     
    .form-control, .form-select {
      transition: box-shadow 0.2s ease, border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
      box-shadow: 0 0 5px 2px rgba(0, 123, 255, 0.22), 0 2px 10px rgba(0,0,0,0.02);
      border-color: #80bdff;
      outline: none;
    }

     
    .form-label {
      position: relative;
      padding-left: 8px;
      animation: labelAppear 0.5s;
    }
    @keyframes labelAppear {
      from { opacity: 0; left: 12px;}
      to { opacity: 1; left: 0; }
    }

     
    .btn-primary {
      transition: background 0.2s, transform 0.13s;
      box-shadow: 0 5px 15px rgba(13, 110, 253, 0.13);
    }
    .btn-primary:active {
      background-color: #0360c8;
      transform: scale(0.96);
    }
    .btn-primary:hover {
      background: linear-gradient(90deg,#2262d4,#0056b3 90%);
    }

    
    .form-control.is-invalid, .form-select.is-invalid {
      animation: fieldBounce 0.23s linear;
    }
    @keyframes fieldBounce {
      0% { transform: translateX(0); }
      25% { transform: translateX(-8px);}
      50% { transform: translateX(6px);}
      75% { transform: translateX(-4px);}
      100% { transform: translateX(0);}
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm p-4 bg-white">
        <h3 class="mb-4 text-center text-primary">Edit Expense</h3>
        <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select" required>
              <option value="">Select User</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $expense->user_id == $user->id ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Group (optional)</label>
            <select name="group_id" class="form-select">
              <option value="">None</option>
              @foreach ($groups as $group)
                <option value="{{ $group->id }}" {{ $expense->group_id == $group->id ? 'selected' : '' }}>
                  {{ $group->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" value="{{ $expense->description }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" step="0.01" class="form-control" value="{{ $expense->amount }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ $expense->category }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="pending" {{ $expense->status == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="approved" {{ $expense->status == 'approved' ? 'selected' : '' }}>Approved</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control">{{ $expense->notes }}</textarea>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Update Expense</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>

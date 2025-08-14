<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Group</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      background: #f1f5f9;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .form-label {
      font-weight: 600;
    }

    .btn-primary {
      background-color: #16a085;
      border: none;
    }

    .btn-primary:hover {
      background-color: #12876f;
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="card p-4 col-md-8 col-lg-6 mx-auto">
      <h3 class="text-center mb-4">Create Group</h3>

      <form method="POST" action="{{ route('admin.groups.store') }}">
        @csrf

        <div class="mb-3">
          <label for="name" class="form-label">Group Name</label>
          <input type="text" class="form-control" id="name" name="name" required />
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="mb-3">
          <label for="created_by" class="form-label">Created By</label>
          <select class="form-select" name="created_by" id="created_by" required>
            @foreach ($users as $user)
              @if ($user->role != 'admin')
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endif
            @endforeach
          </select>
        </div>

        <!-- New: Budget -->
        <div class="mb-3">
          <label for="budget" class="form-label">Budget (₹)</label>
          <input type="number" class="form-control" name="budget" id="budget" step="0.01" min="0" placeholder="0">
        </div>

        <!-- New: Permanent -->
        <div class="mb-3">
          <label for="is_permanent" class="form-label">Permanent</label>
          <select class="form-select" name="is_permanent" id="is_permanent">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Save Group</button>
      </form>

      @if(session('success'))
        <div class="alert alert-success mt-3 text-center">
          {{ session('success') }}
        </div>
      @endif

      <div class="back-link">
        <a href="{{ route('admin.groups.index') }}" class="text-decoration-underline">← Back to Groups</a>
      </div>
    </div>
  </div>

</body>
</html>

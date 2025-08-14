<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Group</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f0f2f5;
    }

    .card {
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    .form-control:focus, .form-select:focus {
      border-color: #86b7fe;
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
    }

    .btn-back {
      text-decoration: none;
      margin-top: 1rem;
      display: inline-block;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card p-4 bg-white">
        <h3 class="text-center text-primary mb-4">Create Group</h3>

        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form action="{{ route('admin.groups.store') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label">Group Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Created By (User)</label>
            <select name="created_by" class="form-select" required>
              <option value="">-- Select User --</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Budget (₹)</label>
            <input type="number" name="budget" class="form-control" step="0.01" min="0">
          </div>

         
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_permanent" name="permanent" value="1">
            <label class="form-check-label" for="is_permanent">Permanent</label>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Save Group</button>
          </div>
        </form>

        <a href="{{ route('admin.groups.index') }}" class="btn btn-link btn-back text-decoration-none mt-3">← Back to Groups</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

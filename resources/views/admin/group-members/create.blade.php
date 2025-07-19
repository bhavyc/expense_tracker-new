<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Group Member</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: translateY(-4px);
    }
    .form-select:focus, .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
      border-color: #86b7fe;
    }
    .btn-primary {
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 bg-white">
        <h3 class="mb-4 text-center text-primary">Add Group Member</h3>

        <form action="{{ route('admin.group-members.store') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label">Select Group</label>
            <select name="group_id" class="form-select" required>
              <option value="">-- Select Group --</option>
              @foreach ($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Select User</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Select User --</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Add Member</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

</body>
</html>

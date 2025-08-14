<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Groups</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
      padding: 2rem;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .table th, .table td {
      vertical-align: middle;
    }

    .table-hover tbody tr:hover {
      background-color: #e6f7ea;
      transition: 0.3s ease-in-out;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
      transition: 0.3s ease-in-out;
    }

    .btn-success:hover {
      background-color: #218838;
      transform: scale(1.05);
    }

    .badge {
      font-size: 0.85rem;
      padding: 0.35em 0.65em;
    }

    .fade-in {
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="container fade-in">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-success fw-bold">All Groups</h3>
    <a href="{{ route('admin.groups.create') }}" class="btn btn-success">+ Add Group</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card p-3 bg-white">
    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle">
        <thead class="table-success text-dark">
          <tr>
            <th>#</th>
            <th>Group Name</th>
            <th>Description</th>
            <th>Budget</th>
            <th>Permanent</th>
            <th>Created By</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($groups as $key => $group)
          <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $group->name }}</td>
            <td>{{ $group->description }}</td>
            <td>
              @if(!is_null($group->budget))
                ₹{{ number_format($group->budget, 2) }}
              @else
                <span class="text-muted">Not Set</span>
              @endif
            </td>
            <td>
              @if($group->permanent)
                <span class="badge bg-success">Yes</span>
              @else
                <span class="badge bg-secondary">No</span>
              @endif
            </td>
            <td>{{ $group->creator->name ?? 'N/A' }}</td>
            <td>
              <a href="{{ route('admin.groups.edit', $group->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
              <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this group?')">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach

          @if ($groups->isEmpty())
            <tr>
              <td colspan="7" class="text-center text-muted">No groups found.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>

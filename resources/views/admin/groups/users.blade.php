<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $group->name }} - Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="card shadow p-4">
    <h3 class="mb-3 text-success">👥 Users in Group: {{ $group->name }}</h3>
    <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-secondary mb-3">⬅ Back to Groups</a>

    <table class="table table-bordered table-hover align-middle">
      <thead class="table-success">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Joined On</th>
        </tr>
      </thead>
      <tbody>
        @forelse($group->users as $key => $user)
        <tr>
          <td>{{ $key + 1 }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center text-muted">No users in this group.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

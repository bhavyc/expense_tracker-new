<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Group Members</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .group-card {
      animation: fadeIn 0.5s ease-in-out;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 1rem;
      padding: 1.5rem;
      background: #fff;
      margin-bottom: 1.5rem;
      transition: transform 0.3s ease;
    }

    .group-card:hover {
      transform: translateY(-3px);
    }

    .remove-btn {
      transition: all 0.3s ease;
    }

    .remove-btn:hover {
      background-color: #dc3545 !important;
      color: #fff !important;
      transform: scale(1.05);
    }

    .list-group-item {
      background-color: #fefefe;
      border-left: 4px solid #0d6efd;
      border-radius: 0.5rem;
      margin-bottom: 8px;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="container py-5">

 <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
  <h2 class="fw-bold text-primary">👥 Group Members</h2>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
      🏠 Dashboard
    </a>
    <a href="{{ route('admin.group-members.create') }}" class="btn btn-success shadow-sm">
      ➕ Add Group Member
    </a>
  </div>
</div>


  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @php
    $grouped = $members->groupBy('group_id');
  @endphp

  @forelse($grouped as $groupId => $groupMembers)
    @php
      $group = $groupMembers->first()->group;
    @endphp

    <div class="group-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
          <span class="text-secondary">Group {{ $loop->iteration}}</span> - 
          <span class="text-primary fw-semibold">{{ $group->name }}</span>
        </h5>
        <span class="badge bg-primary">{{ $groupMembers->count() }} Member{{ $groupMembers->count() > 1 ? 's' : '' }}</span>
      </div>

      <div class="mb-2 fw-semibold text-muted">Members:</div>

      @if($groupMembers->isEmpty())
        <p class="text-muted"><em>No members</em></p>
      @else
        <ul class="list-group list-group-flush">
          @foreach($groupMembers as $member)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>{{ $member->user->name }}</span>
              <form method="POST" action="{{ route('admin.group-members.destroy', $member->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm remove-btn"
                  onclick="return confirm('Are you sure you want to remove {{ $member->user->name }}?')">
                  Remove
                </button>
              </form>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  @empty
    <div class="alert alert-info">No group members found.</div>
  @endforelse

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Groups</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card {
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 15px;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .btn-animated { transition: all 0.3s ease; }
        .btn-animated:hover { transform: scale(1.05); }
        .header-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .group-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body class="p-4">

    <div class="header-btn">
        <h1>Your Groups</h1>
        <a href="{{ route('user.groups.create.form') }}" class="btn btn-success btn-animated">+ Add New Group</a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($groups->isEmpty())
        <div class="alert alert-warning">You are not part of any group yet.</div>
    @else
        @foreach($groups as $group)
            <div class="card mb-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $group->name }}</h5>
                        <p class="text-muted mb-1">{{ $group->description }}</p>
                        <small class="text-secondary">Created on: {{ $group->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="group-actions">
                        <a href="{{ route('user.groups.edit', $group->id) }}" class="btn btn-primary btn-sm btn-animated">Edit</a>
                        <form action="{{ route('user.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-animated">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</body>
</html>

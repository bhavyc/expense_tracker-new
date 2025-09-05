<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f0f4f8);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .fade-slide-in {
            animation: fadeSlideIn 0.8s ease-in-out;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
            animation: fadeSlideIn 1s ease;
        }

        .table thead {
            background-color: #0d6efd;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f1f9ff;
            transition: background-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-danger:hover,
        .btn-outline-success:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .back-btn {
            transition: all 0.3s ease-in-out;
        }

        .back-btn:hover {
            background-color: #d9e0e7;
            transform: translateX(-2px);
        }

        .badge {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container my-5 fade-slide-in">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                Dashboard
            </a>
            <h2 class="mb-0 text-center flex-grow-1">👥 All Registered Users</h2>
            <div style="width: 160px;"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined On</th>
                    <th>Expenses</th>
                    <th>Total Spent</th>
                    <th>Groups</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr class="fade-slide-in">
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>{{ $user->expenses->count() }}</td>
                        <td>₹{{ number_format($user->expenses->sum('amount'), 2) }}</td>
                        <td>
                            @if($user->groups->count() > 0)
                                @foreach($user->groups as $group)
                                    <a href="{{ route('admin.groups.users', $group->id) }}" 
                                       class="badge bg-info text-dark text-decoration-none me-1">
                                        {{ $group->name }}
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted">No Groups</span>
                            @endif
                        </td>
                        <td>
                            <!-- ✅ Chat Button -->
                            <a href="{{ route('admin.chat', $user->id) }}" class="btn btn-sm btn-outline-success me-1">
                                <i class="bi bi-chat-dots"></i> Chat
                            </a>

                            <!-- Update Button -->
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                Update
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
</body>
</html>

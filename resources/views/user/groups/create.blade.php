<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-animated {
            transition: all 0.3s ease;
        }
        .btn-animated:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="p-4">

    <div class="form-container">
        <h1 class="mb-4 text-center">Create New Group</h1>

        {{-- Only show if user is authenticated --}}
        @auth

            {{-- Success message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.groups.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Group Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Group Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control" required>{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-animated">
                        <i class="bi bi-plus-circle"></i> Create Group
                    </button>
                    <a href="{{ route('user.groups.index') }}" class="btn btn-secondary btn-animated">
                        Back to Groups
                    </a>
                </div>
            </form>

        @else
            <div class="alert alert-danger text-center">You must be logged in to create a group.</div>
            <div class="text-center mt-3">
                <a href="{{ route('account.login') }}" class="btn btn-primary">Login</a>
            </div>
        @endauth
    </div>

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>

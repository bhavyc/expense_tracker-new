<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <h1 class="mb-4">Edit Group</h1>

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

    <form action="{{ route('user.groups.update', $group->id) }}" method="POST" class="border p-4 rounded shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Group Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Group Description</label>
            <textarea name="description" id="description" rows="3" class="form-control" required>{{ old('description', $group->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Group</button>
        <a href="{{ route('user.groups.index') }}" class="btn btn-secondary">Back to Groups</a>
    </form>

</body>
</html>

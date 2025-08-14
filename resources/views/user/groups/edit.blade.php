<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

    <div class="container" style="max-width: 600px;">
        <h1 class="mb-4">Edit Group</h1>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        

        <form action="{{ route('user.groups.update', $group->id) }}" method="POST" class="border p-4 rounded shadow-sm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Group Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $group->name) }}"
                    class="form-control"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Group Description</label>
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    class="form-control"
                    >{{ old('description', $group->description) }}</textarea>
            </div>

            <!-- Budget Field
            <div class="mb-3">
                <label for="budget" class="form-label">Group Budget (₹)</label>
                <input
                    type="number"
                    name="budget"
                    id="budget"
                    step="0.01"
                    min="0"
                    value="{{ old('budget', $group->budget) }}"
                    class="form-control"
                    required
                />
            </div> -->

            <!-- Permanent Group Checkbox -->
            <div class="form-check mb-3">
    <input
        type="checkbox"
        name="permanent"
        id="permanent"
        value="1"
        class="form-check-input"
        {{ old('permanent', $group->permanent) ? 'checked' : '' }}
    />
    <label class="form-check-label" for="permanent">
        Permanent Group
    </label>
</div>

            <button type="submit" class="btn btn-primary">Update Group</button>
            <a href="{{ route('user.groups.index') }}" class="btn btn-secondary ms-2">Back to Groups</a>
        </form>
    </div>

</body>
</html>

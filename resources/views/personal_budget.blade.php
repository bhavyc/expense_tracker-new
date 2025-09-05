<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Personal Budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Set Your Personal Budget</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    

    <form action="{{ route('budget.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="personal_budget" class="form-label">Personal Budget</label>
            <input type="number" step="0.01" name="personal_budget" id="personal_budget" class="form-control" value="{{ $user->personal_budget ?? '' }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Budget</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

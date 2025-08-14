<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Submit Feedback</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('feedback.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label for="subject" class="form-label">Subject</label>
      <input type="text" name="subject" id="subject" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="message" class="form-label">Your Message</label>
      <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
    </div>

    <button class="btn btn-primary">Submit Feedback</button>
  </form>
</div>
</body>
</html>

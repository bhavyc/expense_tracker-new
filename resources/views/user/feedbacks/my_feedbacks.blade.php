<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Feedbacks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>My Submitted Feedbacks</h2>

  @if($feedbacks->count() === 0)
    <div class="alert alert-info">You haven't submitted any feedback yet.</div>
  @endif

  <table class="table table-striped mt-3">
    <thead class="table-dark">
      <tr>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th>Admin Reply</th>
        <th>Submitted On</th>
      </tr>
    </thead>
    <tbody>
      @foreach($feedbacks as $feedback)
        <tr>
          <td>{{ $feedback->subject }}</td>
          <td>{{ $feedback->message }}</td>
          <td>
            @if($feedback->status === 'answered')
              <span class="badge bg-success">Answered</span>
            @else
              <span class="badge bg-warning text-dark">Pending</span>
            @endif
          </td>
          <td>
            @if($feedback->admin_reply)
              <div class="text-success">{{ $feedback->admin_reply }}</div>
            @else
              <div class="text-muted">No reply yet</div>
            @endif
          </td>
          <td>{{ $feedback->created_at->format('d M, Y') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Feedback Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>User Feedbacks</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered table-hover mt-4">
    <thead class="table-dark">
      <tr>
        <th>User</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th>Admin Reply</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($feedbacks as $feedback)
        <tr>
          <td>{{ $feedback->user->name }}</td>
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
          <td>
            @if($feedback->status === 'pending')
              <form action="{{ route('admin.feedbacks.reply', $feedback->id) }}" method="POST">
                @csrf
                <div class="input-group">
                  <textarea name="admin_reply" class="form-control" placeholder="Type your reply..." required></textarea>
                  <button class="btn btn-sm btn-success" type="submit">Reply</button>
                </div>
              </form>
            @else
              <button class="btn btn-secondary btn-sm" disabled>Replied</button>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center text-muted">No feedbacks submitted yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
</body>
</html>

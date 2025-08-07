<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #a8edea, #fed6e3);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      padding: 2.5rem;
      width: 100%;
      max-width: 500px;
      animation: slideFadeIn 0.8s ease-out forwards;
      opacity: 0;
      transform: translateY(20px);
    }

    @keyframes slideFadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.4);
      border: none;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.6);
      box-shadow: 0 0 0 0.2rem rgba(72, 239, 133, 0.4);
      border-color: #28a745;
    }

    .btn-primary {
      background-color: #28a745;
      border: none;
      transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #218838;
      transform: scale(1.03);
    }

    .btn-primary:active {
      transform: scale(0.97);
    }

    a.link-secondary:hover {
      color: #28a745 !important;
      text-decoration: underline;
    }

    .error-alert {
      background-color: rgba(255, 0, 0, 0.1);
      border: 1px solid rgba(255, 0, 0, 0.2);
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <div class="glass-card">
    <h4 class="text-center mb-4 fw-semibold">📝 Admin Registration</h4>

    @if ($errors->any())
      <div class="alert alert-danger error-alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.register') }}" class="mt-4">
      @csrf
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
        <label for="name">Full Name</label>
      </div>

      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        <label for="email">Email</label>
      </div>

      <div class="form-floating mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>

      <div class="form-floating mb-4">
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
        <label for="password_confirmation">Confirm Password</label>
      </div>

      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary py-2">Register Admin</button>
      </div>

      <div class="text-center">
        <a href="{{ route('admin.login') }}" class="link-secondary text-decoration-none">Already have an account? Log in</a>
      </div>
    </form>
  </div>
</body>
</html>

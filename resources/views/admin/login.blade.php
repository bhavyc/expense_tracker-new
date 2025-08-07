<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, #a8edea, #fed6e3);
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      width: 100%;
      max-width: 420px;
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

    .text-center h4 {
      font-weight: 600;
      color: #333;
    }

    a.link-secondary:hover {
      color: #28a745 !important;
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .glass-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="glass-card">
    <div class="text-center mb-4">
      <h4>🌿 Admin Panel Login</h4>
    </div>
    <form action="{{ route('admin.authenticate') }}" method="POST" class="needs-validation" novalidate>
      @csrf
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
        <label for="email">Email</label>
      </div>
      <div class="form-floating mb-4">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary py-2">Log In</button>
      </div>
    </form>
    <hr class="my-4 border-light">
    <div class="text-center">
      <a href="{{ route('admin.register') }}" class="link-secondary text-decoration-none">Create new account</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

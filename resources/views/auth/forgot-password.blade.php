<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <section class="p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card border border-light-subtle rounded-4 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="text-center mb-4">Forgot Password</h2>

                            <!-- Status Message -->
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
                                    <label for="email">Email address</label>
                                </div>
                                             
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary py-2">Send Reset Link</button>
                                </div>

                                <div class="text-center">
                                    <a href="{{ route('account.login') }}" class="text-decoration-none">Back to Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

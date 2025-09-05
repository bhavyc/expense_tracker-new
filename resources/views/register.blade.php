<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Register</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <section class="p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                    <div class="card border border-light-subtle rounded-4">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-5">
                                        <h4 class="text-center">Register Here</h4>
                                    </div>
                                </div>
                            </div>

                           

                            <form action="{{ route('account.registerUser') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                @csrf
                                <div class="row gy-3 overflow-hidden">

                                    <!-- Name -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ old('name') }}" required>
                                            <label for="name" class="form-label">Name</label>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                                            <label for="email" class="form-label">Email</label>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Enter Phone Number" value="{{ old('phone_number') }}" required>
                                            <label for="phone_number" class="form-label">Phone Number</label>
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="country" id="country" placeholder="Enter Country" value="{{ old('country') }}">
                                            <label for="country" class="form-label">Country</label>
                                        </div>
                                    </div>

                                    <!-- City -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="city" id="city" placeholder="Enter City" value="{{ old('city') }}">
                                            <label for="city" class="form-label">City</label>
                                        </div>
                                    </div>

                                    <!-- Financial Goal -->
                                   <div class="col-12">
    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="financial_goal" id="financial_goal" placeholder="Enter Financial Goal" value="{{ old('financial_goal') }}">
        <label for="financial_goal" class="form-label">Financial Goal</label>
    </div>
</div>

                                    <!-- Gender -->
                                    <div class="col-12">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender" id="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <!-- Occupation -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="occupation" id="occupation" placeholder="Enter Occupation" value="{{ old('occupation') }}">
                                            <label for="occupation" class="form-label">Occupation</label>
                                        </div>
                                    </div>

                                    <!-- Profile Picture
                                    <div class="col-12">
                                        <label for="profile_pic" class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control" name="profile_pic" id="profile_pic" accept="image/*">
                                    </div> -->

                                    <!-- Password -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                            <label for="password" class="form-label">Password</label>
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="password_confirmation" id="confirm_password" placeholder="Confirm Password" required>
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button class="btn bsb-btn-xl btn-primary py-3" type="submit">Register Now</button>
                                        </div>
                                    </div>

                                </div>
                            </form>

                            <div class="row">
                                <div class="col-12">
                                    <hr class="mt-5 mb-4 border-secondary-subtle">
                                    <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-center">
                                        <a href="{{ route('account.login') }}" class="link-secondary text-decoration-none">Click here to login</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

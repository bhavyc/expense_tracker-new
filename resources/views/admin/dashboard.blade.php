<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@lang('Admin Dashboard')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: linear-gradient(to bottom, #2e7d32, #66bb6a);
      padding-top: 60px;
      box-shadow: 2px 0 12px rgba(0,0,0,0.1);
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 24px;
      color: #e8f5e9;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .sidebar a i {
      font-size: 18px;
      transition: transform 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      border-left: 5px solid #a5d6a7;
      padding-left: 30px;
    }

    .sidebar a:hover i {
      transform: scale(1.1);
    }

    .navbar {
      background: linear-gradient(to right, #ffffff, #e8f5e9);
      border-bottom: 1px solid #c8e6c9;
      z-index: 1050;
    }

    .content {
      padding: 30px;
      margin-top: 70px;
    }

    @media (min-width: 768px) {
      .content {
        margin-left: 260px;
      }
    }

    .card {
      border: none;
      border-radius: 16px;
      background: linear-gradient(145deg, #ffffff, #dcedc8);
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      animation: fadeIn 0.8s ease-in-out;
    }

    .card:hover {
      transform: translateY(-5px) scale(1.015);
      box-shadow: 0 14px 28px rgba(0,0,0,0.08);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card h4 {
      color: #2e7d32;
      font-weight: bold;
    }

    .card small {
      color: #555;
    }

    h6 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #2e7d32;
    }

    canvas {
      background-color: #fff;
      border-radius: 12px;
      padding: 10px;
    }

    .sidebar select {
      display: block;
      padding: 10px 15px;
      color: #fff;
      background-color: #388e3c;
      border: none;
      width: 100%;
      text-align: left;
      border-left: 4px solid transparent;
      background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 16px;
      padding-right: 30px;
      font-size: 16px;
      cursor: pointer;
    }

    .sidebar select:hover,
    .sidebar select:focus {
      background-color: #2e7d32;
      border-left: 4px solid #a5d6a7;
      outline: none;
    }
  </style>
</head>
<body>

<!-- Sidebar for large screens -->
<div class="sidebar d-none d-md-block">
  <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> @lang('messages.Analytics')</a>
  <a href="/admin/users"><i class="bi bi-people"></i> @lang('messages.Users')</a>
  <a href="/admin/expenses"><i class="bi bi-wallet2"></i> @lang('messages.Expenses')</a>
  <a href="/admin/groups"><i class="bi bi-collection"></i> @lang('messages.Groups')</a>
  <a href="/admin/group-members"><i class="bi bi-person-lines-fill"></i> @lang('messages.Group Members')</a>
  <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> @lang('messages.Reports')</a>
  <a href="/admin/apis"><i class="bi bi-code-slash"></i> @lang('messages.APIs')</a>
  <!-- <a href="/admin/feedbacks"><i class="bi bi-code-slash"></i> @lang('messages.feedbacks')</a> -->
 <form method="GET" action="{{ route('change.language') }}">
    <select name="lang" onchange="this.form.submit()">
        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
        <option value="hi" {{ app()->getLocale() === 'hi' ? 'selected' : '' }}>Hindi</option>
    </select>
</form>



</div>

 
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-success">@lang('messages.Admin Panel')</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="sidebar bg-transparent h-100">
      <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> @lang('messages.Analytics')</a>
      <a href="/admin/users"><i class="bi bi-people"></i> @lang('messages.Users')</a>
      <a href="/admin/expenses"><i class="bi bi-wallet2"></i> @lang('messages.Expenses')</a>
      <a href="/admin/groups"><i class="bi bi-collection"></i> @lang('messages.Groups')</a>
      <a href="/admin/group-members"><i class="bi bi-person-lines-fill"></i> @lang('messages.Group Members')</a>
      <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> @lang('messages.Reports')</a>
      <a href="/admin/apis"><i class="bi bi-code-slash"></i> @lang('messages.APIs')</a>
      <form method="GET" action="{{ route('change.language') }}">
        <select name="lang" id="country1" onchange="this.form.submit()">
          <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
          <option value="hi" {{ app()->getLocale() === 'hi' ? 'selected' : '' }}>Hindi</option>
        </select>
      </form>
    </div>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar fixed-top shadow-sm px-3">
  <div class="container-fluid">
    <button class="btn btn-outline-success d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="fw-semibold text-success">
        👤 {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
      </span>
      <form method="GET" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-success btn-sm">
          <i class="bi bi-box-arrow-right"></i> @lang('Logout')
        </button>
      </form>
    </div>
  </div>
</nav>

<!-- @if(session('lang_set'))
    <div class="alert alert-success text-center">
        Language changed to: <strong>{{ session('lang_set') }}</strong>
    </div>
@endif -->

<!-- Content -->
<div class="content">
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">@lang('messages.Total Users')</small>
        <h4>{{ $totalUsers }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">@lang('messages.Total Expenses')</small>
        <h4>{{ number_format($totalExpenses) }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">@lang('messages.Groups')</small>
        <h4>{{ $totalGroups }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">@lang('messages.Active Splits')</small>
        <h4>{{ $totalSplits }}</h4>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h6>📈 @lang('messages.Expense Trend')</h6>
        <canvas id="lineChart"></canvas>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card p-4">
        <h6>📊 @lang('messages.Category Breakdown')</h6>
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: @json($expenseTrendLabels),
      datasets: [{
        label: '{{ __("Expenses") }}',
        data: @json($expenseTrendData),
        // borderColor: '#2e7d32',
        backgroundColor: 'rgba(76, 175, 80, 0.2)',
        fill: false,
        borderWidth: 0,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 8,
          // pointBorderWidth: 3,          // Bold border
  pointBackgroundColor: '#2e7d32',
  pointBorderColor: '#1b5e20', 
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      animation: {
        duration: 1500,
        easing: 'easeOutCubic'
      }
    }
  });

  const pieCtx = document.getElementById('pieChart').getContext('2d');
  new Chart(pieCtx, {
    type: 'doughnut',
    data: {
      labels: @json($categoryLabels),
      datasets: [{
        data: @json($categoryData),
        backgroundColor: ['#2e7d32', '#66bb6a', '#a5d6a7', '#c8e6c9'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      animation: {
        animateScale: true,
        animateRotate: true
      }
    }
  });
</script>
  <script>
      document.getElementById('langSelect').addEventListener('change', function() {
    const lang = this.value;
    const form = document.getElementById('langForm');
    form.action = '/change-language/' + lang;
    form.submit();
  });
  </script>
</body>
</html>

 
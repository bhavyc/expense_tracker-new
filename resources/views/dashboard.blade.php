<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('messages.analytics') }} - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #f4fff4, #e9f9ec);
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: linear-gradient(to bottom, #11998e, #38ef7d);
      padding-top: 60px;
      box-shadow: 2px 0 12px rgba(0,0,0,0.1);
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 24px;
      color: #eaffea;
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
      border-left: 5px solid #00c851;
      padding-left: 30px;
    }

    .sidebar a:hover i {
      transform: scale(1.1);
    }

    .navbar {
      background: linear-gradient(to right, #ffffff, #f0fff0);
      border-bottom: 1px solid #d0f0d0;
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
      box-shadow: 0 6px 20px rgba(0,0,0,0.06);
      background: linear-gradient(145deg, #ffffff, #e0ffe0);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeIn 0.7s ease-in-out;
    }

    .card:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card h4 {
      color: #11998e;
      font-weight: bold;
    }

    .card small {
      color: #6b7280;
    }

    h6 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #11998e;
    }

    canvas {
      background-color: #fff;
      border-radius: 12px;
      padding: 10px;
    }

    select {
      background-color: #11998e;
      color: #fff;
      appearance: none;
      font-size: 16px;
      border: none;
      border-left: 4px solid transparent;
      background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 16px;
      padding: 8px 30px 8px 10px;
      margin: 10px 15px;
      width: calc(100% - 30px);
      border-radius: 5px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-none d-md-block">
  <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> {{ __('messages.analytics') }}</a>  
  <a href="{{ route('account.index') }}"><i class="bi bi-people"></i> {{ __('messages.group_members') }}</a>
  <a href="{{ route('user.expenses.index') }}"><i class="bi bi-wallet2"></i> {{ __('messages.expenses') }}</a>
  <a href="/admin/groups"><i class="bi bi-collection"></i> {{ __('messages.groups') }}</a>
  <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> {{ __('messages.reports') }}</a>
  <a href="{{ route('feedback.form') }}"><i class="bi bi-people"></i> {{ __('messages.feedback_query') }}</a>
<!-- <a href="{{ route('feedback.my') }}"><i class="bi bi-people"></i> {{ __('messages.my_feedbacks') }}</a> -->
  <!-- Language switch -->
  <form method="GET" action="{{ route('change.language') }}">
    <select name="lang" onchange="this.form.submit()">
      <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
      <option value="hi" {{ app()->getLocale() == 'hi' ? 'selected' : '' }}>हिन्दी</option>
    </select>
  </form>
</div>

<!-- Mobile Offcanvas -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-success">{{ Auth::user()->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="sidebar bg-transparent h-100">
      <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> {{ __('messages.analytics') }}</a>
      <a href="{{ route('account.index') }}"><i class="bi bi-people"></i> {{ __('messages.group_members') }}</a>
      <a href="{{ route('user.expenses.index') }}"><i class="bi bi-wallet2"></i> {{ __('messages.expenses') }}</a>
      <a href="/admin/groups"><i class="bi bi-collection"></i> {{ __('messages.groups') }}</a>
      <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> {{ __('messages.reports') }}</a>

      <form method="GET" action="{{ route('change.language') }}">
        <select name="lang" onchange="this.form.submit()">
          <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
          <option value="hi" {{ app()->getLocale() == 'hi' ? 'selected' : '' }}>हिन्दी</option>
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
      <span class="fw-semibold text-success">👤 {{ Auth::user()->name }}</span>
      <form method="GET" action="{{ route('account.logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-success btn-sm">
          <i class="bi bi-box-arrow-right"></i> {{ __('messages.logout') }}
        </button>
      </form>
    </div>
  </div>
</nav>

<!-- Content -->
<div class="content">
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">{{ __('messages.total_users') }}</small>
        <h4>{{ $totalUsers }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">{{ __('messages.total_expenses') }}</small>
        <h4>₹{{ number_format($totalExpenses, 2) }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">{{ __('messages.groups') }}</small>
        <h4>{{ $totalGroups }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">{{ __('messages.active_splits') }}</small>
        <h4>{{ $totalSplits }}</h4>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h6>📈 {{ __('messages.expense_trend') ?? 'Expense Trend' }}</h6>
        <canvas id="lineChart"></canvas>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card p-4">
        <h6>📊 {{ __('messages.category_breakdown') ?? 'Category Breakdown' }}</h6>
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const expenseTrendLabels = {!! json_encode($expenseTrendLabels) !!};
  const expenseTrendData = {!! json_encode($expenseTrendData) !!};
  const categoryLabels = {!! json_encode($categoryLabels) !!};
  const categoryTotals = {!! json_encode($categoryTotals) !!};

  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: expenseTrendLabels,
      datasets: [{
        label: 'Expenses',
        data: expenseTrendData,
        borderColor: '#00b894',
        backgroundColor: 'rgba(0, 184, 148, 0.2)',
        fill: true,
        tension: 0.4
      }]
    }
  });

  new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
      labels: categoryLabels,
      datasets: [{
        data: categoryTotals,
        backgroundColor: ['#00b894', '#55efc4', '#81ecec', '#a3cb38', '#26de81']
      }]
    }
  });
</script>

</body>
</html>

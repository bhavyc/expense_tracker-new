



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #fdfbfb, #ebedee);
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: linear-gradient(to bottom, #6a11cb, #2575fc);
      padding-top: 60px;
      box-shadow: 2px 0 12px rgba(0,0,0,0.1);
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 24px;
      color: #e0e8ff;
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
      border-left: 5px solid #ff80ab;
      padding-left: 30px;
    }

    .sidebar a:hover i {
      transform: scale(1.1);
    }

    .navbar {
      background: linear-gradient(to right, #ffffff, #f5f5ff);
      border-bottom: 1px solid #e0e0f0;
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
      background: linear-gradient(145deg, #ffffff, #f3eaff);
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
      color: #6a11cb;
      font-weight: bold;
    }

    .card small {
      color: #6b7280;
    }

    h6 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #6a11cb;
    }

    canvas {
      background-color: #fff;
      border-radius: 12px;
      padding: 10px;
    }
  </style>
</head>
<body>

<!-- Sidebar for large screens -->
<div class="sidebar d-none d-md-block">
  <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> Analytics</a>
  <a href="/admin/users"><i class="bi bi-people"></i> Users</a>
  <a href="/admin/expenses"><i class="bi bi-wallet2"></i> Expenses</a>
  <a href="/admin/groups"><i class="bi bi-collection"></i> Groups</a>
  <a href="/admin/group-members"><i class="bi bi-person-lines-fill"></i> Group Members</a>
  <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> Reports</a>
</div>

<!-- Offcanvas Sidebar for mobile -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-primary">Admin Panel</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="sidebar bg-transparent h-100">
      <a href="/admin/analytics" class="active"><i class="bi bi-bar-chart"></i> Analytics</a>
      <a href="/admin/users"><i class="bi bi-people"></i> Users</a>
      <a href="/admin/expenses"><i class="bi bi-wallet2"></i> Expenses</a>
      <a href="/admin/groups"><i class="bi bi-collection"></i> Groups</a>
      <a href="/admin/group-members"><i class="bi bi-person-lines-fill"></i> Group Members</a>
      <a href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-text"></i> Reports</a>
    </div>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar fixed-top shadow-sm px-3">
  <div class="container-fluid">
    <button class="btn btn-outline-primary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="fw-semibold text-primary">
        👤 {{ Auth::user()->name ?? 'Admin' }}
      </span>
      <form method="GET" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-box-arrow-right"></i> Logout
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
        <small class="text-muted">Total Users</small>
        <h4>{{ $totalUsers }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">Total Expenses</small>
        <h4>{{ number_format($totalExpenses) }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">Groups</small>
        <h4>{{ $totalGroups }}</h4>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card p-4 text-center">
        <small class="text-muted">Active Splits</small>
        <h4>{{ $totalSplits }}</h4>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h6>📈 Expense Trend</h6>
        <canvas id="lineChart"></canvas>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card p-4">
        <h6>📊 Category Breakdown</h6>
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
        label: 'Expenses',
        data: @json($expenseTrendData),
        borderColor: '#6a11cb',
        backgroundColor: 'rgba(106, 17, 203, 0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 8
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      animation: {
        duration: 1500,
        easing: 'easeOutQuart'
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
        backgroundColor: ['#6a11cb', '#ff80ab', '#00bcd4', '#9575cd'],
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

</body>
</html>

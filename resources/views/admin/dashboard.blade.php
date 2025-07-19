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
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background-color: #fff;
      border-right: 1px solid #dee2e6;
      padding-top: 60px;
    }
    .sidebar a {
      display: block;
      padding: 14px 24px;
      color: #333;
      text-decoration: none;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #e9ecef;
      border-left: 3px solid #0d6efd;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
    }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      transition: transform 0.2s ease;
    }
    .card:hover {
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <a href="#" class="active"><i class="bi bi-bar-chart"></i> Analytics</a>
  <a href="#"><i class="bi bi-people"></i> Users</a>
  <a href="#"><i class="bi bi-wallet2"></i> Expenses</a>
  <a href="#"><i class="bi bi-collection"></i> Groups</a>
  <a href="#"><i class="bi bi-gear"></i> Settings</a>
</div>

<!-- Top Navbar -->
<!-- <nav class="navbar navbar-light bg-white shadow-sm fixed-top" style="margin-left:250px;">
  <div class="container-fluid justify-content-end">
    <form class="d-flex">
      <input class="form-control me-2" type="search" placeholder="Search" />
      <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </form>
  </div>
</nav> -->

<!-- Main Content -->
<div class="content">
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card p-3">
        <small>Total Users</small>
        <h4>{{ $totalUsers }}</h4>
        <span class="text-success">↑</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Total Expenses</small>
        <h4>₹{{ number_format($totalExpenses) }}</h4>
        <span class="text-danger">↓</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Groups</small>
        <h4>{{ $totalGroups }}</h4>
        <span class="text-success">↑</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <small>Active Splits</small>
        <h4>{{ $totalSplits }}</h4>
        <span class="text-success">↑</span>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h6>Expense Trend</h6>
        <canvas id="lineChart"></canvas>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card p-4">
        <h6>Category Breakdown</h6>
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
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
        borderColor: '#0d6efd',
        fill: false,
        tension: 0.4
      }]
    },
    options: { responsive: true }
  });

  const pieCtx = document.getElementById('pieChart').getContext('2d');
  new Chart(pieCtx, {
    type: 'doughnut',
    data: {
      labels: @json($categoryLabels),
      datasets: [{
        data: @json($categoryData),
        backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
</script>

</body>
</html>

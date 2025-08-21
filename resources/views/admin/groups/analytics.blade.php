 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $group->name }} - Analytics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light p-4">

<div class="container">
  <h3 class="mb-3 text-success">{{ $group->name }} - Weekly Analytics</h3>
  <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary mb-3">← Back</a>

  <!-- Table -->
  <div class="card shadow-sm p-3 mb-4">
    <table class="table table-bordered table-hover text-center">
      <thead class="table-success">
        <tr>
        
          <th>Start Date</th>
          <th>End Date</th>
          <th>Total Expense</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($weeklyExpenses as $expense)
          <tr>
            
            <td>{{ \Carbon\Carbon::parse($expense->start_date)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($expense->end_date)->format('d M Y') }}</td>
            <td>₹{{ number_format($expense->total_weekly_expense, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted">No expenses found for this group.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Charts Section -->
  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm p-3">
        <h5 class="text-center text-success">Weekly Trend (Line Chart)</h5>
        <canvas id="lineChart" height="200"></canvas>
      </div>
    </div>

    <div class="col-md-6 mb-4">
  <div class="card shadow-sm p-3">
    <h5 class="text-center text-success">Expense Distribution (Pie Chart)</h5>
    <canvas id="pieChart" height="150" style="max-height:250px;"></canvas>
  </div>
</div>

  </div>
</div>

<script>
  // PHP data -> JS arrays
  const weekLabels = @json($weeklyExpenses->pluck('year_week'));
  const expenses = @json($weeklyExpenses->pluck('total_weekly_expense'));

  // Line Chart (Weekly Trend)
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: weekLabels.map(w => "Week " + w),
      datasets: [{
        label: 'Total Expense (₹)',
        data: expenses,
        borderColor: '#28a745',
        backgroundColor: 'rgba(40, 167, 69, 0.2)',
        fill: true,
        tension: 0.3,
        pointBackgroundColor: '#28a745',
        pointBorderColor: '#fff',
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { labels: { color: '#28a745' } } },
      scales: {
        x: { ticks: { color: '#28a745' } },
        y: { ticks: { color: '#28a745' } }
      }
    }
  });

  // Pie Chart (Distribution)
  new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
      labels: weekLabels.map(w => "Week " + w),
      datasets: [{
        label: 'Weekly Expense',
        data: expenses,
        backgroundColor: [
          'rgba(40, 167, 69, 0.9)',
          'rgba(72, 201, 176, 0.9)',
          'rgba(144, 238, 144, 0.9)',
          'rgba(0, 128, 0, 0.9)',
          'rgba(34, 139, 34, 0.9)',
          'rgba(60, 179, 113, 0.9)'
        ],
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } }
    }
  });
</script>

</body>
</html>


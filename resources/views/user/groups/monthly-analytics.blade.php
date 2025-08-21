<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $group->name }} - Monthly Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            padding: 20px;
            background-color: #f0fdf4; /* light green background */
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            color: #065f46;
            text-align: center;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .chart-container {
            height: 250px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #d1fae5;
            text-align: center;
        }
        th {
            background: #bbf7d0;
        }
    </style>
</head>
<body>

    <h1>Monthly Analytics - {{ $group->name }}</h1>
    <p class="text-center"><strong>Description:</strong> {{ $group->description }}</p>
    <p class="text-center"><strong>Budget:</strong> ₹{{ number_format($group->budget, 2) }}</p>

    <!-- Monthly Total Expenses Line Chart -->
    <div class="card">
        <h2>Monthly Total Expenses</h2>
        <div class="chart-container">
            <canvas id="lineChart"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Total Expense (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weeklyExpenses as $week)
                    <tr>
                        <td>{{ $week->label }}</td>
                        <td>₹{{ number_format($week->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Category-wise Expenses Pie Chart -->
    <div class="card">
        <h2>Category-wise Expenses</h2>
        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Expense (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryExpenses as $cat)
                    <tr>
                        <td>{{ $cat->category }}</td>
                        <td>₹{{ number_format($cat->total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>Total</td>
                    <td>₹{{ number_format($categoryExpenses->sum('total'), 2) }}</td>
                </tr>
            </tbody>
        </table>
        <p><strong>Budget Left:</strong> ₹{{ number_format(max($group->budget - $categoryExpenses->sum('total'),0),2) }}</p>
    </div>

    <script>
        // Line Chart - Monthly total
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: @json($weeklyExpenses->pluck('label')),
                datasets: [{
                    label: 'Weekly Expenses (₹)',
                    data: @json($weeklyExpenses->pluck('total')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    fill: true,
                    tension: 0,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                animation: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Pie Chart - Category wise
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($categoryExpenses->pluck('category')),
                datasets: [{
                    data: @json($categoryExpenses->pluck('total')),
                    backgroundColor: ['#34d399','#10b981','#6ee7b7','#a7f3d0','#d1fae5'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right' } },
                animation: false
            }
        });
    </script>

</body>
</html>

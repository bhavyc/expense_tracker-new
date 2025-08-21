<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $group->name }} - Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            margin: 20px;
        }
        h1, h2 {
            color: #065f46;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .charts {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .chart-container {
            flex: 1;
            min-width: 300px;
            max-width: 350px;
            height: 250px;
        }
        canvas {
            max-height: 250px !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

    <h1>Group Analytics - {{ $group->name }}</h1>
    <p><strong>Description:</strong> {{ $group->description }}</p>
    <p><strong>Budget:</strong>₹{{ number_format($group->budget, 2) }}</p>

    <div class="card">
        <h2>Weekly Expenses</h2>
        <div class="chart-container">
            <canvas id="weeklyChart"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Total Expense</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weeklyExpenses as $week)
                    <tr>
                        <td>{{ $week->date_range }}</td>
                        <td>₹{{ number_format($week->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Category-wise Expenses</h2>
        <div class="chart-container">
            <canvas id="categoryChart"></canvas>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Expense</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryExpenses as $cat)
                    <tr>
                        <td>{{ $cat->category }}</td>
                        <td>₹{{ number_format($cat->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: @json($weeklyExpenses->pluck('date_range')),
                datasets: [{
                    label: 'Weekly Expenses (₹)',
                    data: @json($weeklyExpenses->pluck('total')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointBackgroundColor: '#065f46'
                }]
            },
            options: {
                responsive: true,
                animation: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
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
            options: { responsive: true, animation: false }
        });
    </script>

</body>
</html>

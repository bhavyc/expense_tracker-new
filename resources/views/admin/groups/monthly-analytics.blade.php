<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $group->name }} - Monthly Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            font-weight: 700;
            color: #2c3e50;
        }
    </style>
</head>
<body class="p-4">

    <div class="container">
        <h2 class="mb-5 text-center">📊 {{ $group->name }} - Monthly Expense Analytics</h2>

        <div class="row g-4">
            <!-- Line Chart -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="text-center mb-3">📈 Expense Trend</h5>
                    <canvas id="lineChart" height="300"></canvas>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="text-center mb-3">🥧 Expense Distribution</h5>
                    <canvas id="pieChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels = [
            @foreach($monthlyExpenses as $exp)
                "{{ $exp->month }}-{{ $exp->year }}",
            @endforeach
        ];

        const dataValues = [
            @foreach($monthlyExpenses as $exp)
                {{ $exp->total_monthly_expense }},
            @endforeach
        ];

        // Line Chart
        const ctx1 = document.getElementById('lineChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Expenses',
                    data: dataValues,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: '#007bff',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true, position: 'bottom' } }
            }
        });

        // Pie Chart
        const ctx2 = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545',
                        '#6f42c1', '#20c997', '#fd7e14', '#17a2b8',
                        '#6610f2', '#e83e8c'
                    ],
                    borderWidth: 2,
                    hoverOffset: 10
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

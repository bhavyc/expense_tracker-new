<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 2rem;
            background: #f7f8fa;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        select {
            padding: 0.5rem;
            font-size: 1rem;
        }
        .cards {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
        }
        .card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            flex: 1;
        }
        .charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        canvas {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
        }
        @media (max-width: 768px) {
            .charts {
                grid-template-columns: 1fr;
            }
            .cards {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>User Expense Analytics</h2>
            <form method="GET">
                <select name="user_id" onchange="this.form.submit()">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedUser)
            <div class="cards">
                <div class="card">
                    <h4>Total Spent</h4>
                    <p><strong>₹{{ number_format($totalSpent) }}</strong></p>
                </div>
                <div class="card">
                    <h4>Top Category</h4>
                    <p><strong>{{ $topCategory ?? 'N/A' }}</strong></p>
                </div>
            </div>

            <div class="charts">
                <canvas id="barChart"></canvas>
                <canvas id="donutChart"></canvas>
            </div>
        @else
            <p style="margin-top:2rem;">Please select a user to view analytics.</p>
        @endif
    </div>

    @if($selectedUser)
    <script>
        const categoryLabels = {!! json_encode($categoryNames) !!};
        const categoryTotals = {!! json_encode($categoryAmounts) !!};

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Amount Spent',
                    backgroundColor: '#4CAF50',
                    data: categoryTotals,
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₹' + value
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Category Split',
                    data: categoryTotals,
                    backgroundColor: ['#4CAF50', '#FF9800', '#03A9F4', '#9C27B0', '#E91E63', '#FFC107'],
                    hoverOffset: 6
                }]
            }
        });
    </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f8fa;
            margin: 0;
            padding: 2rem;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        select {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .cards {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            flex: 1;
            min-width: 240px;
            transition: transform 0.2s ease, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: center;
        }

        .chart-container {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        canvas {
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .charts {
                grid-template-columns: 1fr;
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
        @if($user->role !== 'admin') {{--  Exclude admin based on role --}}
            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endif
    @endforeach
</select>

        </form>
    </div>

    @if($selectedUser)
        <div class="cards">
            <div class="card">
                <h4>Total Spent</h4>
                <p style="font-size: 1.5rem; color: #4CAF50;"><strong>₹{{ number_format($totalSpent) }}</strong></p>
            </div>
            <div class="card">
                <h4>Top Category</h4>
                <p style="font-size: 1.5rem; color: #FF5722;"><strong>{{ $topCategory ?? 'N/A' }}</strong></p>
            </div>
        </div>

        <div class="charts">
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-container" style="max-width: 350px; margin: auto;">
                <canvas id="donutChart" width="300" height="300"></canvas>
            </div>
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
                label: 'Amount Spent (₹)',
                data: categoryTotals,
                backgroundColor: '#2196F3',
                borderRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
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
                backgroundColor: [
                    '#4CAF50', '#FF9800', '#03A9F4',
                    '#9C27B0', '#E91E63', '#FFC107'
                ],
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            cutout: '65%'
        }
    });
</script>
@endif
</body>
</html>

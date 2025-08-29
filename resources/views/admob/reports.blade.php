<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdMob Revenue Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">AdMob Revenue Report</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Total Earnings</h6>
                    <h4></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6>Total Impressions</h6>
                    <h4> </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Total Clicks</h6>
                    <h4> </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h6>Avg CTR</h6>
                   
                    <h4> </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Daily Breakdown
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Estimated Earnings (₹)</th>
                        <th>Impressions</th>
                        <th>Clicks</th>
                        <th>CTR (%)</th>
                    </tr>
                </thead>
                <tbody>
                 
                        <tr>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td>
                                
                            </td>
                        </tr>
                
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>






<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdMob Revenue Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">📊 AdMob Revenue Report</h2>

    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Total Earnings</h6>
                    <h4>₹{{ number_format($summary['earnings'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6>Total Impressions</h6>
                    <h4>{{ number_format($summary['impressions']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Total Clicks</h6>
                    <h4>{{ number_format($summary['clicks']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h6>Avg CTR</h6>
                    @php
                        $ctr = $summary['impressions'] > 0 
                            ? ($summary['clicks'] / $summary['impressions']) * 100 
                            : 0;
                    @endphp
                    <h4>{{ number_format($ctr, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

   
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Daily Breakdown
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Estimated Earnings (₹)</th>
                        <th>Impressions</th>
                        <th>Clicks</th>
                        <th>CTR (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailyReports as $report)
                        <tr>
                            <td>{{ $report->date }}</td>
                            <td>₹{{ number_format($report->earnings, 2) }}</td>
                            <td>{{ number_format($report->impressions) }}</td>
                            <td>{{ number_format($report->clicks) }}</td>
                            <td>
                                @php
                                    $dailyCtr = $report->impressions > 0 
                                        ? ($report->clicks / $report->impressions) * 100 
                                        : 0;
                                @endphp
                                {{ number_format($dailyCtr, 2) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html> -->

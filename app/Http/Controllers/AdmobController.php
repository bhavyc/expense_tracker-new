<?php

// app/Http/Controllers/AdmobController.php
namespace App\Http\Controllers;

use Google_Client;
use Google_Service_AdMob;
use Google_Service_AdMob_GenerateNetworkReportRequest;
use Illuminate\Support\Facades\DB;

class AdmobController extends Controller
{
    public function fetchRevenue()
    {
        // 1. Google Client Auth
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/admob.json'));
        $client->addScope('https://www.googleapis.com/auth/admob.report');

        $service = new Google_Service_AdMob($client);

        // 2. Report Request (date wise)
        $request = new Google_Service_AdMob_GenerateNetworkReportRequest([
            'reportSpec' => [
                'dateRange' => [
                    'startDate' => ['year' => 2025, 'month' => 8, 'day' => 1],
                    'endDate'   => ['year' => 2025, 'month' => 8, 'day' => 28],
                ],
                'metrics' => ['IMPRESSIONS', 'ESTIMATED_EARNINGS'],
                'dimensions' => ['DATE'],
            ]
        ]);

        // ⚠️ Apna account ID yahan dalna (pub- se start hota hai)
        $account = 'accounts/pub-XXXXXXXXXXXXXX';

        // 3. Fetch Data
        $response = $service->accounts_networkReport->generate($account, $request);

        foreach ($response as $row) {
            $date = $row->row->dimensionValues['DATE']->value ?? null;
            $impressions = $row->row->metricValues['IMPRESSIONS']->integerValue ?? 0;
            $earnings = ($row->row->metricValues['ESTIMATED_EARNINGS']->microsValue ?? 0) / 1000000;

            // 4. Store or Update in DB
            DB::table('admob_reports')->updateOrInsert(
                ['date' => $date],
                [
                    'impressions' => $impressions,
                    'earnings' => $earnings,
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json(['message' => 'AdMob revenue data fetched successfully!']);
    }


//     public function reports()
// {
//     $reports = \DB::table('admob_reports')
//         ->orderBy('date', 'desc')
//         ->get();

//     return view('admob.reports', compact('reports'));
// }


public function reports()
{
    $reports = \DB::table('admob_reports')->orderBy('date', 'desc')->get();

    $summary = [
        'earnings' => $reports->sum('earnings'),
        'impressions' => $reports->sum('impressions'),
        'clicks' => $reports->sum('clicks'),
    ];

    return view('admob.reports', [
        'summary' => $summary,
        'dailyReports' => $reports,
    ]);
}

}

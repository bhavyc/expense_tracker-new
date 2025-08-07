<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use PDF;
 
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class reportController extends Controller
{
     public function index(Request $request)
    {
        $query = Expense::with('user', 'group');

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $expenses = $query->get();

        return view('reports.index', compact('expenses'));
    }

    public function exportPdf(Request $request)
    {
        $expenses = $this->filteredExpenses($request);
        $pdf = PDF::loadView('reports.pdf', compact('expenses'));
        return $pdf->download('expense_report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $expenses = $this->filteredExpenses($request);

        $filename = "expense_report.csv";
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Date', 'User', 'Group', 'Category', 'Amount']);

        foreach ($expenses as $expense) {
            fputcsv($handle, [
                $expense->created_at->format('Y-m-d'),
                $expense->user->name,
                $expense->group->name ?? 'N/A',
                $expense->category ?? 'N/A',
                $expense->amount
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return Response::make($contents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }

    private function filteredExpenses($request)
    {
        $query = Expense::with('user', 'group');

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query->get();
    }
}

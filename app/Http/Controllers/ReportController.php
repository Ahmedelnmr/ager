<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\RentSchedule;
use App\Models\Building;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use App\Exports\ContractsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $buildings = Building::all();
        $payments  = collect();
        $contracts = collect();

        if ($request->filled('type')) {
            if ($request->type === 'payments') {
                $query = Payment::with(['contract.tenant', 'contract.unit.building', 'rentSchedule'])->latest('payment_date');
                if ($request->filled('from'))        $query->where('payment_date', '>=', $request->from);
                if ($request->filled('to'))          $query->where('payment_date', '<=', $request->to);
                if ($request->filled('building_id')) $query->whereHas('contract.unit', fn($q) => $q->where('building_id', $request->building_id));
                $payments = $query->get();
            } elseif ($request->type === 'contracts') {
                $query = Contract::with(['tenant', 'unit.building'])->latest();
                if ($request->filled('status')) $query->where('status', $request->status);
                $contracts = $query->get();
            }
        }

        $summary = [
            'total_payments'   => Payment::whereMonth('payment_date', now()->month)->sum('amount'),
            'total_overdue'    => RentSchedule::where('status', 'overdue')->sum('final_amount') - RentSchedule::where('status', 'overdue')->sum('paid_amount'),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'occupancy_rate'   => \App\Models\Unit::count() > 0
                ? round(\App\Models\Unit::where('status', 'rented')->count() / \App\Models\Unit::count() * 100, 1)
                : 0,
        ];

        return view('reports.index', compact('buildings', 'payments', 'contracts', 'summary'));
    }

    public function exportPayments(Request $request)
    {
        return Excel::download(new PaymentsExport($request->all()), 'payments-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportContracts(Request $request)
    {
        return Excel::download(new ContractsExport($request->all()), 'contracts-' . now()->format('Y-m-d') . '.xlsx');
    }
}

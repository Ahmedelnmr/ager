<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Contract;
use App\Models\RentSchedule;
use App\Models\Unit;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Run overdue marking instantly on dashboard load too
        app(\App\Services\LatePenaltyService::class)->processOverdue();

        $totalBuildings   = Building::count();
        $totalUnits       = Unit::count();
        $rentedUnits      = Unit::where('status', 'rented')->count();
        $vacantUnits      = Unit::where('status', 'vacant')->count();
        $activeContracts  = Contract::where('status', 'active')->count();

        // Contracts ending soon (next 30 days)
        $endingSoon = Contract::endingSoon(30)->with(['tenant', 'unit.building'])->get();

        // Overdue schedules — unpaid balance only
        $overdueRow = RentSchedule::where('status', 'overdue')
            ->selectRaw('COUNT(*) as cnt, SUM(final_amount - paid_amount) as balance')
            ->first();
        $overdueSchedules = (int) ($overdueRow->cnt ?? 0);
        $overdueTotal     = (float) ($overdueRow->balance ?? 0);

        // This month income
        $monthlyIncome = Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        // Chart: last 6 months income
        $monthlyChart = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date   = now()->subMonths($i);
            $income = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $monthlyChart->push([
                'month'  => $date->translatedFormat('M Y'),
                'income' => $income,
            ]);
        }

        return view('dashboard.index', compact(
            'totalBuildings', 'totalUnits', 'rentedUnits', 'vacantUnits',
            'activeContracts', 'endingSoon', 'overdueSchedules', 'overdueTotal',
            'monthlyIncome', 'monthlyChart'
        ));
    }

}

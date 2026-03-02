<?php

namespace App\Http\Controllers;

use App\Models\RentSchedule;
use App\Models\Contract;
use App\Services\LatePenaltyService;
use Illuminate\Http\Request;

class RentScheduleController extends Controller
{
    public function index(Request $request)
    {
        // ── Real-time overdue marking ──────────────────────────────
        // Mark any schedule past its due_date (due or partial) as overdue instantly.
        // This runs on every page load so we don't wait for the daily scheduler.
        app(LatePenaltyService::class)->processOverdue();

        $query = RentSchedule::with(['contract.tenant', 'contract.unit.building'])->latest('due_date');
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('contract_id')) $query->where('contract_id', $request->contract_id);
        if ($request->filled('month'))       $query->where('period_label', $request->month);
        $schedules = $query->paginate(20)->appends($request->query());
        return view('rent-schedules.index', compact('schedules'));
    }

    public function show(RentSchedule $rentSchedule)
    {
        $rentSchedule->load(['contract.tenant', 'contract.unit.building', 'payments.collectedBy']);
        return view('rent-schedules.show', compact('rentSchedule'));
    }

    public function update(Request $request, RentSchedule $rentSchedule)
    {
        $validated = $request->validate([
            'discount_amount' => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $rentSchedule->update($validated);
        // Recalculate final_amount
        app(\App\Services\RentScheduleService::class)->recalculate($rentSchedule);

        return back()->with('success', 'تم تحديث الاستحقاق.');
    }
}

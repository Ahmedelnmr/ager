<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Building;
use App\Models\Unit;
use App\Models\Contract;
use App\Http\Requests\StoreMaintenanceRequest as StoreMaintenanceForm;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $buildings = Building::orderBy('name')->get();
        $units     = collect();

        $query = MaintenanceRequest::with(['unit.building', 'assignedTo'])->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('building_id')) {
            $query->whereHas('unit', fn($q) => $q->where('building_id', $request->building_id));
            $units = Unit::where('building_id', $request->building_id)->orderBy('unit_number')->get();
        }
        if ($request->filled('unit_id'))     $query->where('unit_id', $request->unit_id);

        $requests = $query->paginate(20)->appends($request->query());
        return view('maintenance.index', compact('requests', 'buildings', 'units'));
    }

    public function create()
    {
        $buildings = \App\Models\Building::orderBy('name')->get();
        $units     = Unit::with('building')->orderBy('building_id')->get();
        $users     = \App\Models\User::all();
        return view('maintenance.create', compact('units', 'users', 'buildings'));
    }

    public function store(StoreMaintenanceForm $request)
    {
        MaintenanceRequest::create($request->validated());
        return redirect()->route('maintenance.index')
            ->with('success', 'تم رفع طلب الصيانة.');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $maintenance->load(['unit.building', 'contract.tenant', 'assignedTo']);
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $units = Unit::with('building')->get();
        $users = \App\Models\User::all();
        return view('maintenance.edit', compact('maintenance', 'units', 'users'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'cost'        => 'nullable|numeric|min:0',
            'started_at'  => 'nullable|date',
            'finished_at' => 'nullable|date',
            'description' => 'required|string',
        ]);
        $maintenance->update($validated);
        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'تم تحديث الطلب.');
    }
}

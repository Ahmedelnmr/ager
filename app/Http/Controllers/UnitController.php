<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Building;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::with('building')->latest();
        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $units     = $query->paginate(20)->appends($request->query());
        $buildings = Building::all();
        return view('units.index', compact('units', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::all();
        return view('units.create', compact('buildings'));
    }

    public function store(StoreUnitRequest $request)
    {
        $unit = Unit::create($request->validated());
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'unit.created',
            'model_type' => Unit::class, 'model_id' => $unit->id,
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('units.show', $unit)
            ->with('success', 'تم إنشاء الوحدة بنجاح.');
    }

    public function show(Unit $unit)
    {
        $unit->load(['building', 'contracts.tenant', 'maintenanceRequests']);
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $buildings = Building::all();
        return view('units.edit', compact('unit', 'buildings'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());
        return redirect()->route('units.show', $unit)
            ->with('success', 'تم تحديث الوحدة بنجاح.');
    }

    public function destroy(Unit $unit)
    {
        // Guard: block if unit has contracts
        $contractsCount = $unit->contracts()->count();
        if ($contractsCount > 0) {
            return redirect()->route('units.show', $unit)
                ->with('error',
                    "لا يمكن حذف الوحدة «{$unit->unit_number}» لأن لديها {$contractsCount} عقد مرتبط بها."
                    . " يرجى إنهاء أو حذف العقود أولاً."
                );
        }

        // Guard: block if unit has open maintenance requests
        $maintenanceCount = $unit->maintenanceRequests()->whereNotIn('status', ['completed','cancelled'])->count();
        if ($maintenanceCount > 0) {
            return redirect()->route('units.show', $unit)
                ->with('error',
                    "لا يمكن حذف الوحدة لأن لديها {$maintenanceCount} طلب صيانة مفتوح."
                    . " يرجى إغلاق طلبات الصيانة أولاً."
                );
        }

        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'تم حذف الوحدة بنجاح.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount(['units', 'activeUnits', 'vacantUnits'])
            ->latest()
            ->paginate(15);
        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(StoreBuildingRequest $request)
    {
        $building = Building::create($request->validated());
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'building.created',
            'model_type' => Building::class, 'model_id' => $building->id,
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('buildings.show', $building)
            ->with('success', 'تم إنشاء المبنى بنجاح.');
    }

    public function show(Building $building)
    {
        $building->load(['units.activeContract.tenant']);
        return view('buildings.show', compact('building'));
    }

    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $building->update($request->validated());
        return redirect()->route('buildings.show', $building)
            ->with('success', 'تم تحديث المبنى بنجاح.');
    }

    public function destroy(Building $building)
    {
        // Guard: block deletion if the building has units
        $unitsCount = $building->units()->count();
        if ($unitsCount > 0) {
            return redirect()->route('buildings.show', $building)
                ->with('error',
                    "لا يمكن حذف المبنى «{$building->name}» لأنه يحتوي على {$unitsCount} وحدة."
                    . " يرجى حذف الوحدات أولاً ثم إعادة المحاولة."
                );
        }

        $building->delete();
        return redirect()->route('buildings.index')
            ->with('success', 'تم حذف المبنى بنجاح.');
    }
}

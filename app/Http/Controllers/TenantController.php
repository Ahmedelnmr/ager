<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::withCount('contracts')->latest();
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('national_id', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%");
            });
        }
        $tenants = $query->paginate(20)->appends($request->query());
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(StoreTenantRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('tenants/photos', 'public');
        }
        $tenant = Tenant::create($data);
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'tenant.created',
            'model_type' => Tenant::class, 'model_id' => $tenant->id,
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'تم إنشاء المستأجر بنجاح.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['contracts.unit.building', 'contracts.rentSchedules']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($tenant->photo_path) Storage::disk('public')->delete($tenant->photo_path);
            $data['photo_path'] = $request->file('photo')->store('tenants/photos', 'public');
        }
        $tenant->update($data);
        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'تم تحديث بيانات المستأجر بنجاح.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')
            ->with('success', 'تم حذف المستأجر.');
    }
}

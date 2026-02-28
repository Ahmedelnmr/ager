<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Unit;
use App\Models\Tenant;
use App\Services\ContractService;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function index(Request $request)
    {
        $query = Contract::with(['tenant', 'unit.building'])->latest();
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('building_id')) $query->whereHas('unit', fn($q) => $q->where('building_id', $request->building_id));
        $contracts = $query->paginate(20)->appends($request->query());
        $buildings = \App\Models\Building::all();
        return view('contracts.index', compact('contracts', 'buildings'));
    }

    public function create()
    {
        $units   = Unit::where('status', 'vacant')->with('building')->get();
        $tenants = Tenant::all();
        return view('contracts.create', compact('units', 'tenants'));
    }

    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('contract_file')) {
            $data['file_path'] = $request->file('contract_file')->store('contracts', 'public');
        }
        $settings = [];
        if ($request->filled('override_late_penalty_type')) {
            $settings['late_penalty_type']  = $request->override_late_penalty_type;
            $settings['late_penalty_value'] = $request->override_late_penalty_value;
        }
        if (!empty($settings)) $data['settings'] = $settings;

        $contract = $this->contractService->create($data);
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم إنشاء العقد وتوليد جدول الاستحقاقات.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['tenant', 'unit.building', 'rentSchedules', 'payments.collectedBy', 'additionalCharges']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $units   = Unit::where('status', 'vacant')->orWhere('id', $contract->unit_id)->with('building')->get();
        $tenants = Tenant::all();
        return view('contracts.edit', compact('contract', 'units', 'tenants'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $data = $request->validated();
        if ($request->hasFile('contract_file')) {
            if ($contract->file_path) Storage::disk('public')->delete($contract->file_path);
            $data['file_path'] = $request->file('contract_file')->store('contracts', 'public');
        }
        $this->contractService->update($contract, $data);
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم تحديث العقد بنجاح.');
    }

    public function terminate(Contract $contract)
    {
        $this->contractService->terminate($contract);
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم إنهاء العقد.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')
            ->with('success', 'تم حذف العقد.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Building;
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
        $query = Contract::with(['tenant', 'unit.building']);

        // Status filter — mark expired contracts as terminated automatically
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                // Contracts whose end_date has passed but status still 'active'
                $query->where('end_date', '<', now())->where('status', 'active');
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('building_id')) {
            $query->whereHas('unit', fn($q) => $q->where('building_id', $request->building_id));
        }

        $contracts = $query->latest()->paginate(20)->appends($request->query());
        $buildings = Building::all();
        return view('contracts.index', compact('contracts', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::with(['units' => fn($q) => $q->where('status', 'vacant')])->get();
        $units     = Unit::where('status', 'vacant')->with('building')->get();
        $tenants   = Tenant::orderBy('name')->get();
        return view('contracts.create', compact('units', 'tenants', 'buildings'));
    }

    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('contract_file')) {
            $data['file_path'] = $request->file('contract_file')->store('contracts', 'public');
        }
        // Store partial refund and any extra settings
        $settings = [];
        if ($request->deposit_policy === 'partial' && $request->filled('partial_refund_type')) {
            $settings['partial_refund_type']  = $request->partial_refund_type;
            $settings['partial_refund_value'] = (float) $request->partial_refund_value;
        }
        if (!empty($settings)) $data['settings'] = $settings;
        unset($data['partial_refund_type'], $data['partial_refund_value']);

        $contract = $this->contractService->create($data);
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم إنشاء العقد وتوليد جدول الاستحقاقات بنجاح.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['tenant', 'unit.building', 'rentSchedules', 'payments.collectedBy', 'additionalCharges']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $buildings = Building::with(['units' => fn($q) => $q->where('status', 'vacant')->orWhere('id', $contract->unit_id)])->get();
        $units     = Unit::where('status', 'vacant')->orWhere('id', $contract->unit_id)->with('building')->get();
        $tenants   = Tenant::orderBy('name')->get();
        return view('contracts.edit', compact('contract', 'units', 'tenants', 'buildings'));
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

    public function terminate(Request $request, Contract $contract)
    {
        $depositAction  = $request->input('deposit_action', 'keep');
        $depositRefund  = (float) $request->input('deposit_refund_amount', 0);
        $this->contractService->terminate($contract, $depositAction, $depositRefund);
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم إنهاء العقد ومعالجة التأمين.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')
            ->with('success', 'تم حذف العقد.');
    }
}

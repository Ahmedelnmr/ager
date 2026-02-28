<?php

namespace App\Http\Controllers;

use App\Models\RentSchedule;
use App\Models\Contract;
use App\Services\PaymentService;
use App\Services\LatePenaltyService;
use App\Http\Requests\StorePaymentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService     $paymentService,
        protected LatePenaltyService $penaltyService
    ) {}

    public function index(Request $request)
    {
        $query = \App\Models\Payment::with(['rentSchedule', 'contract.tenant', 'collectedBy'])->latest();
        if ($request->filled('contract_id')) $query->where('contract_id', $request->contract_id);
        $payments = $query->paginate(20)->appends($request->query());
        return view('payments.index', compact('payments'));
    }

    public function create(RentSchedule $schedule)
    {
        $schedule->load('contract.tenant', 'contract.unit.building');
        return view('payments.create', compact('schedule'));
    }

    public function store(StorePaymentRequest $request, RentSchedule $schedule)
    {
        $payment = $this->paymentService->record($schedule, $request->validated());
        return redirect()->route('payments.receipt', $payment)
            ->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    public function receipt(\App\Models\Payment $payment)
    {
        $payment->load(['rentSchedule', 'contract.tenant', 'contract.unit.building', 'collectedBy']);
        return view('payments.receipt', compact('payment'));
    }

    public function downloadReceipt(\App\Models\Payment $payment)
    {
        $payment->load(['rentSchedule', 'contract.tenant', 'contract.unit.building', 'collectedBy']);
        $pdf = Pdf::loadView('payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("receipt-{$payment->id}.pdf");
    }
}

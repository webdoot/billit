<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use InvalidArgumentException;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $this->authorize('payments.view');

        if ($request->ajax()) {
            $query = Payment::with(['customer', 'invoice']);
            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('invoices.show', $row->invoice_id) . '" class="btn btn-sm btn-outline-info me-1" title="View Invoice"><i class="fa-regular fa-file-lines"></i></a>';
                    if ($row->receipt) {
                        $btn .= '<a href="' . route('receipts.show', $row->receipt->id) . '" class="btn btn-sm btn-outline-success me-1" title="View Receipt"><i class="fa-solid fa-receipt"></i></a>';
                    }
                    if (auth()->user()->can('payments.delete')) {
                        $btn .= '<form action="' . route('payments.destroy', $row->id) . '" method="POST" class="d-inline delete-form">';
                        $btn .= csrf_field() . method_field('DELETE');
                        $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Void Payment"><i class="fa-regular fa-trash-can"></i></button>';
                        $btn .= '</form>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('payments.index');
    }

    /**
     * Store a newly recorded payment.
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        try {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            
            $this->paymentService->recordPayment(
                $invoice,
                (float)$data['amount'],
                $data['payment_method'],
                $data['transaction_no'],
                $data['remarks']
            );

            return redirect()->back()
                ->with('success', 'Payment of ₹' . number_format($data['amount'], 2) . ' recorded successfully and receipt generated.');
                
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the recorded payment (Void transaction).
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('payments.delete');

        return DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $amount = $payment->amount;

            // Restores invoice balance
            $newBalance = round($invoice->balance + $amount, 2);
            
            // Determine invoice status after reversal
            if ($newBalance >= $invoice->total) {
                $newStatus = 'Sent';
            } else {
                $newStatus = 'Partial';
            }

            $invoice->update([
                'balance' => $newBalance,
                'status' => $newStatus,
            ]);

            // Delete payment (cascade will delete related receipt due to foreign keys)
            $payment->delete();

            return redirect()->back()
                ->with('success', 'Payment voided successfully. Invoice balance restored.');
        });
    }
}

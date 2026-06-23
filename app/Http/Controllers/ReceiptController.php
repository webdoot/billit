<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    /**
     * Display a listing of receipts.
     */
    public function index(Request $request)
    {
        $this->authorize('payments.view');

        if ($request->ajax()) {
            // Eager load customer through payment relation
            $query = Receipt::with('payment.customer');
            
            return DataTables::of($query)
                ->addColumn('customer_name', function ($row) {
                    return $row->payment->customer->company_name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('receipts.show', $row->id) . '" class="btn btn-sm btn-outline-info me-1" title="View"><i class="fa-regular fa-eye"></i></a>';
                    $btn .= '<a href="' . route('receipts.pdf', $row->id) . '" class="btn btn-sm btn-outline-success me-1" title="Download PDF"><i class="fa-regular fa-file-pdf"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('receipts.index');
    }

    /**
     * Display the specified receipt.
     */
    public function show(Receipt $receipt): View
    {
        $this->authorize('payments.view');

        $receipt->load(['payment.customer', 'payment.invoice']);

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Generate PDF Receipt using DomPDF.
     */
    public function exportPdf(Receipt $receipt): Response
    {
        $this->authorize('payments.view');

        $receipt->load(['payment.customer', 'payment.invoice']);
        
        $pdf = Pdf::loadView('receipts.pdf', compact('receipt'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($receipt->receipt_no . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\BillingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    protected InvoiceRepositoryInterface $invoiceRepo;
    protected BillingService $billingService;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepo,
        BillingService $billingService
    ) {
        $this->invoiceRepo = $invoiceRepo;
        $this->billingService = $billingService;
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $this->authorize('invoices.view');

        if ($request->ajax()) {
            $query = Invoice::with('customer');
            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('invoices.show', $row->id) . '" class="btn btn-sm btn-outline-info me-1" title="View"><i class="fa-regular fa-eye"></i></a>';
                    $btn .= '<a href="' . route('invoices.pdf', $row->id) . '" class="btn btn-sm btn-outline-success me-1" title="Download PDF"><i class="fa-regular fa-file-pdf"></i></a>';
                    if (auth()->user()->can('invoices.delete')) {
                        $btn .= '<form action="' . route('invoices.destroy', $row->id) . '" method="POST" class="d-inline delete-form">';
                        $btn .= csrf_field() . method_field('DELETE');
                        $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="fa-regular fa-trash-can"></i></button>';
                        $btn .= '</form>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('invoices.index');
    }

    /**
     * Show the form for generating a new invoice.
     */
    public function create(Request $request): View
    {
        $this->authorize('invoices.create');

        $selectedCustomerId = $request->query('customer_id');
        $customers = Customer::where('status', 'Active')->get();
        
        $customerServices = [];
        if ($selectedCustomerId) {
            $customerServices = CustomerService::where('customer_id', $selectedCustomerId)
                ->where('status', 'Active')
                ->get();
        }

        return view('invoices.create', compact('customers', 'selectedCustomerId', 'customerServices'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoiceDate = Carbon::parse($data['invoice_date']);
            $invoiceNo = $this->billingService->generateInvoiceNumber($invoiceDate);

            // Compute subtotal from items
            $subtotal = 0.00;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $itemAmount = round($item['qty'] * $item['rate'], 2);
                $subtotal += $itemAmount;
                
                $itemsData[] = [
                    'customer_service_id' => $item['customer_service_id'] ?: null,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'amount' => $itemAmount,
                ];
            }

            $discount = round($data['discount'], 2);
            $taxRate = $data['tax_rate'];
            $taxableAmount = max(0.00, $subtotal - $discount);
            $tax = round($taxableAmount * ($taxRate / 100), 2);
            $total = round($taxableAmount + $tax, 2);

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $data['customer_id'],
                'invoice_date' => $invoiceDate,
                'due_date' => Carbon::parse($data['due_date']),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'balance' => $total,
                'status' => $data['status'] ?? 'Sent',
                'notes' => $data['notes'],
                'created_by' => auth()->id() ?? 1,
            ]);

            // Create Invoice Items
            foreach ($itemsData as $itemData) {
                $itemData['invoice_id'] = $invoice->id;
                InvoiceItem::create($itemData);
            }

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice ' . $invoiceNo . ' generated successfully.');
        });
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('invoices.view');

        $invoice->load(['customer', 'invoiceItems.customerService', 'payments.receipt', 'creator']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('invoices.delete');

        if ($invoice->payments()->exists()) {
            return redirect()->route('invoices.index')
                ->with('error', 'Cannot delete invoice. Payment collections are already recorded against it.');
        }

        $this->invoiceRepo->delete($invoice->id);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * AJAX endpoint to fetch customer active services.
     */
    public function getCustomerServices(int $customerId): JsonResponse
    {
        $services = CustomerService::where('customer_id', $customerId)
            ->where('status', 'Active')
            ->get(['id', 'service_name', 'amount', 'billing_cycle']);

        return response()->json($services);
    }

    /**
     * Generate PDF Invoice using DomPDF.
     */
    public function exportPdf(Invoice $invoice): Response
    {
        $this->authorize('invoices.view');

        $invoice->load(['customer', 'invoiceItems']);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($invoice->invoice_no . '.pdf');
    }
}

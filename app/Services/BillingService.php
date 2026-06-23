<?php

namespace App\Services;

use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    protected InvoiceRepositoryInterface $invoiceRepo;

    public function __construct(InvoiceRepositoryInterface $invoiceRepo)
    {
        $this->invoiceRepo = $invoiceRepo;
    }

    /**
     * Compute the fiscal year prefix for a given date.
     * E.g., June 2026 -> "2627", Feb 2027 -> "2627"
     */
    public function getFiscalYearPrefix(Carbon $date): string
    {
        $year = $date->year;
        if ($date->month >= 4) {
            $startYear = $year;
            $endYear = $year + 1;
        } else {
            $startYear = $year - 1;
            $endYear = $year;
        }

        return substr($startYear, -2) . substr($endYear, -2);
    }

    /**
     * Generate a unique invoice number in the format INV-YYZZ-XXXXXX.
     */
    public function generateInvoiceNumber(Carbon $date): string
    {
        $prefix = 'INV-' . $this->getFiscalYearPrefix($date) . '-';

        return DB::transaction(function () use ($prefix) {
            $latest = Invoice::where('invoice_no', 'like', $prefix . '%')
                ->orderBy('invoice_no', 'desc')
                ->lockForUpdate()
                ->first();

            if (!$latest) {
                return $prefix . '000001';
            }

            $parts = explode('-', $latest->invoice_no);
            $num = (int) end($parts);
            return $prefix . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Create an invoice from a CustomerService.
     */
    public function createInvoiceFromService(CustomerService $service, float $discount = 0.00, float $taxRate = 18.00): Invoice
    {
        return DB::transaction(function () use ($service, $discount, $taxRate) {
            $invoiceDate = Carbon::now();
            $dueDate = Carbon::now()->addDays(15); // default 15 days due date
            $invoiceNo = $this->generateInvoiceNumber($invoiceDate);

            $subtotal = $service->amount;
            $tax = round(($subtotal - $discount) * ($taxRate / 100), 2);
            $total = $subtotal - $discount + $tax;

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $service->customer_id,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'balance' => $total,
                'status' => 'Sent', // Mark as Sent upon creation
                'created_by' => auth()->id() ?? 1,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'customer_service_id' => $service->id,
                'description' => "Renewal Billing for " . $service->service_name . " (" . $service->start_date->format('Y-m-d') . " to " . $service->expiry_date->format('Y-m-d') . ")",
                'qty' => 1,
                'rate' => $subtotal,
                'amount' => $subtotal,
            ]);

            return $invoice;
        });
    }
}

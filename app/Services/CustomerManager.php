<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerManager
{
    protected CustomerRepositoryInterface $customerRepo;

    public function __construct(CustomerRepositoryInterface $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    /**
     * Generate a unique customer code.
     */
    public function generateCustomerCode(): string
    {
        $latest = Customer::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'CUST-00001';
        }

        $num = (int) str_replace('CUST-', '', $latest->customer_code);
        return 'CUST-' . str_pad($num + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the ledger for a specific customer.
     */
    public function getCustomerLedger(int $customerId): Collection
    {
        $customer = Customer::with(['invoices', 'payments'])->findOrFail($customerId);

        $ledger = collect();

        // Add Invoices
        foreach ($customer->invoices as $invoice) {
            if ($invoice->status === 'Cancelled') {
                continue;
            }
            $ledger->push([
                'date' => $invoice->invoice_date,
                'type' => 'Invoice',
                'reference' => $invoice->invoice_no,
                'debit' => $invoice->total,
                'credit' => 0.00,
                'description' => 'Invoice Generated' . ($invoice->notes ? ' - ' . $invoice->notes : ''),
            ]);
        }

        // Add Payments
        foreach ($customer->payments as $payment) {
            $ledger->push([
                'date' => $payment->payment_date,
                'type' => 'Payment',
                'reference' => $payment->transaction_no ?? 'PAY-' . $payment->id,
                'debit' => 0.00,
                'credit' => $payment->amount,
                'description' => 'Payment Received via ' . $payment->payment_method . ($payment->remarks ? ' - ' . $payment->remarks : ''),
            ]);
        }

        // Sort by date
        $sorted = $ledger->sortBy('date')->values();

        // Calculate running balance
        $runningBalance = 0.00;
        return $sorted->map(function ($item) use (&$runningBalance) {
            $runningBalance += ($item['debit'] - $item['credit']);
            $item['balance'] = $runningBalance;
            return $item;
        });
    }
}

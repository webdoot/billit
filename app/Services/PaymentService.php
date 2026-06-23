<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Record a payment against an invoice.
     */
    public function recordPayment(Invoice $invoice, float $amount, string $method, ?string $transactionNo = null, ?string $remarks = null): Payment
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Payment amount must be greater than zero.");
        }

        // Use round to avoid tiny floating-point precision issues
        $amount = round($amount, 2);
        $invoiceBalance = round($invoice->balance, 2);

        if ($amount > $invoiceBalance) {
            throw new InvalidArgumentException("Payment amount of {$amount} exceeds the outstanding invoice balance of {$invoiceBalance}.");
        }

        return DB::transaction(function () use ($invoice, $amount, $method, $transactionNo, $remarks) {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_no' => $transactionNo,
                'payment_date' => Carbon::now(),
                'remarks' => $remarks,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Update invoice outstanding balance
            $newBalance = max(0.00, round($invoice->balance - $amount, 2));
            $newStatus = $newBalance <= 0.00 ? 'Paid' : 'Partial';

            $invoice->update([
                'balance' => $newBalance,
                'status' => $newStatus,
            ]);

            // Generate receipt automatically
            $this->receiptService->createReceiptForPayment($payment);

            // Update related renewals if paid
            if ($newStatus === 'Paid') {
                Renewal::where('invoice_id', $invoice->id)->update([
                    'status' => 'Paid',
                ]);
            }

            return $payment;
        });
    }
}

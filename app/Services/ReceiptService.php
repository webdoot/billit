<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    /**
     * Compute the fiscal year prefix for a given date.
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
     * Generate a unique receipt number in the format REC-YYZZ-XXXXXX.
     */
    public function generateReceiptNumber(Carbon $date): string
    {
        $prefix = 'REC-' . $this->getFiscalYearPrefix($date) . '-';

        return DB::transaction(function () use ($prefix) {
            $latest = Receipt::where('receipt_no', 'like', $prefix . '%')
                ->orderBy('receipt_no', 'desc')
                ->lockForUpdate()
                ->first();

            if (!$latest) {
                return $prefix . '000001';
            }

            $parts = explode('-', $latest->receipt_no);
            $num = (int) end($parts);
            return $prefix . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Create a receipt for a Payment.
     */
    public function createReceiptForPayment(Payment $payment): Receipt
    {
        return DB::transaction(function () use ($payment) {
            $receiptDate = Carbon::now();
            $receiptNo = $this->generateReceiptNumber($receiptDate);

            return Receipt::create([
                'receipt_no' => $receiptNo,
                'payment_id' => $payment->id,
                'receipt_date' => $receiptDate,
                'amount' => $payment->amount,
            ]);
        });
    }
}

<?php

namespace App\Services;

use App\Models\CustomerService;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RenewalService
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Renew a customer service.
     */
    public function renewService(CustomerService $service, Carbon $newExpiryDate, float $amount, bool $generateInvoice = false): Renewal
    {
        return DB::transaction(function () use ($service, $newExpiryDate, $amount, $generateInvoice) {
            $oldExpiry = $service->expiry_date;
            
            // New start date is the day after the old expiry
            $newStartDate = $oldExpiry->copy()->addDay();

            // Create renewal log
            $renewal = Renewal::create([
                'customer_service_id' => $service->id,
                'renewal_date' => Carbon::now(),
                'old_expiry' => $oldExpiry,
                'new_expiry' => $newExpiryDate,
                'amount' => $amount,
                'status' => 'Pending',
                'created_by' => auth()->id() ?? 1,
            ]);

            // Update customer service record
            $service->update([
                'start_date' => $newStartDate,
                'expiry_date' => $newExpiryDate,
                'amount' => $amount,
                'status' => 'Active', // Make active on renewal
            ]);

            // If it's a domain/hosting, we should update their child tables too
            if ($service->domain) {
                $service->domain->update([
                    'expiry_date' => $newExpiryDate,
                    'status' => 'Active',
                ]);
            }

            if ($service->hosting) {
                $service->hosting->update([
                    'status' => 'Active',
                ]);
            }

            // Generate invoice if requested
            if ($generateInvoice) {
                $invoice = $this->billingService->createInvoiceFromService($service);
                $renewal->update([
                    'invoice_id' => $invoice->id,
                    'status' => 'Generated',
                ]);
            }

            return $renewal;
        });
    }
}

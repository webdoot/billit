<?php

namespace App\Services;

use App\Models\CustomerService;
use App\Models\ExpiryAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlertService
{
    /**
     * Run the daily check:
     * - Auto-expire services that are past their expiry date.
     * - Generate alerts for 60, 30, 15, 7, and 1 day(s) before expiry.
     */
    public function runDailyCheck(): array
    {
        $today = Carbon::today();
        $expiredCount = 0;
        $alertsCreated = 0;

        DB::transaction(function () use ($today, &$expiredCount, &$alertsCreated) {
            // 1. Mark expired services automatically
            $expiredServices = CustomerService::where('status', 'Active')
                ->where('expiry_date', '<', $today)
                ->get();

            foreach ($expiredServices as $service) {
                $service->update(['status' => 'Expired']);
                
                // Update related domain/hosting status to Expired/Suspended
                if ($service->domain) {
                    $service->domain->update(['status' => 'Expired']);
                }
                if ($service->hosting) {
                    $service->hosting->update(['status' => 'Suspended']);
                }
                
                $expiredCount++;
            }

            // 2. Generate alerts for active services
            $alertDays = [60, 30, 15, 7, 1];
            
            // Fetch all active services
            $activeServices = CustomerService::where('status', 'Active')->get();

            foreach ($activeServices as $service) {
                $expiry = Carbon::parse($service->expiry_date);
                $daysRemaining = $today->diffInDays($expiry, false);

                if (in_array($daysRemaining, $alertDays)) {
                    // Check if an alert was already generated for this interval
                    $exists = ExpiryAlert::where('customer_service_id', $service->id)
                        ->where('days_before', $daysRemaining)
                        ->exists();

                    if (!$exists) {
                        ExpiryAlert::create([
                            'customer_service_id' => $service->id,
                            'days_before' => $daysRemaining,
                            'alert_date' => $today,
                            'is_read' => false,
                        ]);
                        $alertsCreated++;
                    }
                }
            }
        });

        return [
            'expired_services' => $expiredCount,
            'alerts_created' => $alertsCreated,
        ];
    }
}

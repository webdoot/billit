<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $thirtyDaysFromNow = Carbon::today()->addDays(30);

        // 1. Statistics Cards
        $stats = [
            'total_customers' => Customer::where('status', 'Active')->count(),
            'active_services' => CustomerService::where('status', 'Active')->count(),
            'expiring_services' => CustomerService::where('status', 'Active')
                ->whereBetween('expiry_date', [$today, $thirtyDaysFromNow])
                ->count(),
            'expired_services' => CustomerService::where('status', 'Expired')->count(),
            'pending_invoices' => Invoice::whereIn('status', ['Sent', 'Partial', 'Overdue'])->count(),
            'collection_this_month' => Payment::whereMonth('payment_date', $today->month)
                ->whereYear('payment_date', $today->year)
                ->sum('amount'),
            'total_outstanding' => Invoice::whereIn('status', ['Sent', 'Partial', 'Overdue'])->sum('balance'),
        ];

        // 2. Renewal Summary
        $renewals = [
            'domains_expiring' => Domain::whereBetween('expiry_date', [$today, $thirtyDaysFromNow])
                ->with('customerService.customer')
                ->get(),
            'hostings_expiring' => Hosting::whereHas('customerService', function ($query) use ($today, $thirtyDaysFromNow) {
                    $query->whereBetween('expiry_date', [$today, $thirtyDaysFromNow])
                          ->where('status', 'Active');
                })
                ->with(['customerService.customer', 'server'])
                ->get(),
            'maintenance_expiring' => CustomerService::whereHas('product.category', function ($query) {
                    $query->where('name', 'Maintenance');
                })
                ->where('status', 'Active')
                ->whereBetween('expiry_date', [$today, $thirtyDaysFromNow])
                ->with('customer')
                ->get(),
        ];

        // 3. Recent Payments (Last 10)
        $recentPayments = Payment::orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->with(['customer', 'invoice'])
            ->get();

        // 4. Upcoming Renewals (Next 20 services due for renewal)
        $upcomingRenewals = CustomerService::where('status', 'Active')
            ->where('expiry_date', '>=', $today)
            ->orderBy('expiry_date', 'asc')
            ->take(20)
            ->with(['customer', 'product'])
            ->get();

        return view('dashboard.index', compact('stats', 'renewals', 'recentPayments', 'upcomingRenewals'));
    }
}

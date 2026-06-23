<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard with tabs.
     */
    public function index(Request $request): View
    {
        $this->authorize('reports.view');

        $today = Carbon::today();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today()->endOfMonth();

        // 1. Outstanding Report (Pending / Partial / Overdue Invoices)
        $outstandingInvoices = Invoice::where('balance', '>', 0.00)
            ->whereIn('status', ['Sent', 'Partial', 'Overdue'])
            ->with('customer')
            ->orderBy('due_date', 'asc')
            ->get();

        // 2. Collection Report (Filtered by Date Range)
        $collections = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with(['customer', 'invoice'])
            ->orderBy('payment_date', 'desc')
            ->get();

        // Aggregated collection calculations
        $collectionSummary = [
            'total' => $collections->sum('amount'),
            'by_method' => $collections->groupBy('payment_method')->map(fn($item) => $item->sum('amount')),
            'daily' => $collections->groupBy(fn($c) => $c->payment_date->format('Y-m-d'))->map(fn($item) => $item->sum('amount')),
        ];

        // 3. Renewal Report (Expiring in next 90 days)
        $ninetyDaysFromNow = $today->copy()->addDays(90);
        
        $expiringDomains = Domain::whereBetween('expiry_date', [$today, $ninetyDaysFromNow])
            ->with('customerService.customer')
            ->orderBy('expiry_date', 'asc')
            ->get();

        $expiringHostings = Hosting::whereHas('customerService', function ($query) use ($today, $ninetyDaysFromNow) {
                $query->whereBetween('expiry_date', [$today, $ninetyDaysFromNow])
                      ->where('status', 'Active');
            })
            ->with(['customerService.customer', 'server'])
            ->get();

        $expiringMaintenance = CustomerService::whereHas('product.category', function ($query) {
                $query->where('name', 'Maintenance');
            })
            ->where('status', 'Active')
            ->whereBetween('expiry_date', [$today, $ninetyDaysFromNow])
            ->with('customer')
            ->get();

        // 4. Service Report
        $services = CustomerService::with(['customer', 'product.category'])->get();
        $servicesGrouped = $services->groupBy('status');

        return view('reports.index', compact(
            'outstandingInvoices',
            'collections',
            'collectionSummary',
            'startDate',
            'endDate',
            'expiringDomains',
            'expiringHostings',
            'expiringMaintenance',
            'servicesGrouped'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerServiceRequest;
use App\Http\Requests\UpdateCustomerServiceRequest;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServiceProduct;
use App\Repositories\Contracts\CustomerServiceRepositoryInterface;
use App\Services\BillingService;
use App\Services\RenewalService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    protected CustomerServiceRepositoryInterface $serviceRepo;
    protected RenewalService $renewalService;
    protected BillingService $billingService;

    public function __construct(
        CustomerServiceRepositoryInterface $serviceRepo,
        RenewalService $renewalService,
        BillingService $billingService
    ) {
        $this->serviceRepo = $serviceRepo;
        $this->renewalService = $renewalService;
        $this->billingService = $billingService;
    }

    /**
     * Display a listing of customer services.
     */
    public function index(): View
    {
        $this->authorize('services.view');

        $services = CustomerService::with(['customer', 'product.category'])->get();

        return view('customer-services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service agreement.
     */
    public function create(Request $request): View
    {
        $this->authorize('services.create');

        $selectedCustomerId = $request->query('customer_id');
        $customers = Customer::where('status', 'Active')->get();
        $products = ServiceProduct::where('status', 'Active')->with('category')->get();

        return view('customer-services.create', compact('customers', 'products', 'selectedCustomerId'));
    }

    /**
     * Store a newly created service agreement.
     */
    public function store(StoreCustomerServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id() ?? 1;

        $service = $this->serviceRepo->create($data);

        // Fetch product to see its category
        $product = ServiceProduct::with('category')->findOrFail($data['service_product_id']);
        $categoryName = $product->category->name ?? '';

        // Intelligently redirect based on category
        if ($categoryName === 'Domain') {
            return redirect()->route('domains.create', ['customer_service_id' => $service->id])
                ->with('success', 'Service created. Please configure Domain registry parameters below.');
        }

        if ($categoryName === 'Hosting') {
            return redirect()->route('hostings.create', ['customer_service_id' => $service->id])
                ->with('success', 'Service created. Please configure Hosting details below.');
        }

        return redirect()->route('customer-services.show', $service->id)
            ->with('success', 'Customer service assigned successfully.');
    }

    /**
     * Display the specified service agreement.
     */
    public function show(CustomerService $customerService): View
    {
        $this->authorize('services.view');

        $customerService->load(['customer', 'product.category', 'domain', 'hosting.server', 'renewals.creator', 'renewals.invoice']);

        return view('customer-services.show', compact('customerService'));
    }

    /**
     * Show the form for editing the specified service agreement.
     */
    public function edit(CustomerService $customerService): View
    {
        $this->authorize('services.edit');

        $customers = Customer::where('status', 'Active')->get();
        $products = ServiceProduct::where('status', 'Active')->with('category')->get();

        return view('customer-services.edit', compact('customerService', 'customers', 'products'));
    }

    /**
     * Update the specified service agreement.
     */
    public function update(UpdateCustomerServiceRequest $request, CustomerService $customerService): RedirectResponse
    {
        $this->serviceRepo->update($customerService->id, $request->validated());

        return redirect()->route('customer-services.show', $customerService->id)
            ->with('success', 'Customer service updated successfully.');
    }

    /**
     * Remove the specified service agreement.
     */
    public function destroy(CustomerService $customerService): RedirectResponse
    {
        $this->authorize('services.delete');

        $this->serviceRepo->delete($customerService->id);

        return redirect()->route('customer-services.index')
            ->with('success', 'Service agreement deleted successfully.');
    }

    /**
     * Show form to renew the service.
     */
    public function showRenewForm(CustomerService $customerService): View
    {
        $this->authorize('services.edit');

        $customerService->load('customer');

        // Estimate new expiry based on cycle
        $newExpiry = $customerService->expiry_date->copy();
        switch ($customerService->billing_cycle) {
            case 'Monthly':
                $newExpiry->addMonth();
                break;
            case 'Quarterly':
                $newExpiry->addMonths(3);
                break;
            case 'Half Yearly':
                $newExpiry->addMonths(6);
                break;
            case 'Yearly':
                $newExpiry->addYear();
                break;
        }

        return view('customer-services.renew', compact('customerService', 'newExpiry'));
    }

    /**
     * Execute renewal logic.
     */
    public function renew(Request $request, CustomerService $customerService): RedirectResponse
    {
        $this->authorize('services.edit');

        $request->validate([
            'expiry_date' => ['required', 'date', 'after:' . $customerService->expiry_date->format('Y-m-d')],
            'amount' => ['required', 'numeric', 'min:0'],
            'generate_invoice' => ['nullable', 'boolean'],
        ]);

        $newExpiry = Carbon::parse($request->expiry_date);
        $amount = $request->amount;
        $generateInvoice = $request->has('generate_invoice') && $request->generate_invoice == 1;

        $this->renewalService->renewService($customerService, $newExpiry, $amount, $generateInvoice);

        return redirect()->route('customer-services.show', $customerService->id)
            ->with('success', 'Service renewed successfully.');
    }

    /**
     * Manually generate billing invoice for this service.
     */
    public function generateInvoice(CustomerService $customerService): RedirectResponse
    {
        $this->authorize('invoices.create');

        $invoice = $this->billingService->createInvoiceFromService($customerService);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Billing invoice ' . $invoice->invoice_no . ' generated successfully.');
    }
}

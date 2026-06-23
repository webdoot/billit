<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends Controller
{
    protected CustomerRepositoryInterface $customerRepo;
    protected CustomerManager $customerManager;

    public function __construct(
        CustomerRepositoryInterface $customerRepo,
        CustomerManager $customerManager
    ) {
        $this->customerRepo = $customerRepo;
        $this->customerManager = $customerManager;
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $this->authorize('customers.view');

        if ($request->ajax()) {
            $query = Customer::query();
            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('customers.show', $row->id) . '" class="btn btn-sm btn-outline-info me-1" title="View Profile"><i class="fa-regular fa-eye"></i></a>';
                    $btn .= '<a href="' . route('customers.ledger', $row->id) . '" class="btn btn-sm btn-outline-secondary me-1" title="Ledger"><i class="fa-solid fa-list-check"></i></a>';
                    if (auth()->user()->can('customers.edit')) {
                        $btn .= '<a href="' . route('customers.edit', $row->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fa-regular fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('customers.delete')) {
                        $btn .= '<form action="' . route('customers.destroy', $row->id) . '" method="POST" class="d-inline delete-form">';
                        $btn .= csrf_field() . method_field('DELETE');
                        $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="fa-regular fa-trash-can"></i></button>';
                        $btn .= '</form>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customers.index');
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $this->authorize('customers.create');
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['customer_code'] = $this->customerManager->generateCustomerCode();

        $this->customerRepo->create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully with code: ' . $data['customer_code']);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $this->authorize('customers.view');
        
        $customer->load(['customerServices.product', 'invoices', 'payments']);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $this->authorize('customers.edit');
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerRepo->update($customer->id, $request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Customer profile updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('customers.delete');
        $this->customerRepo->delete($customer->id);

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * View customer ledger.
     */
    public function ledger(Customer $customer): View
    {
        $this->authorize('customers.view');
        $ledger = $this->customerManager->getCustomerLedger($customer->id);

        return view('customers.ledger', compact('customer', 'ledger'));
    }

    /**
     * Export customer list to Excel.
     */
    public function export(): BinaryFileResponse
    {
        $this->authorize('customers.view');
        return Excel::download(new CustomersExport, 'customers_' . date('Y-m-d') . '.xlsx');
    }
}

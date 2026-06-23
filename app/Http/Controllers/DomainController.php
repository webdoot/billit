<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\CustomerService;
use App\Models\Domain;
use App\Repositories\Contracts\DomainRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DomainController extends Controller
{
    protected DomainRepositoryInterface $domainRepo;

    public function __construct(DomainRepositoryInterface $domainRepo)
    {
        $this->domainRepo = $domainRepo;
    }

    /**
     * Display a listing of domains.
     */
    public function index(): View
    {
        $this->authorize('services.view');
        
        $domains = Domain::with('customerService.customer')->get();

        return view('domains.index', compact('domains'));
    }

    /**
     * Show the form for creating a new domain.
     */
    public function create(): View
    {
        $this->authorize('services.create');

        // Fetch customer services of category Domain that do not have domain registry details yet
        $services = CustomerService::whereHas('product.category', function ($query) {
                $query->where('name', 'Domain');
            })
            ->whereDoesntHave('domain')
            ->where('status', 'Active')
            ->with('customer')
            ->get();

        return view('domains.create', compact('services'));
    }

    /**
     * Store a newly created domain.
     */
    public function store(StoreDomainRequest $request): RedirectResponse
    {
        $this->domainRepo->create($request->validated());

        return redirect()->route('domains.index')
            ->with('success', 'Domain registration details recorded.');
    }

    /**
     * Show the form for editing the specified domain.
     */
    public function edit(Domain $domain): View
    {
        $this->authorize('services.edit');
        
        $domain->load('customerService.customer');

        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified domain.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $this->domainRepo->update($domain->id, $request->validated());

        return redirect()->route('domains.index')
            ->with('success', 'Domain registry specifications updated.');
    }

    /**
     * Remove the specified domain.
     */
    public function destroy(Domain $domain): RedirectResponse
    {
        $this->authorize('services.delete');
        $this->domainRepo->delete($domain->id);

        return redirect()->route('domains.index')
            ->with('success', 'Domain record deleted.');
    }
}

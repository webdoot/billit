<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHostingRequest;
use App\Http\Requests\UpdateHostingRequest;
use App\Models\CustomerService;
use App\Models\Hosting;
use App\Models\Server;
use App\Repositories\Contracts\HostingRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HostingController extends Controller
{
    protected HostingRepositoryInterface $hostingRepo;

    public function __construct(HostingRepositoryInterface $hostingRepo)
    {
        $this->hostingRepo = $hostingRepo;
    }

    /**
     * Display a listing of hostings.
     */
    public function index(): View
    {
        $this->authorize('services.view');
        
        $hostings = Hosting::with(['customerService.customer', 'server'])->get();

        return view('hostings.index', compact('hostings'));
    }

    /**
     * Show the form for creating a new hosting.
     */
    public function create(): View
    {
        $this->authorize('services.create');
        
        // Get customer services of category Hosting that do not have a hosting details record yet
        $services = CustomerService::whereHas('product.category', function ($query) {
                $query->where('name', 'Hosting');
            })
            ->whereDoesntHave('hosting')
            ->where('status', 'Active')
            ->with('customer')
            ->get();

        $servers = Server::where('status', 'Active')->get();

        return view('hostings.create', compact('services', 'servers'));
    }

    /**
     * Store a newly created hosting.
     */
    public function store(StoreHostingRequest $request): RedirectResponse
    {
        $this->hostingRepo->create($request->validated());

        return redirect()->route('hostings.index')
            ->with('success', 'Hosting details recorded successfully.');
    }

    /**
     * Show the form for editing the specified hosting.
     */
    public function edit(Hosting $hosting): View
    {
        $this->authorize('services.edit');
        
        $servers = Server::where('status', 'Active')->get();
        $hosting->load('customerService.customer');

        return view('hostings.edit', compact('hosting', 'servers'));
    }

    /**
     * Update the specified hosting.
     */
    public function update(UpdateHostingRequest $request, Hosting $hosting): RedirectResponse
    {
        $this->hostingRepo->update($hosting->id, $request->validated());

        return redirect()->route('hostings.index')
            ->with('success', 'Hosting specifications updated successfully.');
    }

    /**
     * Remove the specified hosting.
     */
    public function destroy(Hosting $hosting): RedirectResponse
    {
        $this->authorize('services.delete');
        $this->hostingRepo->delete($hosting->id);

        return redirect()->route('hostings.index')
            ->with('success', 'Hosting account details deleted.');
    }
}

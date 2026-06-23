<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Models\Server;
use App\Repositories\Contracts\ServerRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServerController extends Controller
{
    protected ServerRepositoryInterface $serverRepo;

    public function __construct(ServerRepositoryInterface $serverRepo)
    {
        $this->serverRepo = $serverRepo;
    }

    /**
     * Display a listing of servers.
     */
    public function index(): View
    {
        $this->authorize('servers.view');
        
        $servers = $this->serverRepo->all();

        return view('servers.index', compact('servers'));
    }

    /**
     * Show the form for creating a new server.
     */
    public function create(): View
    {
        $this->authorize('servers.create');
        return view('servers.create');
    }

    /**
     * Store a newly created server.
     */
    public function store(StoreServerRequest $request): RedirectResponse
    {
        $this->serverRepo->create($request->validated());

        return redirect()->route('servers.index')
            ->with('success', 'Server added successfully to inventory.');
    }

    /**
     * Show the form for editing the specified server.
     */
    public function edit(Server $server): View
    {
        $this->authorize('servers.edit');
        return view('servers.edit', compact('server'));
    }

    /**
     * Update the specified server.
     */
    public function update(UpdateServerRequest $request, Server $server): RedirectResponse
    {
        $this->serverRepo->update($server->id, $request->validated());

        return redirect()->route('servers.index')
            ->with('success', 'Server specifications updated successfully.');
    }

    /**
     * Remove the specified server.
     */
    public function destroy(Server $server): RedirectResponse
    {
        $this->authorize('servers.delete');

        // Check if hosting accounts are linked to this server
        if ($server->hostings()->exists()) {
            return redirect()->route('servers.index')
                ->with('error', 'Cannot delete server. There are active hosting accounts hosted on this server.');
        }

        $this->serverRepo->delete($server->id);

        return redirect()->route('servers.index')
            ->with('success', 'Server removed successfully from inventory.');
    }
}

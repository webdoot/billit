@extends('layouts.app')

@section('title', 'Service Agreement Details')
@section('page_title', 'Customer Service Agreement')

@section('content')
<div class="row">
    <!-- Main Service Specs Panel -->
    <div class="col-lg-8 mb-4">
        <div class="card card-custom mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-cube text-primary me-2"></i>Agreement Specifications</h5>
                <div>
                    @can('services.edit')
                    <a href="{{ route('customer-services.edit', $customerService->id) }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fa-regular fa-edit me-1"></i> Edit Specifications
                    </a>
                    @endcan
                    @if(in_array($customerService->status, ['Active', 'Expired']))
                    <a href="{{ route('customer-services.renew', $customerService->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Renew Service
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body px-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-secondary small d-block mb-1">Customer / Client</span>
                        <h6 class="fw-bold"><a href="{{ route('customers.show', $customerService->customer_id) }}" class="text-decoration-none">{{ $customerService->customer->company_name }}</a></h6>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary small d-block mb-1">Service / Package name</span>
                        <h6 class="fw-bold text-dark">{{ $customerService->service_name }}</h6>
                    </div>
                    
                    <hr class="text-light-hover my-2">

                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Billing Cycle</span>
                        <span class="badge bg-light text-dark border">{{ $customerService->billing_cycle }}</span>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Agreed Price</span>
                        <h6 class="fw-bold">₹{{ number_format($customerService->amount, 2) }}</h6>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Start Date</span>
                        <h6>{{ $customerService->start_date->format('Y-m-d') }}</h6>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Expiry Date</span>
                        @php
                            $isExpired = $customerService->expiry_date->isPast();
                            $daysLeft = Carbon\Carbon::today()->diffInDays($customerService->expiry_date, false);
                        @endphp
                        <h6 class="fw-bold {{ $isExpired ? 'text-danger' : ($daysLeft <= 30 ? 'text-warning' : 'text-success') }}">
                            {{ $customerService->expiry_date->format('Y-m-d') }}
                            @if(!$isExpired && $daysLeft <= 30)
                                <span class="small d-block text-secondary">({{ $daysLeft }} days remaining)</span>
                            @endif
                        </h6>
                    </div>

                    <hr class="text-light-hover my-2">

                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Auto Renew</span>
                        <span class="badge {{ $customerService->auto_renew ? 'bg-success' : 'bg-light text-dark border' }}">
                            {{ $customerService->auto_renew ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-secondary small d-block mb-1">Agreement Status</span>
                        @php
                            $statusBadge = 'bg-secondary';
                            if ($customerService->status === 'Active') $statusBadge = 'bg-success';
                            elseif ($customerService->status === 'Expired') $statusBadge = 'bg-danger';
                            elseif ($customerService->status === 'Suspended') $statusBadge = 'bg-warning text-dark';
                        @endphp
                        <span class="badge {{ $statusBadge }}">{{ $customerService->status }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary small d-block mb-1">Created By</span>
                        <span class="text-dark small"><i class="fa-regular fa-user me-1"></i>{{ $customerService->creator->name ?? 'System' }}</span>
                    </div>
                </div>

                @if($customerService->remarks)
                <div class="mt-4">
                    <span class="text-secondary small d-block mb-2">Scope / Remarks</span>
                    <div class="p-3 bg-light rounded text-secondary" style="font-size: 0.9rem;">
                        {!! nl2br(e($customerService->remarks)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Infrastructure Link Panel (Domain or Hosting Specifics) -->
        @php
            $categoryName = $customerService->product->category->name ?? '';
        @endphp

        @if($categoryName === 'Domain')
            <div class="card card-custom mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-globe text-primary me-2"></i>Domain Registry Specifications</h5>
                    @if(!$customerService->domain)
                        @can('services.create')
                        <a href="{{ route('domains.create', ['customer_service_id' => $customerService->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i> Configure Registry Details
                        </a>
                        @endcan
                    @endif
                </div>
                <div class="card-body px-4">
                    @if($customerService->domain)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-secondary small d-block mb-1">Domain Name</span>
                                <h6 class="fw-bold text-primary">{{ $customerService->domain->domain_name }}</h6>
                            </div>
                            <div class="col-md-6">
                                <span class="text-secondary small d-block mb-1">Registrar</span>
                                <h6>{{ $customerService->domain->registrar }} <span class="text-secondary small">({{ $customerService->domain->registrar_account ?? '-' }})</span></h6>
                            </div>
                            <div class="col-md-6">
                                <span class="text-secondary small d-block mb-1">DNS Provider</span>
                                <h6>{{ $customerService->domain->dns_provider ?? '-' }}</h6>
                            </div>
                            <div class="col-md-6">
                                <span class="text-secondary small d-block mb-1">Nameservers</span>
                                <code class="d-block">{{ $customerService->domain->nameserver_1 }}</code>
                                <code class="d-block">{{ $customerService->domain->nameserver_2 }}</code>
                                @if($customerService->domain->nameserver_3)
                                    <code class="d-block">{{ $customerService->domain->nameserver_3 }}</code>
                                @endif
                                @if($customerService->domain->nameserver_4)
                                    <code class="d-block">{{ $customerService->domain->nameserver_4 }}</code>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-secondary text-center py-3 m-0">No Domain registry specifications are configured yet for this active service contract.</p>
                    @endif
                </div>
            </div>
        @elseif($categoryName === 'Hosting')
            <div class="card card-custom mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-network-wired text-primary me-2"></i>Web Hosting Specifications</h5>
                    @if(!$customerService->hosting)
                        @can('services.create')
                        <a href="{{ route('hostings.create', ['customer_service_id' => $customerService->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i> Configure Hosting details
                        </a>
                        @endcan
                    @endif
                </div>
                <div class="card-body px-4">
                    @if($customerService->hosting)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Hosting Server</span>
                                @if($customerService->hosting->server)
                                    <h6 class="fw-bold text-dark">{{ $customerService->hosting->server->name }}</h6>
                                    <span class="small text-secondary">{{ $customerService->hosting->server->ip_address }}</span>
                                @else
                                    <span class="text-secondary">Unassigned</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Hosting Type</span>
                                <h6>{{ $customerService->hosting->hosting_type }}</h6>
                            </div>
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Control Panel</span>
                                <span class="badge bg-light text-dark border">{{ $customerService->hosting->control_panel ?? 'None' }}</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Control Panel Username</span>
                                <code>{{ $customerService->hosting->username ?? '-' }}</code>
                            </div>
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Disk Limit</span>
                                <h6>{{ $customerService->hosting->disk_limit ?? '-' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <span class="text-secondary small d-block mb-1">Bandwidth Limit</span>
                                <h6>{{ $customerService->hosting->bandwidth_limit ?? '-' }}</h6>
                            </div>
                        </div>
                    @else
                        <p class="text-secondary text-center py-3 m-0">No Hosting specifications are configured yet for this active service contract.</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Renewal History Log -->
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-history text-secondary me-2"></i>Service Renewal History</h5>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Renewal Date</th>
                                <th>Old Expiry</th>
                                <th>New Expiry</th>
                                <th>Amount (₹)</th>
                                <th>Invoice</th>
                                <th>Creator</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerService->renewals as $renewal)
                                <tr>
                                    <td>{{ $renewal->renewal_date->format('Y-m-d') }}</td>
                                    <td>{{ $renewal->old_expiry->format('Y-m-d') }}</td>
                                    <td class="fw-bold text-success">{{ $renewal->new_expiry->format('Y-m-d') }}</td>
                                    <td class="fw-semibold">₹{{ number_format($renewal->amount, 2) }}</td>
                                    <td>
                                        @if($renewal->invoice)
                                            <a href="{{ route('invoices.show', $renewal->invoice_id) }}" class="text-decoration-none fw-bold">{{ $renewal->invoice->invoice_no }}</a>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $renewal->creator->name ?? 'System' }}</td>
                                    <td>
                                        @php
                                            $rBadge = 'bg-secondary';
                                            if ($renewal->status === 'Paid') $rBadge = 'bg-success';
                                            elseif ($renewal->status === 'Generated') $rBadge = 'bg-primary';
                                            elseif ($renewal->status === 'Pending') $rBadge = 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $rBadge }}">{{ $renewal->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-secondary">No renewals recorded for this service contract.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Context / Operations Panel -->
    <div class="col-lg-4">
        <!-- Quick Billing Panel -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Invoice Billing</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-secondary small">Generate a fresh billing invoice to request payment collection for this service agreement.</p>
                @can('invoices.create')
                    <form action="{{ route('customer-services.generate-invoice', $customerService->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-plus me-1"></i> Generate Invoice
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary w-100" disabled>
                        <i class="fa-solid fa-lock me-1"></i> Generation Locked
                    </button>
                @endcan
            </div>
        </div>
        
        <!-- Helpful notes/instructions -->
        <div class="card card-custom bg-light border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-secondary small mb-2"><i class="fa-solid fa-circle-info text-primary me-2"></i>Support Staff Guide</h6>
                <ul class="small text-secondary ps-3 mb-0">
                    <li class="mb-2">Changing the Service status to **Suspended** will automatically restrict clients but preserve history.</li>
                    <li class="mb-2">Clicking **Renew** lets you extend the expiry date, record pricing, and generate invoices with one click.</li>
                    <li>Verify DNS settings and nameservers when configuring Domain registry details.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    .stat-card .card-body {
        padding: 1.5rem;
        z-index: 2;
        position: relative;
    }
    .stat-icon {
        position: absolute;
        right: 15px;
        bottom: 10px;
        font-size: 4rem;
        opacity: 0.15;
        z-index: 1;
        transition: transform 0.3s ease;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }
    
    /* Stats Colors */
    .bg-gradient-customers { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .bg-gradient-active { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
    .bg-gradient-expiring { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); }
    .bg-gradient-expired { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
    .bg-gradient-invoices { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .bg-gradient-collection { background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%); }
    .bg-gradient-outstanding { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); }

    .renewal-tab-header {
        font-weight: 500;
        font-size: 0.95rem;
    }
</style>
@endsection

@section('content')
<!-- Stats Row -->
<div class="row">
    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-customers h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Total Customers</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($stats['total_customers']) }}</div>
                <i class="fa-solid fa-users stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Active Services -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-active h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Active Services</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($stats['active_services']) }}</div>
                <i class="fa-solid fa-cube stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Expiring Services -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-expiring h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Expiring (30 Days)</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($stats['expiring_services']) }}</div>
                <i class="fa-solid fa-hourglass-half stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Expired Services -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-expired h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Expired Services</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($stats['expired_services']) }}</div>
                <i class="fa-solid fa-circle-exclamation stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Pending Invoices -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-invoices h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Pending Invoices</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($stats['pending_invoices']) }}</div>
                <i class="fa-solid fa-file-invoice-dollar stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Collection -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card bg-gradient-collection h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Collection (This Month)</div>
                <div class="h2 mb-0 fw-bold">₹{{ number_format($stats['collection_this_month'], 2) }}</div>
                <i class="fa-solid fa-money-bill-trend-up stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Total Outstanding -->
    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card stat-card bg-gradient-outstanding h-100">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-white-50 small mb-1">Total Outstanding</div>
                <div class="h2 mb-0 fw-bold">₹{{ number_format($stats['total_outstanding'], 2) }}</div>
                <i class="fa-solid fa-hand-holding-dollar stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Expiry Summary Rows -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-bell text-warning me-2"></i>Renewal Summary (Expiring in 30 Days)</h5>
            </div>
            <div class="card-body px-4">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm" id="pills-domains-tab" data-bs-toggle="pill" data-bs-target="#pills-domains" type="button" role="tab">
                            Domains ({{ $renewals['domains_expiring']->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm" id="pills-hostings-tab" data-bs-toggle="pill" data-bs-target="#pills-hostings" type="button" role="tab">
                            Hostings ({{ $renewals['hostings_expiring']->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm" id="pills-maintenance-tab" data-bs-toggle="pill" data-bs-target="#pills-maintenance" type="button" role="tab">
                            Maintenance Agreements ({{ $renewals['maintenance_expiring']->count() }})
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <!-- Domains -->
                    <div class="tab-pane fade show active" id="pills-domains" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Domain Name</th>
                                        <th>Customer</th>
                                        <th>Registrar</th>
                                        <th>Expiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($renewals['domains_expiring'] as $domain)
                                        <tr>
                                            <td class="fw-semibold text-primary">{{ $domain->domain_name }}</td>
                                            <td>{{ $domain->customerService->customer->company_name ?? 'N/A' }}</td>
                                            <td>{{ $domain->registrar }}</td>
                                            <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $domain->expiry_date->format('Y-m-d') }}</span></td>
                                            <td>
                                                <a href="{{ route('customer-services.show', $domain->customer_service_id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-secondary">No domains expiring in next 30 days.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hostings -->
                    <div class="tab-pane fade" id="pills-hostings" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Server</th>
                                        <th>Type</th>
                                        <th>Expiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($renewals['hostings_expiring'] as $hosting)
                                        <tr>
                                            <td class="fw-semibold">{{ $hosting->customerService->customer->company_name ?? 'N/A' }}</td>
                                            <td>{{ $hosting->server->name ?? 'N/A' }} ({{ $hosting->server->ip_address ?? '' }})</td>
                                            <td>{{ $hosting->hosting_type }}</td>
                                            <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $hosting->customerService->expiry_date->format('Y-m-d') }}</span></td>
                                            <td>
                                                <a href="{{ route('customer-services.show', $hosting->customer_service_id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-secondary">No hostings expiring in next 30 days.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Maintenance -->
                    <div class="tab-pane fade" id="pills-maintenance" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service Name</th>
                                        <th>Expiry Date</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($renewals['maintenance_expiring'] as $service)
                                        <tr>
                                            <td class="fw-semibold">{{ $service->customer->company_name }}</td>
                                            <td>{{ $service->service_name }}</td>
                                            <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $service->expiry_date->format('Y-m-d') }}</span></td>
                                            <td>₹{{ number_format($service->amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('customer-services.show', $service->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-secondary">No maintenance AMC expiring in next 30 days.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Collections & Upcoming Renewals -->
<div class="row">
    <!-- Upcoming Renewals -->
    <div class="col-lg-6 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-regular fa-calendar-days text-primary me-2"></i>Upcoming Renewals (Next 20 Due)</h5>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingRenewals as $service)
                                <tr>
                                    <td>{{ $service->customer->company_name }}</td>
                                    <td>{{ $service->service_name }}</td>
                                    <td>
                                        @php
                                            $daysLeft = Carbon\Carbon::today()->diffInDays($service->expiry_date, false);
                                            $badge = 'bg-secondary';
                                            if ($daysLeft <= 0) $badge = 'bg-danger';
                                            elseif ($daysLeft <= 7) $badge = 'bg-warning text-dark';
                                            elseif ($daysLeft <= 15) $badge = 'bg-info text-white';
                                        @endphp
                                        <span class="badge {{ $badge }}" style="font-size: 0.8rem;">
                                            {{ $service->expiry_date->format('Y-m-d') }}
                                            ({{ $daysLeft <= 0 ? 'Expired' : $daysLeft . ' days left' }})
                                        </span>
                                    </td>
                                    <td class="fw-bold">₹{{ number_format($service->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-secondary">No upcoming renewals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-lg-6 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-wallet text-success me-2"></i>Recent Payments (Last 10 Collections)</h5>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Customer</th>
                                <th>Invoice No</th>
                                <th>Method</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td class="fw-semibold">{{ $payment->customer->company_name ?? 'N/A' }}</td>
                                    <td><a href="{{ route('invoices.show', $payment->invoice_id) }}" class="text-decoration-none fw-semibold">{{ $payment->invoice->invoice_no ?? 'N/A' }}</a></td>
                                    <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                                    <td class="fw-bold text-success">+₹{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-secondary">No recent payments logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

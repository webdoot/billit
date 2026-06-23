@extends('layouts.app')

@section('title', 'Reports')
@section('page_title', 'Business Reports & Analytics')

@section('content')
<!-- Reports Tab System -->
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="reportsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="outstanding-tab" data-bs-toggle="tab" data-bs-target="#outstanding" type="button" role="tab">
                            <i class="fa-solid fa-hand-holding-dollar text-danger me-2"></i>Outstanding Invoices ({{ $outstandingInvoices->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="collection-tab" data-bs-toggle="tab" data-bs-target="#collection" type="button" role="tab">
                            <i class="fa-solid fa-money-bill-trend-up text-success me-2"></i>Collection Report
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="renewals-tab" data-bs-toggle="tab" data-bs-target="#renewals" type="button" role="tab">
                            <i class="fa-regular fa-clock text-warning me-2"></i>Upcoming Renewals
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="services-rpt-tab" data-bs-toggle="tab" data-bs-target="#services-rpt" type="button" role="tab">
                            <i class="fa-solid fa-cube text-primary me-2"></i>Service Statuses
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="tab-content" id="reportsTabContent">
                    
                    <!-- Outstanding Tab -->
                    <div class="tab-pane fade show active" id="outstanding" role="tabpanel">
                        <div class="p-3 bg-danger-subtle rounded text-danger mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold m-0"><i class="fa-solid fa-circle-exclamation me-2"></i>Total Outstanding Receivables</h6>
                                <p class="small m-0 text-secondary">Summary of all outstanding sent, partial, or overdue invoices.</p>
                            </div>
                            <h3 class="fw-bold m-0">₹{{ number_format($outstandingInvoices->sum('balance'), 2) }}</h3>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle report-datatable">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Customer Name</th>
                                        <th>Invoice Date</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-end text-danger">Outstanding Balance</th>
                                        <th>Status</th>
                                        <th style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outstandingInvoices as $invoice)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $invoice->invoice_no }}</td>
                                            <td class="fw-semibold">{{ $invoice->customer->company_name }}</td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td>
                                                @php
                                                    $isPast = $invoice->due_date->isPast();
                                                @endphp
                                                <span class="{{ $isPast ? 'text-danger fw-bold' : '' }}">
                                                    {{ $invoice->due_date->format('Y-m-d') }}
                                                    {!! $isPast ? '<small class="d-block">(Overdue)</small>' : '' !!}
                                                </span>
                                            </td>
                                            <td class="text-end">₹{{ number_format($invoice->total, 2) }}</td>
                                            <td class="text-end fw-bold text-danger">₹{{ number_format($invoice->balance, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $invoice->status === 'Overdue' ? 'bg-danger' : ($invoice->status === 'Partial' ? 'bg-info text-white' : 'bg-primary') }}">
                                                    {{ $invoice->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Collection Tab -->
                    <div class="tab-pane fade" id="collection" role="tabpanel">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 mb-4 align-items-end">
                            <input type="hidden" name="tab" value="collection">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label small text-secondary fw-semibold">Start Date</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label small text-secondary fw-semibold">End Date</label>
                                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-filter me-1"></i> Filter Collections</button>
                            </div>
                        </form>

                        <!-- Collection Breakdown Summary -->
                        <div class="row mb-4 g-3">
                            <div class="col-lg-4">
                                <div class="p-3 bg-success-subtle rounded text-success text-center">
                                    <span class="small text-uppercase fw-semibold d-block mb-1">Total Collections in Period</span>
                                    <h3 class="fw-bold m-0">₹{{ number_format($collectionSummary['total'], 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="p-3 bg-light rounded h-100">
                                    <span class="small text-secondary text-uppercase fw-semibold d-block mb-2">Breakdown by Payment Method</span>
                                    <div class="d-flex flex-wrap gap-3">
                                        @forelse($collectionSummary['by_method'] as $method => $amount)
                                            <span class="badge bg-white text-dark border p-2" style="font-size: 0.9rem;">
                                                <strong>{{ $method }}:</strong> ₹{{ number_format($amount, 2) }}
                                            </span>
                                        @empty
                                            <span class="text-secondary small">No collections recorded in selected period.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collection List -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle report-datatable">
                                <thead>
                                    <tr>
                                        <th>Collection Date</th>
                                        <th>Customer</th>
                                        <th>Invoice Reference</th>
                                        <th>Payment Method</th>
                                        <th>Transaction No</th>
                                        <th class="text-end">Collected Amount</th>
                                        <th style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collections as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                            <td class="fw-semibold text-dark">{{ $payment->customer->company_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->invoice->invoice_no ?? 'N/A' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                                            <td><code>{{ $payment->transaction_no ?? '-' }}</code></td>
                                            <td class="text-end fw-bold text-success">+₹{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Renewals Tab -->
                    <div class="tab-pane fade" id="renewals" role="tabpanel">
                        <div class="alert alert-info border-0 mb-4" role="alert" style="border-left: 4px solid #3b82f6 !important;">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Showing services expiring within the **next 90 days** (from today).
                        </div>

                        <ul class="nav nav-pills mb-3" id="renewalSubTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active btn-sm" id="subpill-domains-tab" data-bs-toggle="pill" data-bs-target="#subpill-domains" type="button" role="tab">
                                    Domains Due ({{ $expiringDomains->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm" id="subpill-hostings-tab" data-bs-toggle="pill" data-bs-target="#subpill-hostings" type="button" role="tab">
                                    Hostings Due ({{ $expiringHostings->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm" id="subpill-maintenance-tab" data-bs-toggle="pill" data-bs-target="#subpill-maintenance" type="button" role="tab">
                                    Maintenance AMC Due ({{ $expiringMaintenance->count() }})
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="renewalSubTabContent">
                            <!-- Domains -->
                            <div class="tab-pane fade show active" id="subpill-domains" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle report-datatable">
                                        <thead>
                                            <tr>
                                                <th>Domain Name</th>
                                                <th>Customer</th>
                                                <th>Registrar</th>
                                                <th>Expiry Date</th>
                                                <th style="width: 80px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expiringDomains as $domain)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $domain->domain_name }}</td>
                                                    <td>{{ $domain->customerService->customer->company_name ?? 'N/A' }}</td>
                                                    <td>{{ $domain->registrar }}</td>
                                                    <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $domain->expiry_date->format('Y-m-d') }}</span></td>
                                                    <td>
                                                        <a href="{{ route('customer-services.show', $domain->customer_service_id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Hostings -->
                            <div class="tab-pane fade" id="subpill-hostings" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle report-datatable">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Server</th>
                                                <th>Hosting Type</th>
                                                <th>Expiry Date</th>
                                                <th style="width: 80px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expiringHostings as $hosting)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $hosting->customerService->customer->company_name ?? 'N/A' }}</td>
                                                    <td>{{ $hosting->server->name ?? 'N/A' }} ({{ $hosting->server->ip_address ?? '' }})</td>
                                                    <td>{{ $hosting->hosting_type }}</td>
                                                    <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $hosting->customerService->expiry_date->format('Y-m-d') }}</span></td>
                                                    <td>
                                                        <a href="{{ route('customer-services.show', $hosting->customer_service_id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Maintenance -->
                            <div class="tab-pane fade" id="subpill-maintenance" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle report-datatable">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Service Agreement Name</th>
                                                <th>Amount</th>
                                                <th>Expiry Date</th>
                                                <th style="width: 80px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expiringMaintenance as $service)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $service->customer->company_name }}</td>
                                                    <td>{{ $service->service_name }}</td>
                                                    <td>₹{{ number_format($service->amount, 2) }}</td>
                                                    <td><span class="text-danger fw-semibold"><i class="fa-regular fa-clock me-1"></i>{{ $service->expiry_date->format('Y-m-d') }}</span></td>
                                                    <td>
                                                        <a href="{{ route('customer-services.show', $service->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Statuses Tab -->
                    <div class="tab-pane fade" id="services-rpt" role="tabpanel">
                        <!-- Stats Row -->
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-md-3">
                                <div class="p-3 bg-success-subtle rounded text-success">
                                    <span class="small text-uppercase fw-semibold d-block mb-1">Active Contracts</span>
                                    <h4 class="fw-bold m-0">{{ isset($servicesGrouped['Active']) ? $servicesGrouped['Active']->count() : 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-danger-subtle rounded text-danger">
                                    <span class="small text-uppercase fw-semibold d-block mb-1">Expired Contracts</span>
                                    <h4 class="fw-bold m-0">{{ isset($servicesGrouped['Expired']) ? $servicesGrouped['Expired']->count() : 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-warning-subtle rounded text-warning">
                                    <span class="small text-uppercase fw-semibold d-block mb-1">Suspended Contracts</span>
                                    <h4 class="fw-bold m-0">{{ isset($servicesGrouped['Suspended']) ? $servicesGrouped['Suspended']->count() : 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded text-secondary">
                                    <span class="small text-uppercase fw-semibold d-block mb-1">Cancelled Contracts</span>
                                    <h4 class="fw-bold m-0">{{ isset($servicesGrouped['Cancelled']) ? $servicesGrouped['Cancelled']->count() : 0 }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- All Services list -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle report-datatable">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service/Package</th>
                                        <th>Billing Cycle</th>
                                        <th>Price (₹)</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servicesGrouped as $status => $services)
                                        @foreach($services as $service)
                                            <tr>
                                                <td class="fw-semibold text-dark">{{ $service->customer->company_name ?? 'N/A' }}</td>
                                                <td>{{ $service->service_name }} <span class="badge bg-light text-secondary border small ms-1">{{ $service->product->category->name ?? '' }}</span></td>
                                                <td>{{ $service->billing_cycle }}</td>
                                                <td>₹{{ number_format($service->amount, 2) }}</td>
                                                <td>{{ $service->expiry_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        if ($status === 'Active') $badgeClass = 'bg-success';
                                                        elseif ($status === 'Expired') $badgeClass = 'bg-danger';
                                                        elseif ($status === 'Suspended') $badgeClass = 'bg-warning text-dark';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('customer-services.show', $service->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Init Datatable on all reports tables
        $('.report-datatable').DataTable({
            order: [],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search report..."
            }
        });

        // Maintain active tab on collection filters reload
        var urlParams = new URLSearchParams(window.location.search);
        var activeTab = urlParams.get('tab');
        if (activeTab === 'collection') {
            $('#collection-tab').tab('show');
        }
    });
</script>
@endsection

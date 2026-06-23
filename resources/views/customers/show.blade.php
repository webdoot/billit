@extends('layouts.app')

@section('title', 'Customer Profile')
@section('page_title', 'Customer Profile Details')

@section('content')
<div class="row">
    <!-- Profile Card (Left Panel) -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card card-custom h-100">
            <div class="card-body pt-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; border: 2px solid var(--primary-color);">
                        {{ strtoupper(substr($customer->company_name, 0, 2)) }}
                    </div>
                    <h5 class="fw-bold m-0">{{ $customer->company_name }}</h5>
                    <p class="text-secondary small mt-1 mb-2">{{ $customer->customer_code }}</p>
                    <span class="badge {{ $customer->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $customer->status }}</span>
                </div>

                <hr class="text-light-hover">

                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase text-secondary small mb-3">Contact Information</h6>
                    <p class="mb-2"><i class="fa-regular fa-user text-primary me-2" style="width: 16px;"></i>{{ $customer->contact_person }}</p>
                    <p class="mb-2"><i class="fa-regular fa-envelope text-primary me-2" style="width: 16px;"></i><a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a></p>
                    <p class="mb-2"><i class="fa-solid fa-mobile-screen text-primary me-2" style="width: 16px;"></i>{{ $customer->mobile }}</p>
                    @if($customer->alternate_mobile)
                        <p class="mb-2"><i class="fa-solid fa-phone text-secondary me-2" style="width: 16px;"></i>{{ $customer->alternate_mobile }}</p>
                    @endif
                    @if($customer->website)
                        <p class="mb-0"><i class="fa-solid fa-globe text-primary me-2" style="width: 16px;"></i><a href="{{ $customer->website }}" target="_blank" class="text-decoration-none">{{ $customer->website }}</a></p>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase text-secondary small mb-3">Billing & Tax Details</h6>
                    <p class="mb-2"><strong>GSTIN:</strong> {{ $customer->gstin ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>PAN:</strong> {{ $customer->pan ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Address:</strong><br>
                        {!! nl2br(e($customer->address)) !!}<br>
                        {{ $customer->city }}{{ $customer->state ? ', ' . $customer->state : '' }} {{ $customer->pin_code }}<br>
                        {{ $customer->country }}
                    </p>
                </div>

                @if($customer->notes)
                <div class="mb-0">
                    <h6 class="fw-bold text-uppercase text-secondary small mb-2">Notes</h6>
                    <div class="p-3 bg-light rounded text-secondary" style="font-size: 0.9rem;">
                        {!! nl2br(e($customer->notes)) !!}
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer bg-transparent border-0 pb-4 px-4 d-flex justify-content-between">
                <a href="{{ route('customers.ledger', $customer->id) }}" class="btn btn-outline-secondary w-100 me-2">
                    <i class="fa-solid fa-list-check me-1"></i> View Ledger
                </a>
                @can('customers.edit')
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary w-100">
                    <i class="fa-regular fa-edit me-1"></i> Edit Profile
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Related Modules (Right Panel) -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                            Services ({{ $customer->customerServices->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                            Invoices ({{ $customer->invoices->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                            Payments ({{ $customer->payments->count() }})
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body px-4">
                <div class="tab-content" id="myTabContent">
                    <!-- Services Tab -->
                    <div class="tab-pane fade show active" id="services" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            @can('services.create')
                            <a href="{{ route('customer-services.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus me-1"></i> Add Service
                            </a>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Billing Cycle</th>
                                        <th>Amount</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->customerServices as $service)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $service->service_name }}</td>
                                            <td>{{ $service->billing_cycle }}</td>
                                            <td class="fw-semibold">₹{{ number_format($service->amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $isExpired = $service->expiry_date->isPast();
                                                    $daysLeft = Carbon\Carbon::today()->diffInDays($service->expiry_date, false);
                                                @endphp
                                                <span class="{{ $isExpired ? 'text-danger fw-semibold' : '' }}">
                                                    {{ $service->expiry_date->format('Y-m-d') }}
                                                    @if(!$isExpired && $daysLeft <= 30)
                                                        <span class="text-warning small d-block">({{ $daysLeft }} days left)</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusBadge = 'bg-secondary';
                                                    if ($service->status === 'Active') $statusBadge = 'bg-success';
                                                    elseif ($service->status === 'Expired') $statusBadge = 'bg-danger';
                                                    elseif ($service->status === 'Suspended') $statusBadge = 'bg-warning text-dark';
                                                @endphp
                                                <span class="badge {{ $statusBadge }}">{{ $service->status }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('customer-services.show', $service->id) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-secondary">No services assigned to this customer yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Invoices Tab -->
                    <div class="tab-pane fade" id="invoices" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            @can('invoices.create')
                            <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-file-invoice me-1"></i> Generate Invoice
                            </a>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Total</th>
                                        <th>Outstanding Balance</th>
                                        <th>Status</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->invoices as $invoice)
                                        <tr>
                                            <td class="fw-semibold text-primary"><a href="{{ route('invoices.show', $invoice->id) }}">{{ $invoice->invoice_no }}</a></td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td class="fw-semibold">₹{{ number_format($invoice->total, 2) }}</td>
                                            <td class="fw-bold {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($invoice->balance, 2) }}</td>
                                            <td>
                                                @php
                                                    $invBadge = 'bg-secondary';
                                                    if ($invoice->status === 'Paid') $invBadge = 'bg-success';
                                                    elseif ($invoice->status === 'Partial') $invBadge = 'bg-info text-white';
                                                    elseif ($invoice->status === 'Sent') $invBadge = 'bg-primary';
                                                    elseif ($invoice->status === 'Overdue') $invBadge = 'bg-danger';
                                                @endphp
                                                <span class="badge {{ $invBadge }}">{{ $invoice->status }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fa-regular fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-secondary">No invoices generated for this customer yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payments Tab -->
                    <div class="tab-pane fade" id="payments" role="tabpanel">
                        <div class="table-responsive py-3">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Invoice No</th>
                                        <th>Method</th>
                                        <th>Transaction No</th>
                                        <th>Amount</th>
                                        <th style="width: 80px;">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($payment->invoice)
                                                    <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="fw-semibold">{{ $payment->invoice->invoice_no }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                                            <td><code>{{ $payment->transaction_no ?? '-' }}</code></td>
                                            <td class="fw-bold text-success">₹{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if($payment->receipt)
                                                    <a href="{{ route('receipts.show', $payment->receipt->id) }}" class="btn btn-sm btn-outline-success" title="View Receipt"><i class="fa-solid fa-receipt"></i></a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-secondary">No payments collected from this customer yet.</td>
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
@endsection

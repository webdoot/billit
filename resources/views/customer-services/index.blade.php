@extends('layouts.app')

@section('title', 'Customer Services')
@section('page_title', 'Customer Service Agreements')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-cubes text-primary me-2"></i>Active Customer Service Agreements</h5>
                @can('services.create')
                <a href="{{ route('customer-services.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> New Service Assignment
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="services-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service/Package Name</th>
                                <th>Cycle</th>
                                <th>Amount</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Auto Renew</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $service->customer->company_name ?? 'N/A' }}</td>
                                    <td>{{ $service->service_name }} <span class="badge bg-light text-secondary border small ms-1">{{ $service->product->category->name ?? '' }}</span></td>
                                    <td>{{ $service->billing_cycle }}</td>
                                    <td class="fw-semibold">₹{{ number_format($service->amount, 2) }}</td>
                                    <td>{{ $service->start_date->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $isExpired = $service->expiry_date->isPast();
                                            $daysLeft = Carbon\Carbon::today()->diffInDays($service->expiry_date, false);
                                        @endphp
                                        <span class="fw-semibold {{ $isExpired ? 'text-danger' : ($daysLeft <= 30 ? 'text-warning' : 'text-dark') }}">
                                            {{ $service->expiry_date->format('Y-m-d') }}
                                            @if(!$isExpired && $daysLeft <= 30)
                                                <span class="small d-block text-secondary">({{ $daysLeft }} days left)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $service->auto_renew ? 'bg-success' : 'bg-light text-dark border' }}">
                                            {{ $service->auto_renew ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = 'bg-secondary';
                                            if ($service->status === 'Active') $statusBadge = 'bg-success';
                                            elseif ($service->status === 'Expired') $statusBadge = 'bg-danger';
                                            elseif ($service->status === 'Suspended') $statusBadge = 'bg-warning text-dark';
                                            elseif ($service->status === 'Cancelled') $statusBadge = 'bg-dark';
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">{{ $service->status }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('customer-services.show', $service->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Specifications">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>

                                        @can('services.edit')
                                        <a href="{{ route('customer-services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fa-regular fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('services.delete')
                                        <form action="{{ route('customer-services.destroy', $service->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-secondary">No customer services mapped yet.</td>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('#services-table').DataTable({
            order: [[5, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search services..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this service agreement will also remove related domains, hostings and renewal history!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection

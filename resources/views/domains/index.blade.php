@extends('layouts.app')

@section('title', 'Domain Registry')
@section('page_title', 'Domain Registry Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-globe text-primary me-2"></i>Domain Registrations</h5>
                @can('services.create')
                <a href="{{ route('domains.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add Domain Details
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="domains-table">
                        <thead>
                            <tr>
                                <th>Domain Name</th>
                                <th>Customer</th>
                                <th>Registrar</th>
                                <th>Purchase Date</th>
                                <th>Expiry Date</th>
                                <th>Auto Renew</th>
                                <th>DNS Provider</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($domains as $domain)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $domain->domain_name }}</td>
                                    <td class="fw-semibold">{{ $domain->customerService->customer->company_name ?? 'N/A' }}</td>
                                    <td>{{ $domain->registrar }} <span class="text-secondary small d-block">{{ $domain->registrar_account }}</span></td>
                                    <td>{{ $domain->purchase_date ? $domain->purchase_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        @php
                                            $isExpired = $domain->expiry_date->isPast();
                                            $daysLeft = Carbon\Carbon::today()->diffInDays($domain->expiry_date, false);
                                        @endphp
                                        <span class="fw-semibold {{ $isExpired ? 'text-danger' : ($daysLeft <= 30 ? 'text-warning' : 'text-dark') }}">
                                            {{ $domain->expiry_date->format('Y-m-d') }}
                                            @if(!$isExpired && $daysLeft <= 30)
                                                <span class="small d-block text-secondary">({{ $daysLeft }} days left)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $domain->auto_renew ? 'bg-success' : 'bg-light text-dark border' }}">
                                            {{ $domain->auto_renew ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $domain->dns_provider ?? '-' }}</td>
                                    <td>
                                        @php
                                            $dBadge = 'bg-secondary';
                                            if ($domain->status === 'Active') $dBadge = 'bg-success';
                                            elseif ($domain->status === 'Expired') $dBadge = 'bg-danger';
                                            elseif ($domain->status === 'Transferred') $dBadge = 'bg-info text-white';
                                        @endphp
                                        <span class="badge {{ $dBadge }}">{{ $domain->status }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        @can('services.edit')
                                        <a href="{{ route('domains.edit', $domain->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-regular fa-edit" title="Edit"></i>
                                        </a>
                                        @endcan

                                        @can('services.delete')
                                        <form action="{{ route('domains.destroy', $domain->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete">
                                                <i class="fa-regular fa-trash-can" title="Delete"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-secondary">No domain details logged yet.</td>
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
        $('#domains-table').DataTable({
            order: [[4, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search domains..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this domain registry entry will remove the details.",
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

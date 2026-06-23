@extends('layouts.app')

@section('title', 'Hosting Accounts')
@section('page_title', 'Hosting Account Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-network-wired text-primary me-2"></i>Hosting Account Configurations</h5>
                @can('services.create')
                <a href="{{ route('hostings.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add Hosting
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="hostings-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service Package</th>
                                <th>Server</th>
                                <th>Type</th>
                                <th>Panel</th>
                                <th>Username</th>
                                <th>Disk Limit</th>
                                <th>Bandwidth</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostings as $hosting)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $hosting->customerService->customer->company_name ?? 'N/A' }}</td>
                                    <td>{{ $hosting->customerService->service_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($hosting->server)
                                            <span class="fw-bold text-primary">{{ $hosting->server->name }}</span>
                                            <span class="small d-block text-secondary">{{ $hosting->server->ip_address }}</span>
                                        @else
                                            <span class="text-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $hosting->hosting_type }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $hosting->control_panel ?? 'None' }}</span></td>
                                    <td><code>{{ $hosting->username ?? '-' }}</code></td>
                                    <td>{{ $hosting->disk_limit ?? '-' }}</td>
                                    <td>{{ $hosting->bandwidth_limit ?? '-' }}</td>
                                    <td>
                                        @php
                                            $hBadge = 'bg-secondary';
                                            if ($hosting->status === 'Active') $hBadge = 'bg-success';
                                            elseif ($hosting->status === 'Suspended') $hBadge = 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $hBadge }}">{{ $hosting->status }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        @can('services.edit')
                                        <a href="{{ route('hostings.edit', $hosting->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-regular fa-edit" title="Edit"></i>
                                        </a>
                                        @endcan

                                        @can('services.delete')
                                        <form action="{{ route('hostings.destroy', $hosting->id) }}" method="POST" class="d-inline delete-form">
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
                                    <td colspan="10" class="text-center py-4 text-secondary">No hosting accounts setup yet.</td>
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
        $('#hostings-table').DataTable({
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search hostings..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this hosting record will remove details permanently.",
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

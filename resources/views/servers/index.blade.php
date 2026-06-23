@extends('layouts.app')

@section('title', 'Server Inventory')
@section('page_title', 'Server Inventory Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-server text-primary me-2"></i>Infrastructure Servers</h5>
                @can('servers.create')
                <a href="{{ route('servers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add Server
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="servers-table">
                        <thead>
                            <tr>
                                <th>Server Name</th>
                                <th>Provider</th>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Monthly Cost (₹)</th>
                                <th>Renewal Date</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servers as $server)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $server->name }} <span class="text-secondary small d-block">{{ $server->hostname }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $server->provider }}</span></td>
                                    <td><code>{{ $server->ip_address ?? '-' }}</code></td>
                                    <td>{{ $server->location ?? '-' }}</td>
                                    <td class="fw-semibold">₹{{ number_format($server->monthly_cost, 2) }}</td>
                                    <td>
                                        @if($server->renewal_date)
                                            {{ $server->renewal_date->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $server->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $server->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @can('servers.edit')
                                        <a href="{{ route('servers.edit', $server->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-regular fa-edit" title="Edit"></i>
                                        </a>
                                        @endcan

                                        @can('servers.delete')
                                        <form action="{{ route('servers.destroy', $server->id) }}" method="POST" class="d-inline delete-form">
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
                                    <td colspan="8" class="text-center py-4 text-secondary">No servers registered in the system yet.</td>
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
        $('#servers-table').DataTable({
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search servers..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this server will remove it permanently. Check that no hostings are mapped first.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection

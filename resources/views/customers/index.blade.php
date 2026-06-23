@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customer Directory')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-users text-primary me-2"></i>All Customers</h5>
                <div>
                    <a href="{{ route('customers.export') }}" class="btn btn-outline-success btn-sm me-2">
                        <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                    </a>
                    @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-user-plus me-1"></i> Add Customer
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="customers-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
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
        var table = $('#customers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('customers.index') }}",
            columns: [
                { data: 'customer_code', name: 'customer_code', className: 'fw-semibold text-dark' },
                { data: 'company_name', name: 'company_name', className: 'fw-semibold' },
                { data: 'contact_person', name: 'contact_person' },
                { data: 'email', name: 'email' },
                { data: 'mobile', name: 'mobile' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        var badge = data === 'Active' ? 'bg-success' : 'bg-secondary';
                        return '<span class="badge ' + badge + '">' + data + '</span>';
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This customer record will be soft deleted!",
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

@extends('layouts.app')

@section('title', 'Invoices')
@section('page_title', 'Invoice Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-file-invoice text-primary me-2"></i>Billing Invoices</h5>
                @can('invoices.create')
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Generate Invoice
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="invoices-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Total (₹)</th>
                                <th>Balance (₹)</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
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
        $('#invoices-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('invoices.index') }}",
            columns: [
                { 
                    data: 'invoice_no', 
                    name: 'invoice_no', 
                    className: 'fw-bold text-dark',
                    render: function(data, type, row) {
                        return '<a href="/invoices/' + row.id + '" class="text-decoration-none">' + data + '</a>';
                    }
                },
                { data: 'customer.company_name', name: 'customer.company_name', className: 'fw-semibold' },
                { 
                    data: 'invoice_date', 
                    name: 'invoice_date',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                { 
                    data: 'due_date', 
                    name: 'due_date',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                { 
                    data: 'total', 
                    name: 'total', 
                    className: 'fw-semibold text-end',
                    render: function(data) {
                        return '₹' + parseFloat(data).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                },
                { 
                    data: 'balance', 
                    name: 'balance', 
                    className: 'fw-bold text-end',
                    render: function(data) {
                        var colorClass = parseFloat(data) > 0 ? 'text-danger' : 'text-success';
                        return '<span class="' + colorClass + '">₹' + parseFloat(data).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</span>';
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        var badge = 'bg-secondary';
                        if (data === 'Paid') badge = 'bg-success';
                        else if (data === 'Partial') badge = 'bg-info text-white';
                        else if (data === 'Sent') badge = 'bg-primary';
                        else if (data === 'Overdue') badge = 'bg-danger';
                        else if (data === 'Cancelled') badge = 'bg-dark';
                        return '<span class="badge ' + badge + '">' + data + '</span>';
                    }
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-end'
                }
            ],
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search invoices..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this invoice record is permanent. Linked payments block deletion.",
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

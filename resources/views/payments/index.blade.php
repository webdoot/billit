@extends('layouts.app')

@section('title', 'Payments Collected')
@section('page_title', 'Payment Transactions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-wallet text-primary me-2"></i>Collection History</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="payments-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Invoice No</th>
                                <th>Method</th>
                                <th>Transaction No</th>
                                <th class="text-end">Amount (₹)</th>
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
        $('#payments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('payments.index') }}",
            columns: [
                { 
                    data: 'payment_date', 
                    name: 'payment_date',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                { data: 'customer.company_name', name: 'customer.company_name', className: 'fw-semibold' },
                { 
                    data: 'invoice.invoice_no', 
                    name: 'invoice.invoice_no',
                    className: 'fw-bold',
                    render: function(data, type, row) {
                        return data ? '<a href="/invoices/' + row.invoice_id + '" class="text-decoration-none">' + data + '</a>' : 'N/A';
                    }
                },
                { 
                    data: 'payment_method', 
                    name: 'payment_method',
                    render: function(data) {
                        return '<span class="badge bg-light text-dark border">' + data + '</span>';
                    }
                },
                { 
                    data: 'transaction_no', 
                    name: 'transaction_no',
                    render: function(data) {
                        return data ? '<code>' + data + '</code>' : '-';
                    }
                },
                { 
                    data: 'amount', 
                    name: 'amount', 
                    className: 'fw-bold text-success text-end',
                    render: function(data) {
                        return '₹' + parseFloat(data).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                searchPlaceholder: "Search collections..."
            }
        });

        // SweetAlert Void Payment Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Void Payment Transaction?',
                text: "This will remove the transaction, delete the receipt, and restore the outstanding balance on the invoice!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection

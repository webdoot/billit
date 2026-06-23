@extends('layouts.app')

@section('title', 'Receipts')
@section('page_title', 'Billing Receipts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-receipt text-primary me-2"></i>Generated Payment Receipts</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="receipts-table">
                        <thead>
                            <tr>
                                <th>Receipt No</th>
                                <th>Customer</th>
                                <th>Receipt Date</th>
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
        $('#receipts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('receipts.index') }}",
            columns: [
                { 
                    data: 'receipt_no', 
                    name: 'receipt_no', 
                    className: 'fw-bold text-dark',
                    render: function(data, type, row) {
                        return '<a href="/receipts/' + row.id + '" class="text-decoration-none">' + data + '</a>';
                    }
                },
                { data: 'customer_name', name: 'customer_name', className: 'fw-semibold' },
                { 
                    data: 'receipt_date', 
                    name: 'receipt_date',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
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
                searchPlaceholder: "Search receipts..."
            }
        });
    });
</script>
@endsection

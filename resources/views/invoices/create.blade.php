@extends('layouts.app')

@section('title', 'Generate Invoice')
@section('page_title', 'Create Invoice')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>New Billing Invoice</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
                    @csrf
                    
                    <!-- Meta Row -->
                    <div class="row mb-4">
                        <div class="col-lg-4 mb-3">
                            <label for="customer_id" class="form-label">Select Customer <span class="text-danger">*</span></label>
                            <select class="form-select select2-enable @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" name="customer_id" required>
                                <option value="">Choose Customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ ($selectedCustomerId == $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-lg-3 col-md-4 mb-3">
                            <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                   id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+15 days'))) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-2 col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Sent" selected>Sent</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-list me-2"></i>Invoice Line Items</h6>
                    <div class="table-responsive mb-3">
                        <table class="table align-middle" id="items-table">
                            <thead>
                                <tr class="table-light">
                                    <th style="width: 250px;">Linked Service</th>
                                    <th>Description / Scope <span class="text-danger">*</span></th>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 150px;">Rate (₹)</th>
                                    <th style="width: 150px; text-align: right;">Amount (₹)</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be added dynamically by jQuery -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item" disabled>
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                        <span id="select-customer-warning" class="text-secondary small ms-2"><i class="fa-solid fa-circle-exclamation text-warning me-1"></i>Please select a customer first to enable line items.</span>
                    </div>

                    <!-- Calculations and Notes -->
                    <div class="row">
                        <!-- Notes -->
                        <div class="col-lg-7 mb-4">
                            <label for="notes" class="form-label">Client Notes / Payment Instructions</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter bank details, UPI link, or generic invoice notes.">{{ old('notes') }}</textarea>
                        </div>
                        
                        <!-- Calculations -->
                        <div class="col-lg-5">
                            <div class="card border border-light-subtle bg-light-subtle rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Subtotal:</span>
                                    <span class="fw-semibold" id="label-subtotal">₹0.00</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-secondary">Discount (₹):</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" 
                                           id="discount" name="discount" value="0.00" style="width: 120px;">
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-secondary">Tax Rate (GST %):</span>
                                    <select class="form-select form-select-sm text-end" id="tax_rate" name="tax_rate" style="width: 120px;">
                                        <option value="18" selected>18% (IT)</option>
                                        <option value="0">0%</option>
                                        <option value="5">5%</option>
                                        <option value="12">12%</option>
                                        <option value="28">28%</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Calculated Tax (GST):</span>
                                    <span class="fw-semibold" id="label-tax">₹0.00</span>
                                </div>
                                <hr class="text-light-hover">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">Grand Total:</span>
                                    <span class="h4 fw-bold text-primary m-0" id="label-total">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="btn-submit">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var rowIndex = 0;
        var activeServices = [];

        // When Customer changes, fetch their active services
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            
            // Empty existing rows
            $('#items-table tbody').empty();
            rowIndex = 0;
            recalculateTotals();

            if (customerId) {
                $('#btn-add-item').prop('disabled', false);
                $('#select-customer-warning').hide();

                // Fetch services
                $.ajax({
                    url: '/invoices/customer/' + customerId + '/services',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        activeServices = data;
                        
                        // Add default empty row
                        addNewRow();
                    },
                    error: function() {
                        activeServices = [];
                        addNewRow();
                    }
                });
            } else {
                $('#btn-add-item').prop('disabled', true);
                $('#select-customer-warning').show();
                activeServices = [];
            }
        });

        // Trigger change if customer is pre-selected (e.g. from redirect)
        if ($('#customer_id').val()) {
            $('#customer_id').trigger('change');
        }

        // Add line item row
        $('#btn-add-item').on('click', function() {
            addNewRow();
        });

        function addNewRow() {
            var serviceOptions = '<option value="">Custom Item (No Service)</option>';
            activeServices.forEach(function(service) {
                serviceOptions += '<option value="' + service.id + '" data-price="' + service.amount + '" data-name="' + service.service_name + '">' + service.service_name + ' (₹' + service.amount + ')</option>';
            });

            var html = '<tr id="item-row-' + rowIndex + '">' +
                '<td>' +
                    '<select name="items[' + rowIndex + '][customer_service_id]" class="form-select form-select-sm service-select select2-enable">' +
                        serviceOptions +
                    '</select>' +
                '</td>' +
                '<td>' +
                    '<textarea name="items[' + rowIndex + '][description]" class="form-control form-control-sm item-desc" rows="1" required placeholder="Enter description..."></textarea>' +
                '</td>' +
                '<td>' +
                    '<input type="number" name="items[' + rowIndex + '][qty]" class="form-control form-control-sm text-center item-qty" value="1" min="1" required>' +
                '</td>' +
                '<td>' +
                    '<input type="number" name="items[' + rowIndex + '][rate]" step="0.01" min="0" class="form-control form-control-sm text-end item-rate" value="0.00" required>' +
                '</td>' +
                '<td class="text-end fw-semibold text-dark p-2">' +
                    '₹<span class="item-amount">0.00</span>' +
                '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-link text-danger btn-remove-row"><i class="fa-regular fa-trash-can"></i></button>' +
                '</td>' +
            '</tr>';

            $('#items-table tbody').append(html);
            
            // Initialize select2 on the new dropdown
            $('#item-row-' + rowIndex + ' .select2-enable').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            rowIndex++;
        }

        // Remove row
        $(document).on('click', '.btn-remove-row', function() {
            $(this).closest('tr').remove();
            recalculateTotals();
        });

        // Handle service selection change in row
        $(document).on('change', '.service-select', function() {
            var row = $(this).closest('tr');
            var selected = $(this).find(':selected');
            
            if (selected.val()) {
                var price = selected.data('price');
                var name = selected.data('name');

                row.find('.item-desc').val('Billing for: ' + name);
                row.find('.item-rate').val(price);
            }
            
            calculateRowAmount(row);
        });

        // Listen for input changes in rows to recalculate
        $(document).on('input change', '.item-qty, .item-rate, #discount, #tax_rate', function() {
            var row = $(this).closest('tr');
            if (row.length) {
                calculateRowAmount(row);
            } else {
                recalculateTotals();
            }
        });

        function calculateRowAmount(row) {
            var qty = parseInt(row.find('.item-qty').val()) || 0;
            var rate = parseFloat(row.find('.item-rate').val()) || 0;
            var amount = qty * rate;

            row.find('.item-amount').text(amount.toFixed(2));
            recalculateTotals();
        }

        function recalculateTotals() {
            var subtotal = 0.00;
            
            $('.item-amount').each(function() {
                subtotal += parseFloat($(this).text()) || 0;
            });

            var discount = parseFloat($('#discount').val()) || 0;
            var taxRate = parseFloat($('#tax_rate').val()) || 0;

            var taxableAmount = Math.max(0, subtotal - discount);
            var tax = taxableAmount * (taxRate / 100);
            var total = taxableAmount + tax;

            $('#label-subtotal').text('₹' + subtotal.toFixed(2));
            $('#label-tax').text('₹' + tax.toFixed(2));
            $('#label-total').text('₹' + total.toFixed(2));
        }

        // Form Submit check
        $('#invoice-form').on('submit', function(e) {
            var rows = $('#items-table tbody tr').length;
            if (rows === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'Please add at least one line item before generating the invoice.'
                });
            }
        });
    });
</script>
@endsection

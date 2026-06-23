@extends('layouts.app')

@section('title', 'Assign Service')
@section('page_title', 'Assign Service to Customer')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-plus text-primary me-2"></i>New Service Agreement Assignment</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('customer-services.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Select Customer <span class="text-danger">*</span></label>
                        <select class="form-select select2-enable @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                            <option value="">Choose Customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id') == $customer->id || $selectedCustomerId == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->company_name }} ({{ $customer->customer_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="service_product_id" class="form-label">Select Catalog Product <span class="text-danger">*</span></label>
                            <select class="form-select select2-enable @error('service_product_id') is-invalid @enderror" id="service_product_id" name="service_product_id" required>
                                <option value="">Choose Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->price }}"
                                            data-cycle="{{ $product->billing_cycle }}"
                                            data-category="{{ $product->category->name ?? '' }}"
                                            {{ old('service_product_id') == $product->id ? 'selected' : '' }}>
                                        [{{ $product->category->name ?? 'Other' }}] {{ $product->name }} (₹{{ number_format($product->price, 2) }} / {{ $product->billing_cycle }})
                                    </option>
                                @endforeach
                            </select>
                            @error('service_product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="service_name" class="form-label">Service Agreement Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('service_name') is-invalid @enderror" 
                                   id="service_name" name="service_name" value="{{ old('service_name') }}" required placeholder="e.g. company.com Domain">
                            @error('service_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                   id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                            <select class="form-select @error('billing_cycle') is-invalid @enderror" id="billing_cycle" name="billing_cycle" required>
                                <option value="">Select Cycle</option>
                                <option value="Monthly" {{ old('billing_cycle') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="Quarterly" {{ old('billing_cycle') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="Half Yearly" {{ old('billing_cycle') == 'Half Yearly' ? 'selected' : '' }}>Half Yearly</option>
                                <option value="Yearly" {{ old('billing_cycle') == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="One Time" {{ old('billing_cycle') == 'One Time' ? 'selected' : '' }}>One Time</option>
                            </select>
                            @error('billing_cycle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="amount" class="form-label">Agreed Price (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" required placeholder="0.00">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Agreement Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Pending" {{ old('status', 'Pending') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Suspended" {{ old('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="hidden" name="auto_renew" value="0">
                                <input class="form-check-input" type="checkbox" name="auto_renew" value="1" id="auto_renew" {{ old('auto_renew') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-semibold" for="auto_renew">
                                    Auto Renew Enabled
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Scope / Remarks</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3" placeholder="Specify any unique service terms.">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('customer-services.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Assign Service</button>
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
        // Auto populate fields on Product selection
        $('#service_product_id').on('change', function() {
            var selected = $(this).find(':selected');
            if (selected.val()) {
                var name = selected.data('name');
                var price = selected.data('price');
                var cycle = selected.data('cycle');

                $('#service_name').val(name);
                $('#amount').val(price);
                $('#billing_cycle').val(cycle);

                calculateExpiry();
            }
        });

        // Auto calculate Expiry Date based on Start Date + Billing Cycle
        $('#start_date, #billing_cycle').on('change', function() {
            calculateExpiry();
        });

        function calculateExpiry() {
            var startDateVal = $('#start_date').val();
            var cycleVal = $('#billing_cycle').val();
            
            if (startDateVal && cycleVal) {
                var startDate = new Date(startDateVal);
                var expiryDate = new Date(startDate);

                if (cycleVal === 'Monthly') {
                    expiryDate.setMonth(startDate.getMonth() + 1);
                } else if (cycleVal === 'Quarterly') {
                    expiryDate.setMonth(startDate.getMonth() + 3);
                } else if (cycleVal === 'Half Yearly') {
                    expiryDate.setMonth(startDate.getMonth() + 6);
                } else if (cycleVal === 'Yearly') {
                    expiryDate.setFullYear(startDate.getFullYear() + 1);
                } else if (cycleVal === 'One Time') {
                    // Default to 1 year for AMC/Website creation one-time billing cycles
                    expiryDate.setFullYear(startDate.getFullYear() + 1);
                }

                // Adjust by minus 1 day to make billing cycles inclusive (e.g. 2026-06-23 to 2027-06-22)
                expiryDate.setDate(expiryDate.getDate() - 1);

                var year = expiryDate.getFullYear();
                var month = String(expiryDate.getMonth() + 1).padStart(2, '0');
                var day = String(expiryDate.getDate()).padStart(2, '0');

                $('#expiry_date').val(year + '-' + month + '-' + day);
            }
        }
    });
</script>
@endsection

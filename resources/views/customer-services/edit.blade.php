@extends('layouts.app')

@section('title', 'Edit Service Assignment')
@section('page_title', 'Modify Customer Service Agreement')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-edit text-primary me-2"></i>Edit Service: {{ $customerService->service_name }}</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('customer-services.update', $customerService->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-select select2-enable" id="customer_id" name="customer_id" required>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $customerService->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="service_product_id" class="form-label">Catalog Product</label>
                            <select class="form-select select2-enable" id="service_product_id" name="service_product_id" required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('service_product_id', $customerService->service_product_id) == $product->id ? 'selected' : '' }}>
                                        [{{ $product->category->name ?? 'Other' }}] {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="service_name" class="form-label">Service Agreement Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('service_name') is-invalid @enderror" 
                                   id="service_name" name="service_name" value="{{ old('service_name', $customerService->service_name) }}" required>
                            @error('service_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $customerService->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                   id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $customerService->expiry_date->format('Y-m-d')) }}" required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                            <select class="form-select @error('billing_cycle') is-invalid @enderror" id="billing_cycle" name="billing_cycle" required>
                                <option value="Monthly" {{ old('billing_cycle', $customerService->billing_cycle) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="Quarterly" {{ old('billing_cycle', $customerService->billing_cycle) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="Half Yearly" {{ old('billing_cycle', $customerService->billing_cycle) == 'Half Yearly' ? 'selected' : '' }}>Half Yearly</option>
                                <option value="Yearly" {{ old('billing_cycle', $customerService->billing_cycle) == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="One Time" {{ old('billing_cycle', $customerService->billing_cycle) == 'One Time' ? 'selected' : '' }}>One Time</option>
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
                                       id="amount" name="amount" value="{{ old('amount', $customerService->amount) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Agreement Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Active" {{ old('status', $customerService->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Pending" {{ old('status', $customerService->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Expired" {{ old('status', $customerService->status) == 'Expired' ? 'selected' : '' }}>Expired</option>
                                <option value="Suspended" {{ old('status', $customerService->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Cancelled" {{ old('status', $customerService->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                <input class="form-check-input" type="checkbox" name="auto_renew" value="1" id="auto_renew" {{ old('auto_renew', $customerService->auto_renew) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-semibold" for="auto_renew">
                                    Auto Renew Enabled
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Scope / Remarks</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3">{{ old('remarks', $customerService->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('customer-services.show', $customerService->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Agreement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

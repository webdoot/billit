@extends('layouts.app')

@section('title', 'Add Product')
@section('page_title', 'Create Service Product')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-box-open text-primary me-2"></i>New Service Product Configuration</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('service-products.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="service_category_id" class="form-label">Service Category <span class="text-danger">*</span></label>
                            <select class="form-select select2-enable @error('service_category_id') is-invalid @enderror" 
                                    id="service_category_id" name="service_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. VPS Starter Core-1">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
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

                        <div class="col-md-6">
                            <label for="price" class="form-label">Standard Price (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" required placeholder="e.g. 1500.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="Specify technical specifications or service inclusions">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('service-products.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

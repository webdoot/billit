@extends('layouts.app')

@section('title', 'Renew Service')
@section('page_title', 'Renew Service Agreement')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-arrows-rotate text-primary me-2"></i>Renew Service: {{ $customerService->service_name }}</h5>
                <p class="text-secondary small mt-1 mb-0">For Customer: {{ $customerService->customer->company_name }}</p>
            </div>
            <div class="card-body px-4 pb-4">
                
                <div class="alert alert-warning border-0 mb-4" role="alert" style="border-left: 4px solid #f59e0b !important;">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Current Expiry: <strong>{{ $customerService->expiry_date->format('Y-m-d') }}</strong>. 
                    The renewed period will start on <strong>{{ $customerService->expiry_date->copy()->addDay()->format('Y-m-d') }}</strong>.
                </div>

                <form action="{{ route('customer-services.renew.post', $customerService->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="expiry_date" class="form-label">New Expiry Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                               id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $newExpiry->format('Y-m-d')) }}" required>
                        <div class="form-text">Choose the ending date for this renewal period.</div>
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Renewal Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $customerService->amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Pre-filled with the standard service amount. Can be adjusted.</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check pt-2">
                            <input type="hidden" name="generate_invoice" value="0">
                            <input class="form-check-input" type="checkbox" name="generate_invoice" value="1" id="generate_invoice" checked>
                            <label class="form-check-label text-dark fw-semibold" for="generate_invoice">
                                Auto-Generate Billing Invoice
                            </label>
                            <div class="form-text small text-secondary">If checked, a billing invoice and line items will be generated automatically for this renewal.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('customer-services.show', $customerService->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-warning">Confirm & Renew Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

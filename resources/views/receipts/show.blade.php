@extends('layouts.app')

@section('title', 'Receipt details')
@section('page_title', 'Payment Receipt')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-receipt text-primary me-2"></i>Receipt: {{ $receipt->receipt_no }}</h5>
                <a href="{{ route('receipts.pdf', $receipt->id) }}" class="btn btn-outline-success btn-sm">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
            <div class="card-body p-4">
                
                <div class="text-center mb-4 p-3 bg-light rounded">
                    <span class="text-secondary small text-uppercase fw-semibold d-block mb-1">Receipt Date</span>
                    <h5 class="fw-bold text-dark mb-3">{{ $receipt->receipt_date->format('Y-m-d') }}</h5>
                    <span class="text-secondary small text-uppercase fw-semibold d-block mb-1">Amount Collected</span>
                    <h2 class="fw-bold text-success m-0">₹{{ number_format($receipt->amount, 2) }}</h2>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">Customer / Client</span>
                        <h6 class="fw-bold text-dark"><a href="{{ route('customers.show', $receipt->payment->customer_id) }}" class="text-decoration-none">{{ $receipt->payment->customer->company_name }}</a></h6>
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-secondary small d-block mb-1">Billed Invoice</span>
                        <h6 class="fw-bold"><a href="{{ route('invoices.show', $receipt->payment->invoice_id) }}" class="text-decoration-none">{{ $receipt->payment->invoice->invoice_no }}</a></h6>
                    </div>
                    
                    <hr class="text-light-hover my-2">

                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">Payment Method</span>
                        <span class="badge bg-light text-dark border">{{ $receipt->payment->payment_method }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-secondary small d-block mb-1">Transaction Ref / No</span>
                        <code>{{ $receipt->payment->transaction_no ?? '-' }}</code>
                    </div>

                    @if($receipt->payment->remarks)
                        <div class="col-12 mt-3">
                            <span class="text-secondary small d-block mb-1">Transaction Remarks</span>
                            <p class="small text-dark mb-0 bg-light p-2 rounded border border-light-subtle">{{ $receipt->payment->remarks }}</p>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary me-2">All Receipts</a>
                    <a href="{{ route('invoices.show', $receipt->payment->invoice_id) }}" class="btn btn-primary">View Invoice</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

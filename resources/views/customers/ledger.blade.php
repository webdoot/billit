@extends('layouts.app')

@section('title', 'Customer Ledger')
@section('page_title', 'Customer Ledger Statement')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <!-- Ledger Summary Header -->
        <div class="card card-custom mb-4">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">{{ $customer->company_name }}</h5>
                    <p class="text-secondary small m-0">{{ $customer->customer_code }} | Account Statement</p>
                </div>
                <div class="d-flex mt-3 mt-sm-0">
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fa-solid fa-print me-1"></i> Print Statement
                    </button>
                    <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Statement Table -->
        <div class="card card-custom">
            <div class="card-body p-4">
                @php
                    $totalDebit = $ledger->sum('debit');
                    $totalCredit = $ledger->sum('credit');
                    $closingBalance = $ledger->last()['balance'] ?? 0.00;
                @endphp
                
                <!-- Ledger Metrics Row -->
                <div class="row mb-4 g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-secondary small text-uppercase fw-semibold d-block mb-1">Total Invoiced (Debits)</span>
                            <h4 class="fw-bold text-dark m-0">₹{{ number_format($totalDebit, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-secondary small text-uppercase fw-semibold d-block mb-1">Total Paid (Credits)</span>
                            <h4 class="fw-bold text-success m-0">₹{{ number_format($totalCredit, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded text-center {{ $closingBalance > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                            <span class="small text-uppercase fw-semibold d-block mb-1">Closing Balance (Outstanding)</span>
                            <h4 class="fw-bold m-0">₹{{ number_format($closingBalance, 2) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead>
                            <tr class="table-light">
                                <th>Date</th>
                                <th>Transaction Type</th>
                                <th>Reference</th>
                                <th class="text-end">Debit (₹)</th>
                                <th class="text-end">Credit (₹)</th>
                                <th class="text-end">Balance (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ledger as $item)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($item['date'])->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge {{ $item['type'] === 'Invoice' ? 'bg-light text-dark border' : 'bg-success-subtle text-success' }}">
                                            {{ $item['type'] }}
                                        </span>
                                        <span class="text-secondary small d-block mt-1">{{ $item['description'] }}</span>
                                    </td>
                                    <td>
                                        @if($item['type'] === 'Invoice')
                                            <strong>{{ $item['reference'] }}</strong>
                                        @else
                                            <code>{{ $item['reference'] }}</code>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-danger">
                                        {{ $item['debit'] > 0 ? '₹' . number_format($item['debit'], 2) : '-' }}
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        {{ $item['credit'] > 0 ? '₹' . number_format($item['credit'], 2) : '-' }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        ₹{{ number_format($item['balance'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">No ledger entries found for this customer account.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($ledger->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end text-danger">₹{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($totalCredit, 2) }}</td>
                                <td class="text-end text-dark">₹{{ number_format($closingBalance, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    @media print {
        .topbar, .sidebar, .btn, hr, .card-header-tabs {
            display: none !important;
        }
        .main-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
        .card-custom {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
@endsection

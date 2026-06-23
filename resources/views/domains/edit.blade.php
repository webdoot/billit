@extends('layouts.app')

@section('title', 'Edit Domain')
@section('page_title', 'Modify Domain Registry Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-globe text-primary me-2"></i>Modify Domain: {{ $domain->domain_name }}</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('domains.update', $domain->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden field to preserve service relation -->
                    <input type="hidden" name="customer_service_id" value="{{ $domain->customer_service_id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('domain_name') is-invalid @enderror" 
                                   id="domain_name" name="domain_name" value="{{ old('domain_name', $domain->domain_name) }}" required>
                            @error('domain_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="registrar" class="form-label">Registrar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('registrar') is-invalid @enderror" 
                                   id="registrar" name="registrar" value="{{ old('registrar', $domain->registrar) }}" required>
                            @error('registrar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="registrar_account" class="form-label">Registrar Account / Email</label>
                            <input type="text" class="form-control @error('registrar_account') is-invalid @enderror" 
                                   id="registrar_account" name="registrar_account" value="{{ old('registrar_account', $domain->registrar_account) }}">
                            @error('registrar_account')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="dns_provider" class="form-label">DNS Provider</label>
                            <input type="text" class="form-control @error('dns_provider') is-invalid @enderror" 
                                   id="dns_provider" name="dns_provider" value="{{ old('dns_provider', $domain->dns_provider) }}">
                            @error('dns_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                   id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $domain->purchase_date ? $domain->purchase_date->format('Y-m-d') : '') }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Domain Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                   id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $domain->expiry_date->format('Y-m-d')) }}" required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <div class="form-check pt-4">
                                <input type="hidden" name="auto_renew" value="0">
                                <input class="form-check-input" type="checkbox" name="auto_renew" value="1" id="auto_renew" {{ old('auto_renew', $domain->auto_renew) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-semibold" for="auto_renew">
                                    Auto Renew Enabled at Registrar
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Domain Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Active" {{ old('status', $domain->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Expired" {{ old('status', $domain->status) == 'Expired' ? 'selected' : '' }}>Expired</option>
                                <option value="Transferred" {{ old('status', $domain->status) == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="text-light-hover my-4">
                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-network-wired me-2"></i>Nameservers</h6>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="nameserver_1" class="form-label text-secondary small">Nameserver 1</label>
                            <input type="text" class="form-control" id="nameserver_1" name="nameserver_1" value="{{ old('nameserver_1', $domain->nameserver_1) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameserver_2" class="form-label text-secondary small">Nameserver 2</label>
                            <input type="text" class="form-control" id="nameserver_2" name="nameserver_2" value="{{ old('nameserver_2', $domain->nameserver_2) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="nameserver_3" class="form-label text-secondary small">Nameserver 3</label>
                            <input type="text" class="form-control" id="nameserver_3" name="nameserver_3" value="{{ old('nameserver_3', $domain->nameserver_3) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="nameserver_4" class="form-label text-secondary small">Nameserver 4</label>
                            <input type="text" class="form-control" id="nameserver_4" name="nameserver_4" value="{{ old('nameserver_4', $domain->nameserver_4) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('domains.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Specifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

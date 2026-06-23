@extends('layouts.app')

@section('title', 'Add Server')
@section('page_title', 'Add Server to Inventory')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-server text-primary me-2"></i>New Server Profile</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('servers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Server Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. AWS Production Mumbai">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="provider" class="form-label">Hosting Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('provider') is-invalid @enderror" 
                                   id="provider" name="provider" value="{{ old('provider') }}" required placeholder="e.g. AWS, DigitalOcean, Hetzner">
                            @error('provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hostname" class="form-label">Hostname / FQDN</label>
                            <input type="text" class="form-control @error('hostname') is-invalid @enderror" 
                                   id="hostname" name="hostname" value="{{ old('hostname') }}" placeholder="e.g. ns1.company.com">
                            @error('hostname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                                   id="ip_address" name="ip_address" value="{{ old('ip_address') }}" placeholder="e.g. 192.168.1.1">
                            @error('ip_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="location" class="form-label">Data Center Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Mumbai, India">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="monthly_cost" class="form-label">Monthly Cost (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('monthly_cost') is-invalid @enderror" 
                                       id="monthly_cost" name="monthly_cost" value="{{ old('monthly_cost', '0.00') }}" required>
                                @error('monthly_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="renewal_date" class="form-label">Server Renewal Date</label>
                            <input type="date" class="form-control @error('renewal_date') is-invalid @enderror" 
                                   id="renewal_date" name="renewal_date" value="{{ old('renewal_date') }}">
                            @error('renewal_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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

                    <div class="mb-4">
                        <label for="notes" class="form-label">Technical Notes / Specs</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Enter RAM, CPU, Storage, login info etc.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('servers.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Server</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

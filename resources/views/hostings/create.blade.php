@extends('layouts.app')

@section('title', 'Add Hosting')
@section('page_title', 'Configure Hosting Account')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-network-wired text-primary me-2"></i>New Hosting Specifications</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('hostings.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="customer_service_id" class="form-label">Customer Service Agreement <span class="text-danger">*</span></label>
                        <select class="form-select select2-enable @error('customer_service_id') is-invalid @enderror" 
                                id="customer_service_id" name="customer_service_id" required>
                            <option value="">Select Service Agreement</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('customer_service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->customer->company_name }} - {{ $service->service_name }} (Expires: {{ $service->expiry_date->format('Y-m-d') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Only shows active hosting customer services without existing hosting specifications.</div>
                        @error('customer_service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="server_id" class="form-label">Target Server</label>
                            <select class="form-select select2-enable @error('server_id') is-invalid @enderror" id="server_id" name="server_id">
                                <option value="">Select Server Instance</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
                                        {{ $server->name }} ({{ $server->ip_address }})
                                    </option>
                                @endforeach
                            </select>
                            @error('server_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hosting_type" class="form-label">Hosting Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('hosting_type') is-invalid @enderror" id="hosting_type" name="hosting_type" required>
                                <option value="">Select Type</option>
                                <option value="Shared" {{ old('hosting_type') == 'Shared' ? 'selected' : '' }}>Shared</option>
                                <option value="VPS" {{ old('hosting_type') == 'VPS' ? 'selected' : '' }}>VPS</option>
                                <option value="Dedicated" {{ old('hosting_type') == 'Dedicated' ? 'selected' : '' }}>Dedicated</option>
                                <option value="Cloud" {{ old('hosting_type') == 'Cloud' ? 'selected' : '' }}>Cloud</option>
                            </select>
                            @error('hosting_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="control_panel" class="form-label">Control Panel</label>
                            <input type="text" class="form-control @error('control_panel') is-invalid @enderror" 
                                   id="control_panel" name="control_panel" value="{{ old('control_panel') }}" placeholder="e.g. cPanel, Plesk, CyberPanel">
                            @error('control_panel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Hosting Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" placeholder="e.g. admin_wp">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="disk_limit" class="form-label">Disk Limit</label>
                            <input type="text" class="form-control @error('disk_limit') is-invalid @enderror" 
                                   id="disk_limit" name="disk_limit" value="{{ old('disk_limit') }}" placeholder="e.g. 20 GB, Unlimited">
                            @error('disk_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="bandwidth_limit" class="form-label">Bandwidth Limit</label>
                            <input type="text" class="form-control @error('bandwidth_limit') is-invalid @enderror" 
                                   id="bandwidth_limit" name="bandwidth_limit" value="{{ old('bandwidth_limit') }}" placeholder="e.g. 100 GB, 1 TB, Unlimited">
                            @error('bandwidth_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Hosting Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Suspended" {{ old('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('hostings.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Specifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

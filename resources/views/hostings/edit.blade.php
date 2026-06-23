@extends('layouts.app')

@section('title', 'Edit Hosting')
@section('page_title', 'Modify Hosting specifications')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-network-wired text-primary me-2"></i>Modify Hosting: {{ $hosting->customerService->customer->company_name ?? 'N/A' }} - {{ $hosting->customerService->service_name ?? 'N/A' }}</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('hostings.update', $hosting->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden field to preserve service relation -->
                    <input type="hidden" name="customer_service_id" value="{{ $hosting->customer_service_id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="server_id" class="form-label">Target Server</label>
                            <select class="form-select select2-enable @error('server_id') is-invalid @enderror" id="server_id" name="server_id">
                                <option value="">Select Server Instance</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" {{ old('server_id', $hosting->server_id) == $server->id ? 'selected' : '' }}>
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
                                <option value="Shared" {{ old('hosting_type', $hosting->hosting_type) == 'Shared' ? 'selected' : '' }}>Shared</option>
                                <option value="VPS" {{ old('hosting_type', $hosting->hosting_type) == 'VPS' ? 'selected' : '' }}>VPS</option>
                                <option value="Dedicated" {{ old('hosting_type', $hosting->hosting_type) == 'Dedicated' ? 'selected' : '' }}>Dedicated</option>
                                <option value="Cloud" {{ old('hosting_type', $hosting->hosting_type) == 'Cloud' ? 'selected' : '' }}>Cloud</option>
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
                                   id="control_panel" name="control_panel" value="{{ old('control_panel', $hosting->control_panel) }}">
                            @error('control_panel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Hosting Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $hosting->username) }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="disk_limit" class="form-label">Disk Limit</label>
                            <input type="text" class="form-control @error('disk_limit') is-invalid @enderror" 
                                   id="disk_limit" name="disk_limit" value="{{ old('disk_limit', $hosting->disk_limit) }}">
                            @error('disk_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="bandwidth_limit" class="form-label">Bandwidth Limit</label>
                            <input type="text" class="form-control @error('bandwidth_limit') is-invalid @enderror" 
                                   id="bandwidth_limit" name="bandwidth_limit" value="{{ old('bandwidth_limit', $hosting->bandwidth_limit) }}">
                            @error('bandwidth_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Hosting Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Active" {{ old('status', $hosting->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Suspended" {{ old('status', $hosting->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Inactive" {{ old('status', $hosting->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('hostings.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Specifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

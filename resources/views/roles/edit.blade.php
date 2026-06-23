@extends('layouts.app')

@section('title', 'Edit Role')
@section('page_title', 'Modify User Role')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-user-shield text-primary me-2"></i>Edit Role: {{ $role->name }}</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $role->name) }}" 
                               {{ $role->name === 'Super Admin' ? 'readonly' : '' }} required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($role->name === 'Super Admin')
                            <div class="form-text text-danger">The Super Admin role name cannot be modified.</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block mb-3">Modify Permissions</label>
                        
                        @foreach($groupedPermissions as $groupName => $perms)
                            <div class="card border border-light-subtle shadow-none bg-light-subtle mb-3" style="border-radius: 8px;">
                                <div class="card-header bg-transparent border-0 pb-1 pt-3 px-3">
                                    <h6 class="fw-bold m-0 text-secondary" style="font-size: 0.85rem; letter-spacing: 0.5px; text-transform: uppercase;">
                                        {{ $groupName }} Management
                                    </h6>
                                </div>
                                <div class="card-body pt-2 px-3 pb-3">
                                    <div class="row">
                                        @foreach($perms as $permission)
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                           value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                           {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                           {{ $role->name === 'Super Admin' ? 'disabled' : '' }}>
                                                    <label class="form-check-label text-dark fs-7" for="perm_{{ $permission->id }}" style="font-size: 0.9rem;">
                                                        {{ ucwords(str_replace(['.', '_', '-'], ' ', str_replace(strtolower($groupName) . '.', '', $permission->name))) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($role->name === 'Super Admin')
                            <div class="form-text text-danger">Super Admin permissions cannot be modified (they bypass authorization automatically).</div>
                            {{-- Add hidden inputs to preserve permissions on post if form is submitted for Super Admin --}}
                            @foreach($rolePermissions as $pName)
                                <input type="hidden" name="permissions[]" value="{{ $pName }}">
                            @endforeach
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

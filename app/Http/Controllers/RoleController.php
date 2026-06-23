<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $this->authorize('roles.view');

        if ($request->ajax()) {
            $query = Role::with('permissions');
            return DataTables::of($query)
                ->addColumn('permissions_count', function ($row) {
                    return '<span class="badge bg-info badge-role">' . $row->permissions->count() . ' permissions</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('roles.edit')) {
                        $btn .= '<a href="' . route('roles.edit', $row->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fa-regular fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('roles.delete')) {
                        // Prevent deleting core roles
                        if (!in_array($row->name, ['Super Admin', 'Accounts', 'Support Staff'])) {
                            $btn .= '<form action="' . route('roles.destroy', $row->id) . '" method="POST" class="d-inline delete-form">';
                            $btn .= csrf_field() . method_field('DELETE');
                            $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="fa-regular fa-trash-can"></i></button>';
                            $btn .= '</form>';
                        }
                    }
                    return $btn;
                })
                ->rawColumns(['permissions_count', 'action'])
                ->make(true);
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $this->authorize('roles.create');

        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web'
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $role->name . ' created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $this->authorize('roles.edit');

        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();

        if (in_array($role->name, ['Super Admin']) && $data['name'] !== 'Super Admin') {
            return redirect()->route('roles.index')
                ->with('error', 'The Super Admin role name cannot be changed.');
        }

        $role->update([
            'name' => $data['name']
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $role->name . ' updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('roles.delete');

        if (in_array($role->name, ['Super Admin', 'Accounts', 'Support Staff'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Core system roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Helper to group permissions by their prefix.
     */
    private function groupPermissions($permissions): array
    {
        $grouped = [];
        foreach ($permissions as $p) {
            $parts = explode('.', $p->name);
            $group = count($parts) > 1 ? ucfirst($parts[0]) : 'General';
            $grouped[$group][] = $p;
        }
        return $grouped;
    }
}

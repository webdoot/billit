<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected UserRepositoryInterface $userRepo;
    protected RoleRepositoryInterface $roleRepo;

    public function __construct(
        UserRepositoryInterface $userRepo,
        RoleRepositoryInterface $roleRepo
    ) {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->authorize('users.view');

        if ($request->ajax()) {
            $query = User::with('roles');
            return DataTables::of($query)
                ->addColumn('roles_list', function ($row) {
                    return $row->roles->pluck('name')->map(function ($role) {
                        $badgeClass = 'bg-secondary';
                        if ($role === 'Super Admin') $badgeClass = 'bg-danger';
                        if ($role === 'Accounts') $badgeClass = 'bg-success';
                        if ($role === 'Support Staff') $badgeClass = 'bg-primary';
                        return '<span class="badge ' . $badgeClass . ' badge-role me-1">' . htmlspecialchars($role) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('users.edit')) {
                        $btn .= '<a href="' . route('users.edit', $row->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fa-regular fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('users.delete')) {
                        // Prevent deleting self or primary admin
                        if ($row->id !== auth()->id() && $row->email !== 'admin@company.com') {
                            $btn .= '<form action="' . route('users.destroy', $row->id) . '" method="POST" class="d-inline delete-form">';
                            $btn .= csrf_field() . method_field('DELETE');
                            $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="fa-regular fa-trash-can"></i></button>';
                            $btn .= '</form>';
                        }
                    }
                    return $btn;
                })
                ->rawColumns(['roles_list', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->authorize('users.create');
        $roles = $this->roleRepo->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        /** @var User $user */
        $user = $this->userRepo->create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User ' . $user->name . ' created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorize('users.edit');
        $roles = $this->roleRepo->all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepo->update($user->id, $data);

        // Fetch user from repo or directly since we need Spatie's syncRoles method
        $updatedUser = User::findOrFail($user->id);
        if (isset($data['roles'])) {
            $updatedUser->syncRoles($data['roles']);
        } else {
            $updatedUser->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User profile updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('users.delete');

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        if ($user->email === 'admin@company.com') {
            return redirect()->route('users.index')
                ->with('error', 'The primary administrator cannot be deleted.');
        }

        $this->userRepo->delete($user->id);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // This seeds the roles, permissions and default users
        $this->seed();
    }

    /**
     * Guest user cannot access management pages.
     */
    public function test_guest_cannot_access_user_and_role_management()
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
        $this->get(route('roles.index'))->assertRedirect(route('login'));
    }

    /**
     * Non-admin users are forbidden from accessing user management.
     */
    public function test_non_admin_cannot_access_user_management()
    {
        // Support user is seeded by seeder
        $supportUser = User::where('email', 'support@company.com')->first();
        $this->actingAs($supportUser);

        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('users.create'))->assertStatus(403);
        $this->get(route('roles.index'))->assertStatus(403);
    }

    /**
     * Super Admin has access to user and role directory index.
     */
    public function test_super_admin_can_access_user_and_role_management()
    {
        $adminUser = User::where('email', 'admin@company.com')->first();
        $this->actingAs($adminUser);

        $this->get(route('users.index'))->assertStatus(200);
        $this->get(route('roles.index'))->assertStatus(200);
    }

    /**
     * Super Admin can create a new user and assign roles.
     */
    public function test_super_admin_can_create_user_with_roles()
    {
        $adminUser = User::where('email', 'admin@company.com')->first();
        $this->actingAs($adminUser);

        $response = $this->post(route('users.store'), [
            'name' => 'New Staff User',
            'email' => 'newstaff@company.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'roles' => ['Support Staff']
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New Staff User',
            'email' => 'newstaff@company.com',
        ]);

        $newUser = User::where('email', 'newstaff@company.com')->first();
        $this->assertTrue($newUser->hasRole('Support Staff'));
    }

    /**
     * Super Admin can update an existing user.
     */
    public function test_super_admin_can_update_user_roles()
    {
        $adminUser = User::where('email', 'admin@company.com')->first();
        $this->actingAs($adminUser);

        $supportUser = User::where('email', 'support@company.com')->first();

        $response = $this->put(route('users.update', $supportUser->id), [
            'name' => 'Updated Support User',
            'email' => 'support_updated@company.com',
            'roles' => ['Accounts']
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $supportUser->id,
            'name' => 'Updated Support User',
            'email' => 'support_updated@company.com',
        ]);

        $supportUser->refresh();
        $this->assertTrue($supportUser->hasRole('Accounts'));
        $this->assertFalse($supportUser->hasRole('Support Staff'));
    }

    /**
     * Super Admin can create roles with permissions.
     */
    public function test_super_admin_can_create_role_with_permissions()
    {
        $adminUser = User::where('email', 'admin@company.com')->first();
        $this->actingAs($adminUser);

        $response = $this->post(route('roles.store'), [
            'name' => 'Compliance Manager',
            'permissions' => ['customers.view', 'reports.view']
        ]);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'Compliance Manager',
        ]);

        $role = Role::findByName('Compliance Manager');
        $this->assertTrue($role->hasPermissionTo('customers.view'));
        $this->assertTrue($role->hasPermissionTo('reports.view'));
    }

    /**
     * Super Admin cannot delete themselves.
     */
    public function test_super_admin_cannot_delete_self()
    {
        $adminUser = User::where('email', 'admin@company.com')->first();
        $this->actingAs($adminUser);

        $response = $this->delete(route('users.destroy', $adminUser->id));
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $adminUser->id
        ]);
    }
}

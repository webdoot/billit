<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_access_everything()
    {
        $user = User::where('email', 'admin@company.com')->first();
        $this->actingAs($user);

        // Can access reports
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);

        // Can access servers
        $response = $this->get(route('servers.index'));
        $response->assertStatus(200);
    }

    public function test_accounts_user_can_access_reports_but_not_servers()
    {
        $user = User::where('email', 'accounts@company.com')->first();
        $this->actingAs($user);

        // Can access reports
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);

        // Cannot access servers
        $response = $this->get(route('servers.index'));
        $response->assertStatus(403);
    }

    public function test_support_user_can_access_servers_but_not_reports()
    {
        $user = User::where('email', 'support@company.com')->first();
        $this->actingAs($user);

        // Can access servers
        $response = $this->get(route('servers.index'));
        $response->assertStatus(200);

        // Cannot access reports
        $response = $this->get(route('reports.index'));
        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ExpiryAlert;
use App\Models\ServiceCategory;
use App\Models\ServiceProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpiryCheckCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_console_command_marks_expired_services()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $customer = Customer::create([
            'customer_code' => 'CUST-00001',
            'company_name' => 'Acme Corp',
            'contact_person' => 'John Doe',
            'email' => 'john@acme.com',
            'mobile' => '9876543210',
            'status' => 'Active',
        ]);

        $category = ServiceCategory::create([
            'name' => 'Web Hosting',
            'status' => 'Active',
        ]);

        $product = ServiceProduct::create([
            'service_category_id' => $category->id,
            'name' => 'Shared Hosting Basic',
            'billing_cycle' => 'Yearly',
            'price' => 1000.00,
            'status' => 'Active',
        ]);

        // Service that expired yesterday
        $expiredService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Expired Service',
            'start_date' => Carbon::today()->subYear()->subDay(),
            'expiry_date' => Carbon::today()->subDay(),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => false,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        // Service that is still active
        $activeService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Active Service',
            'start_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addMonths(6),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => false,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        // Act: Run the Artisan check-expiry command
        $this->artisan('services:check-expiry')
            ->expectsOutput('Starting service expiry and alert generation check...')
            ->expectsOutput('Done! Expired services marked: 1')
            ->assertExitCode(0);

        // Assert: Expired service has status 'Expired'
        $this->assertEquals('Expired', $expiredService->fresh()->status);
        // Active service remains 'Active'
        $this->assertEquals('Active', $activeService->fresh()->status);
    }

    public function test_console_command_creates_alerts_on_exact_milestones()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $customer = Customer::create([
            'customer_code' => 'CUST-00001',
            'company_name' => 'Acme Corp',
            'contact_person' => 'John Doe',
            'email' => 'john@acme.com',
            'mobile' => '9876543210',
            'status' => 'Active',
        ]);

        $category = ServiceCategory::create([
            'name' => 'Web Hosting',
            'status' => 'Active',
        ]);

        $product = ServiceProduct::create([
            'service_category_id' => $category->id,
            'name' => 'Shared Hosting Basic',
            'billing_cycle' => 'Yearly',
            'price' => 1000.00,
            'status' => 'Active',
        ]);

        // Service expiring in exactly 30 days
        $service30 = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Expiring in 30 Days',
            'start_date' => Carbon::today()->subMonths(11),
            'expiry_date' => Carbon::today()->addDays(30),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => false,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        // Service expiring in exactly 15 days
        $service15 = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Expiring in 15 Days',
            'start_date' => Carbon::today()->subMonths(11),
            'expiry_date' => Carbon::today()->addDays(15),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => false,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        // Service expiring in 40 days (no milestone alert)
        $service40 = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Expiring in 40 Days',
            'start_date' => Carbon::today()->subMonths(11),
            'expiry_date' => Carbon::today()->addDays(40),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => false,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        // Act: Run the command
        $this->artisan('services:check-expiry')
            ->assertExitCode(0);

        // Assert: ExpiryAlerts generated for 30 and 15 days
        $this->assertDatabaseHas('expiry_alerts', [
            'customer_service_id' => $service30->id,
            'days_before' => 30,
        ]);

        $this->assertDatabaseHas('expiry_alerts', [
            'customer_service_id' => $service15->id,
            'days_before' => 15,
        ]);

        // No alert for 40 days
        $this->assertDatabaseMissing('expiry_alerts', [
            'customer_service_id' => $service40->id,
        ]);

        // Act again: Run command again, it shouldn't duplicate the alerts
        $this->artisan('services:check-expiry')
            ->expectsOutput('Done! Expired services marked: 0')
            ->expectsOutput('New expiry alerts created: 0')
            ->assertExitCode(0);
    }
}

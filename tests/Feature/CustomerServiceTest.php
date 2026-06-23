<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServiceCategory;
use App\Models\ServiceProduct;
use App\Models\User;
use App\Services\RenewalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database to create roles & permissions
        $this->seed();
    }

    public function test_service_registration_and_expiry_calculations()
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
            'price' => 5000.00,
            'status' => 'Active',
        ]);

        $startDate = Carbon::today();
        $expiryDate = Carbon::today()->addYear();
        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Acme Hosting',
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'billing_cycle' => 'Yearly',
            'amount' => 5000.00,
            'auto_renew' => true,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('customer_services', [
            'id' => $customerService->id,
            'service_name' => 'Acme Hosting',
            'status' => 'Active',
        ]);

        $this->assertTrue($customerService->expiry_date->isSameDay($expiryDate));
    }

    public function test_renewal_service_trigger()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        $this->actingAs($user);

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
            'price' => 5000.00,
            'status' => 'Active',
        ]);

        $startDate = Carbon::today()->subYear();
        $expiryDate = Carbon::today();
        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Acme Hosting',
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'billing_cycle' => 'Yearly',
            'amount' => 5000.00,
            'auto_renew' => true,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        $renewalService = app(RenewalService::class);
        $newExpiry = Carbon::today()->addYear();
        $renewalAmount = 5500.00;

        $renewal = $renewalService->renewService($customerService, $newExpiry, $renewalAmount, false);

        $this->assertDatabaseHas('renewals', [
            'id' => $renewal->id,
            'customer_service_id' => $customerService->id,
            'amount' => 5500.00,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('customer_services', [
            'id' => $customerService->id,
            'amount' => 5500.00,
            'status' => 'Active',
        ]);

        $expectedStartDate = $expiryDate->copy()->addDay();
        $this->assertTrue($customerService->fresh()->start_date->isSameDay($expectedStartDate));
        $this->assertTrue($customerService->fresh()->expiry_date->isSameDay($newExpiry));
    }
}

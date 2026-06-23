<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\ServiceCategory;
use App\Models\ServiceProduct;
use App\Models\User;
use App\Services\BillingService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceBillingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_invoice_generation_sums_and_taxes()
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

        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Acme Hosting',
            'start_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addYear(),
            'billing_cycle' => 'Yearly',
            'amount' => 5000.00,
            'auto_renew' => true,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        $billingService = app(BillingService::class);
        
        // Act: Generate Invoice (18% tax)
        $invoice = $billingService->createInvoiceFromService($customerService, 0.00, 18.00);

        // Assert: $5000 subtotal, 18% tax of 5000 = 900. Total = 5900.
        $this->assertEquals(5000.00, $invoice->subtotal);
        $this->assertEquals(900.00, $invoice->tax);
        $this->assertEquals(5900.00, $invoice->total);
        $this->assertEquals(5900.00, $invoice->balance);
        $this->assertEquals('Sent', $invoice->status);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'customer_service_id' => $customerService->id,
            'amount' => 5000.00,
        ]);
    }

    public function test_invoice_payments_partial_and_full()
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
            'price' => 1000.00,
            'status' => 'Active',
        ]);

        $customerService = CustomerService::create([
            'customer_id' => $customer->id,
            'service_product_id' => $product->id,
            'service_name' => 'Acme Hosting',
            'start_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addYear(),
            'billing_cycle' => 'Yearly',
            'amount' => 1000.00,
            'auto_renew' => true,
            'status' => 'Active',
            'created_by' => $user->id,
        ]);

        $billingService = app(BillingService::class);
        $paymentService = app(PaymentService::class);

        // Subtotal = 1000, Tax = 180, Total = 1180
        $invoice = $billingService->createInvoiceFromService($customerService, 0.00, 18.00);

        // Act: Make a partial payment of 500
        $payment1 = $paymentService->recordPayment($invoice, 500.00, 'UPI', 'TXN001', 'Partial Payment');

        // Assert: balance is 1180 - 500 = 680, status is Partial
        $this->assertEquals(680.00, $invoice->fresh()->balance);
        $this->assertEquals('Partial', $invoice->fresh()->status);

        // Assert receipt created for partial payment
        $this->assertDatabaseHas('receipts', [
            'payment_id' => $payment1->id,
            'amount' => 500.00,
        ]);

        // Act: Make remaining payment of 680
        $payment2 = $paymentService->recordPayment($invoice->fresh(), 680.00, 'Bank Transfer', 'TXN002', 'Final Payment');

        // Assert: balance is 0, status is Paid
        $this->assertEquals(0.00, $invoice->fresh()->balance);
        $this->assertEquals('Paid', $invoice->fresh()->status);

        // Assert receipt created for final payment
        $this->assertDatabaseHas('receipts', [
            'payment_id' => $payment2->id,
            'amount' => 680.00,
        ]);
    }
}

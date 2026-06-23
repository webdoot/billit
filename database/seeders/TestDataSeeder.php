<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Server;
use App\Models\ServiceCategory;
use App\Models\ServiceProduct;
use App\Services\BillingService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billingService = app(BillingService::class);
        $paymentService = app(PaymentService::class);

        // 1. Create Service Products for each category
        $categories = ServiceCategory::all()->keyBy('name');

        $productsData = [
            'Domain' => [
                ['name' => '.com Domain Registration', 'billing_cycle' => 'Yearly', 'price' => 999.00],
                ['name' => '.in Domain Registration', 'billing_cycle' => 'Yearly', 'price' => 699.00],
                ['name' => '.org Domain Registration', 'billing_cycle' => 'Yearly', 'price' => 1199.00],
            ],
            'Hosting' => [
                ['name' => 'Basic Shared Hosting', 'billing_cycle' => 'Yearly', 'price' => 2999.00],
                ['name' => 'Premium Shared Hosting', 'billing_cycle' => 'Yearly', 'price' => 5999.00],
                ['name' => 'Reseller Hosting Basic', 'billing_cycle' => 'Yearly', 'price' => 12999.00],
            ],
            'Server' => [
                ['name' => 'VPS Starter Server', 'billing_cycle' => 'Monthly', 'price' => 1500.00],
                ['name' => 'Dedicated Power Server', 'billing_cycle' => 'Monthly', 'price' => 9500.00],
            ],
            'SSL' => [
                ['name' => 'PositiveSSL Certificate', 'billing_cycle' => 'Yearly', 'price' => 1499.00],
                ['name' => 'Wildcard SSL Certificate', 'billing_cycle' => 'Yearly', 'price' => 4999.00],
            ],
            'Website' => [
                ['name' => 'Business Website Development', 'billing_cycle' => 'One Time', 'price' => 25000.00],
            ],
            'Application' => [
                ['name' => 'Custom SaaS Web App Development', 'billing_cycle' => 'One Time', 'price' => 150000.00],
            ],
            'Maintenance' => [
                ['name' => 'Website AMC Annual Support', 'billing_cycle' => 'Yearly', 'price' => 12000.00],
                ['name' => 'Server AMC Maintenance Support', 'billing_cycle' => 'Yearly', 'price' => 24000.00],
            ],
            'Other' => [
                ['name' => 'Database Optimization Consult', 'billing_cycle' => 'One Time', 'price' => 8000.00],
            ],
        ];

        foreach ($productsData as $catName => $products) {
            $cat = $categories->get($catName);
            if ($cat) {
                foreach ($products as $prod) {
                    ServiceProduct::updateOrCreate(
                        ['name' => $prod['name']],
                        [
                            'service_category_id' => $cat->id,
                            'billing_cycle' => $prod['billing_cycle'],
                            'price' => $prod['price'],
                            'description' => $prod['name'] . ' Package description.',
                            'status' => 'Active',
                        ]
                    );
                }
            }
        }

        // 2. Create Servers
        $servers = [
            [
                'name' => 'AWS-EC2-Mumbai',
                'provider' => 'Amazon Web Services',
                'hostname' => 'ec2.ap-south-1.amazonaws.com',
                'ip_address' => '13.233.15.42',
                'location' => 'Mumbai, India',
                'monthly_cost' => 2500.00,
                'renewal_date' => Carbon::today()->addMonths(6),
                'notes' => 'Primary AWS production instance.',
                'status' => 'Active',
            ],
            [
                'name' => 'DigitalOcean-BLR1',
                'provider' => 'DigitalOcean',
                'hostname' => 'blr1.droplet.digitalocean.com',
                'ip_address' => '104.248.88.92',
                'location' => 'Bangalore, India',
                'monthly_cost' => 1200.00,
                'renewal_date' => Carbon::today()->addMonths(3),
                'notes' => 'DO droplet for hosting reseller accounts.',
                'status' => 'Active',
            ],
            [
                'name' => 'Hetzner-DE',
                'provider' => 'Hetzner Cloud',
                'hostname' => 'de.hetzner.cloud',
                'ip_address' => '95.217.33.104',
                'location' => 'Falkenstein, Germany',
                'monthly_cost' => 3800.00,
                'renewal_date' => Carbon::today()->addMonths(1),
                'notes' => 'Hetzner dedicated server for custom application deployments.',
                'status' => 'Active',
            ],
        ];

        $serverModels = [];
        foreach ($servers as $srv) {
            $serverModels[] = Server::updateOrCreate(['ip_address' => $srv['ip_address']], $srv);
        }

        // 3. Create 10 Customers
        $customersData = [
            [
                'customer_code' => 'CUST-00001',
                'company_name' => 'TechVantage Solutions',
                'contact_person' => 'Amit Sharma',
                'email' => 'amit@techvantage.in',
                'mobile' => '9812345678',
                'address' => '101, Signature Towers, Sector 30',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'country' => 'India',
                'pin_code' => '122001',
                'website' => 'https://techvantage.in',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00002',
                'company_name' => 'Nexus Retail Group',
                'contact_person' => 'Priya Patel',
                'email' => 'priya@nexusretail.com',
                'mobile' => '9823456789',
                'address' => '402, Trade Center, Bandra Kurla Complex',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'pin_code' => '400051',
                'website' => 'https://nexusretail.com',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00003',
                'company_name' => 'Apex Healthcare Services',
                'contact_person' => 'Dr. Rajesh Kumar',
                'email' => 'admin@apexhealth.org',
                'mobile' => '9834567890',
                'address' => '12, G.N. Chetty Road, T. Nagar',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'country' => 'India',
                'pin_code' => '600017',
                'website' => 'https://apexhealth.org',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00004',
                'company_name' => 'Blue Sky Logistics',
                'contact_person' => 'Sandeep Singh',
                'email' => 'sandeep@blueskylogistics.com',
                'mobile' => '9845678901',
                'address' => 'Plot 45, Phase 1, Industrial Area',
                'city' => 'Chandigarh',
                'state' => 'Punjab',
                'country' => 'India',
                'pin_code' => '160002',
                'website' => 'https://blueskylogistics.com',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00005',
                'company_name' => 'Vanguard EduTech',
                'contact_person' => 'Neha Gupta',
                'email' => 'neha@vanguard.edu.in',
                'mobile' => '9856789012',
                'address' => '5A, Camac Street, Circular Road',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'pin_code' => '700016',
                'website' => 'https://vanguard.edu.in',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00006',
                'company_name' => 'Green Fields Agriculture',
                'contact_person' => 'Vijay Rao',
                'email' => 'vijay@greenfields.co.in',
                'mobile' => '9867890123',
                'address' => '8-2-293/82, Jubilee Hills',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'country' => 'India',
                'pin_code' => '500033',
                'website' => 'https://greenfields.co.in',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00007',
                'company_name' => 'Saffron Hospitality',
                'contact_person' => 'Vikram Mehta',
                'email' => 'vikram@saffronhotels.com',
                'mobile' => '9878901234',
                'address' => 'Hotel Saffron, MG Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country' => 'India',
                'pin_code' => '560001',
                'website' => 'https://saffronhotels.com',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00008',
                'company_name' => 'Zenix Media Works',
                'contact_person' => 'Rohan Sen',
                'email' => 'rohan@zenixmedia.com',
                'mobile' => '9889012345',
                'address' => 'Building B, Film City, Noida Sector 16A',
                'city' => 'Noida',
                'state' => 'Uttar Pradesh',
                'country' => 'India',
                'pin_code' => '201301',
                'website' => 'https://zenixmedia.com',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00009',
                'company_name' => 'Infinity Finance Advisors',
                'contact_person' => 'Meera Nair',
                'email' => 'meera@infinityfinance.in',
                'mobile' => '9890123456',
                'address' => '202, Skyline Towers, K.G. Road',
                'city' => 'Ernakulam',
                'state' => 'Kerala',
                'country' => 'India',
                'pin_code' => '682011',
                'website' => 'https://infinityfinance.in',
                'status' => 'Active',
            ],
            [
                'customer_code' => 'CUST-00010',
                'company_name' => 'ProFit Gymnasiums',
                'contact_person' => 'Karan Malhotra',
                'email' => 'karan@profitgyms.com',
                'mobile' => '9901234567',
                'address' => '78, Ring Road, Lajpat Nagar III',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'pin_code' => '110024',
                'website' => 'https://profitgyms.com',
                'status' => 'Active',
            ],
        ];

        $customerModels = [];
        foreach ($customersData as $cust) {
            $customerModels[] = Customer::updateOrCreate(['customer_code' => $cust['customer_code']], $cust);
        }

        // 4. Create Customer Services Configuration
        $domainProducts = ServiceProduct::whereHas('category', fn($q) => $q->where('name', 'Domain'))->get();
        $hostingProducts = ServiceProduct::whereHas('category', fn($q) => $q->where('name', 'Hosting'))->get();
        $serverProducts = ServiceProduct::whereHas('category', fn($q) => $q->where('name', 'Server'))->get();
        $otherProducts = ServiceProduct::whereHas('category', fn($q) => $q->whereNotIn('name', ['Domain', 'Hosting', 'Server']))->get();

        $servicesConfig = [
            // Customer 1: Active Domain + Active Hosting
            [
                'customer_idx' => 0, 'product' => $domainProducts->get(0), 'name' => 'techvantage.in Domain',
                'start_offset' => -300, 'expiry_offset' => 65, 'billing_cycle' => 'Yearly', 'amount' => 699.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Domain', 'domain_name' => 'techvantage.in', 'registrar' => 'GoDaddy'
            ],
            [
                'customer_idx' => 0, 'product' => $hostingProducts->get(1), 'name' => 'Premium Hosting Plan - techvantage.in',
                'start_offset' => -300, 'expiry_offset' => 65, 'billing_cycle' => 'Yearly', 'amount' => 5999.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Hosting', 'server' => $serverModels[0], 'type_details' => 'Shared'
            ],
            // Customer 2: Active Server + Active Domain
            [
                'customer_idx' => 1, 'product' => $serverProducts->get(0), 'name' => 'VPS Starter - Development',
                'start_offset' => -15, 'expiry_offset' => 15, 'billing_cycle' => 'Monthly', 'amount' => 1500.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Server'
            ],
            [
                'customer_idx' => 1, 'product' => $domainProducts->get(0), 'name' => 'nexusretail.com Domain',
                'start_offset' => -350, 'expiry_offset' => 15, 'billing_cycle' => 'Yearly', 'amount' => 999.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Domain', 'domain_name' => 'nexusretail.com', 'registrar' => 'Namecheap'
            ],
            // Customer 3: Active Website (One-Time)
            [
                'customer_idx' => 2, 'product' => $otherProducts->where('name', 'Business Website Development')->first(), 'name' => 'Corporate Portal Dev',
                'start_offset' => -45, 'expiry_offset' => 320, 'billing_cycle' => 'One Time', 'amount' => 25000.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Other'
            ],
            // Customer 4: Expiring Maintenance AMC (expiring in 5 days) + SSL
            [
                'customer_idx' => 3, 'product' => $otherProducts->where('name', 'Website AMC Annual Support')->first(), 'name' => 'Blue Sky Website AMC',
                'start_offset' => -360, 'expiry_offset' => 5, 'billing_cycle' => 'Yearly', 'amount' => 12000.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Other'
            ],
            [
                'customer_idx' => 3, 'product' => $otherProducts->where('name', 'PositiveSSL Certificate')->first(), 'name' => 'SSL for blueskylogistics.com',
                'start_offset' => -360, 'expiry_offset' => 5, 'billing_cycle' => 'Yearly', 'amount' => 1499.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Other'
            ],
            // Customer 5: Expired Domain + Expired Hosting (start date offsets set past expiry)
            [
                'customer_idx' => 4, 'product' => $domainProducts->get(2), 'name' => 'vanguard.edu.in Domain',
                'start_offset' => -375, 'expiry_offset' => -10, 'billing_cycle' => 'Yearly', 'amount' => 1199.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Domain', 'domain_name' => 'vanguard.edu.in', 'registrar' => 'PDR'
            ],
            [
                'customer_idx' => 4, 'product' => $hostingProducts->get(0), 'name' => 'Basic Hosting - vanguard.edu.in',
                'start_offset' => -375, 'expiry_offset' => -10, 'billing_cycle' => 'Yearly', 'amount' => 2999.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Hosting', 'server' => $serverModels[1], 'type_details' => 'Reseller'
            ],
            // Customer 6: Active Hosting
            [
                'customer_idx' => 5, 'product' => $hostingProducts->get(0), 'name' => 'Basic Hosting - greenfields.co.in',
                'start_offset' => -100, 'expiry_offset' => 265, 'billing_cycle' => 'Yearly', 'amount' => 2999.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Hosting', 'server' => $serverModels[1], 'type_details' => 'Shared'
            ],
            // Customer 7: Active Domain + Active Hosting
            [
                'customer_idx' => 6, 'product' => $domainProducts->get(0), 'name' => 'saffronhotels.com Domain',
                'start_offset' => -180, 'expiry_offset' => 185, 'billing_cycle' => 'Yearly', 'amount' => 999.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Domain', 'domain_name' => 'saffronhotels.com', 'registrar' => 'GoDaddy'
            ],
            [
                'customer_idx' => 6, 'product' => $hostingProducts->get(2), 'name' => 'Reseller Hosting - saffronhotels.com',
                'start_offset' => -180, 'expiry_offset' => 185, 'billing_cycle' => 'Yearly', 'amount' => 12999.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Hosting', 'server' => $serverModels[2], 'type_details' => 'Reseller'
            ],
            // Customer 8: Active Server
            [
                'customer_idx' => 7, 'product' => $serverProducts->get(1), 'name' => 'Dedicated Server-DE for Video Render',
                'start_offset' => -90, 'expiry_offset' => 210, 'billing_cycle' => 'Monthly', 'amount' => 9500.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Server'
            ],
            // Customer 9: Website Development + Maintenance AMC
            [
                'customer_idx' => 8, 'product' => $otherProducts->where('name', 'Database Optimization Consult')->first(), 'name' => 'PostgreSQL Query Opt',
                'start_offset' => -10, 'expiry_offset' => 10, 'billing_cycle' => 'One Time', 'amount' => 8000.00, 'status' => 'Active', 'auto_renew' => false,
                'type' => 'Other'
            ],
            // Customer 10: Active Hosting
            [
                'customer_idx' => 9, 'product' => $hostingProducts->get(1), 'name' => 'Premium Hosting - profitgyms.com',
                'start_offset' => -20, 'expiry_offset' => 345, 'billing_cycle' => 'Yearly', 'amount' => 5999.00, 'status' => 'Active', 'auto_renew' => true,
                'type' => 'Hosting', 'server' => $serverModels[0], 'type_details' => 'Shared'
            ],
        ];

        foreach ($servicesConfig as $cfg) {
            $customer = $customerModels[$cfg['customer_idx']];
            $startDate = Carbon::today()->addDays($cfg['start_offset']);
            $expiryDate = Carbon::today()->addDays($cfg['expiry_offset']);

            $custService = CustomerService::create([
                'customer_id' => $customer->id,
                'service_product_id' => $cfg['product']->id,
                'service_name' => $cfg['name'],
                'start_date' => $startDate,
                'expiry_date' => $expiryDate,
                'billing_cycle' => $cfg['billing_cycle'],
                'amount' => $cfg['amount'],
                'auto_renew' => $cfg['auto_renew'],
                'status' => $cfg['status'],
                'remarks' => 'Automated test seeding.',
                'created_by' => 1,
            ]);

            // Create Domain or Hosting details if applicable
            if ($cfg['type'] === 'Domain') {
                Domain::create([
                    'customer_service_id' => $custService->id,
                    'domain_name' => $cfg['domain_name'],
                    'registrar' => $cfg['registrar'],
                    'registrar_account' => 'Company-Main',
                    'purchase_date' => $startDate,
                    'expiry_date' => $expiryDate,
                    'auto_renew' => $cfg['auto_renew'],
                    'dns_provider' => 'Cloudflare',
                    'nameserver_1' => 'ns1.cloudflare.com',
                    'nameserver_2' => 'ns2.cloudflare.com',
                    'status' => $cfg['status'],
                ]);
            } elseif ($cfg['type'] === 'Hosting') {
                Hosting::create([
                    'customer_service_id' => $custService->id,
                    'server_id' => $cfg['server']->id,
                    'hosting_type' => $cfg['type_details'],
                    'control_panel' => 'cPanel',
                    'username' => 'c_usr_' . rand(100, 999),
                    'disk_limit' => 50,
                    'bandwidth_limit' => 500,
                    'status' => $cfg['status'] === 'Active' ? 'Active' : 'Suspended',
                ]);
            }

            // Generate Invoice dynamically using BillingService
            $invoice = $billingService->createInvoiceFromService($custService, 0.00, 18.00);

            // Record Payments (some paid, some partial, some unpaid)
            $rand = rand(0, 2);
            if ($rand === 0) {
                // Keep invoice unpaid (Sent)
            } elseif ($rand === 1) {
                // Partial payment
                $partialAmt = round($invoice->total / 2, 2);
                $paymentService->recordPayment($invoice, $partialAmt, 'UPI', 'TXN-' . rand(100000, 999999), 'Partial Payment Seeding');
            } else {
                // Full payment
                $paymentService->recordPayment($invoice, $invoice->total, 'Bank Transfer', 'TXN-' . rand(100000, 999999), 'Full Payment Seeding');
            }
        }
    }
}

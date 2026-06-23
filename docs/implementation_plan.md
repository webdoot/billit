# Service Billing & Renewal Management System - Implementation Plan

We will build a professional Laravel 12 web application called **Service Billing & Renewal Management System** to manage customer details, recurring customer services, domain expiry, hostings, servers, renewals, invoicing, payments, receipts, and reporting.

## User Review Required

> [!IMPORTANT]
> - **Preseeded Roles and Permissions**: We will create three standard roles: `Super Admin`, `Accounts`, and `Support Staff` using `spatie/laravel-permission`. A default Super Admin user will be preseeded with email `admin@company.com` and password `Password@123`.
> - **Bootstrap 5 & Custom Authentication**: Since Laravel 12 uses Tailwind for its default starters (like Breeze), and the specification explicitly requires Bootstrap 5, we will build a clean, custom authentication system (Login, Logout, Password Change) with Bootstrap 5 templates, avoiding Tailwind dependencies.
> - **Scheduler Execution**: The Laravel scheduler will run daily to check for expiring services. We will create a command `services:check-expiry` and alert system that logs/saves alerts to an `expiry_alerts` table, which will update the dashboard and could be extended to send emails.
> - **Repository and Service Pattern**: All controllers will call service classes, and services will interact with the database via repositories, adhering strictly to the requested architecture.

## Open Questions

> [!NOTE]
> - **GST / Tax configuration**: What is the default tax percentage to be applied to invoices? We will assume a default of 18% (standard for IT services in India) and make it configurable or editable on invoice generation.
> - **Invoice and Receipt PDF style**: We will design a clean, professional PDF template for invoices and receipts using `barryvdh/laravel-dompdf` matching standard commercial layouts.
> - **Invoice Numbering Format**: The specified format is `INV-2627-000001` (representing financial year 2026-2027). We will dynamically compute the prefix based on the current fiscal year (April 1st to March 31st).

---

## Proposed Changes

We will create a clean Laravel 12 application in `e:\PROG\BILLING`.

### 1. Database Migrations

We will define migrations for all required tables:

#### [NEW] [migrations](file:///e:/PROG/BILLING/database/migrations)
* `create_customers_table.php`: `id`, `customer_code`, `company_name`, `contact_person`, `email`, `mobile`, `alternate_mobile`, `gstin`, `pan`, `address`, `city`, `state`, `country`, `pin_code`, `website`, `notes`, `status`, `deleted_at` (soft deletes), `timestamps`.
* `create_service_categories_table.php`: `id`, `name`, `description`, `status`, `timestamps`.
* `create_service_products_table.php`: `id`, `service_category_id`, `name`, `billing_cycle`, `price`, `description`, `status`, `timestamps`.
* `create_servers_table.php`: `id`, `name`, `provider`, `hostname`, `ip_address`, `location`, `monthly_cost`, `renewal_date`, `notes`, `status`, `timestamps`.
* `create_customer_services_table.php`: `id`, `customer_id`, `service_product_id`, `service_name`, `start_date`, `expiry_date`, `billing_cycle`, `amount`, `auto_renew` (boolean), `status` (Active, Expired, Suspended, Cancelled, Pending), `remarks`, `created_by`, `deleted_at`, `timestamps`.
* `create_domains_table.php`: `id`, `customer_service_id` (foreign key to `customer_services`), `domain_name`, `registrar`, `registrar_account`, `purchase_date`, `expiry_date`, `auto_renew`, `dns_provider`, `nameserver_1` to `_4`, `status`, `timestamps`.
* `create_hostings_table.php`: `id`, `customer_service_id` (foreign key to `customer_services`), `server_id`, `hosting_type`, `control_panel`, `username`, `disk_limit`, `bandwidth_limit`, `status`, `timestamps`.
* `create_renewals_table.php`: `id`, `customer_service_id`, `renewal_date`, `old_expiry`, `new_expiry`, `amount`, `invoice_id` (nullable), `status` (Pending, Generated, Paid, Expired), `created_by`, `timestamps`.
* `create_invoices_table.php`: `id`, `invoice_no`, `customer_id`, `invoice_date`, `due_date`, `subtotal`, `discount`, `tax`, `total`, `balance`, `status` (Draft, Sent, Paid, Partial, Overdue, Cancelled), `notes`, `created_by`, `deleted_at`, `timestamps`.
* `create_invoice_items_table.php`: `id`, `invoice_id`, `customer_service_id` (nullable), `description`, `qty`, `rate`, `amount`, `timestamps`.
* `create_payments_table.php`: `id`, `invoice_id`, `customer_id`, `amount`, `payment_method` (UPI, Bank Transfer, Cash, Cheque, Razorpay), `transaction_no`, `payment_date`, `remarks`, `created_by`, `timestamps`.
* `create_receipts_table.php`: `id`, `receipt_no`, `payment_id`, `receipt_date`, `amount`, `timestamps`.
* `create_expiry_alerts_table.php`: `id`, `customer_service_id`, `days_before`, `alert_date`, `is_read`, `timestamps`.

---

### 2. Eloquent Models & Relationships

#### [NEW] [Models](file:///e:/PROG/BILLING/app/Models)
We will create models for each table with proper attributes, fillables, casts, and relations:
* `Customer`: Has many `CustomerService`, `Invoice`, `Payment`.
* `ServiceCategory`: Has many `ServiceProduct`.
* `ServiceProduct`: Belongs to `ServiceCategory`, has many `CustomerService`.
* `Server`: Has many `Hosting`.
* `CustomerService`: Belongs to `Customer`, `ServiceProduct`. Has one `Domain`, `Hosting`. Has many `Renewal`, `InvoiceItem`.
* `Domain`: Belongs to `CustomerService`.
* `Hosting`: Belongs to `CustomerService`, `Server`.
* `Renewal`: Belongs to `CustomerService`, `Invoice` (nullable), `User` (`created_by`).
* `Invoice`: Belongs to `Customer`, `User` (`created_by`). Has many `InvoiceItem`, `Payment`.
* `InvoiceItem`: Belongs to `Invoice`, `CustomerService` (nullable).
* `Payment`: Belongs to `Invoice`, `Customer`, `User` (`created_by`). Has one `Receipt`.
* `Receipt`: Belongs to `Payment`.
* `ExpiryAlert`: Belongs to `CustomerService`.

---

### 3. Repositories

We will use the Repository Pattern to decouple Eloquent models from the services.

#### [NEW] [Repositories](file:///e:/PROG/BILLING/app/Repositories)
* `Contracts/BaseRepositoryInterface.php`: Generic CRUD interface.
* `Eloquent/BaseRepository.php`: Base implementation of CRUD.
* Specific Repositories:
  - `CustomerRepository`
  - `ServiceCategoryRepository`
  - `ServiceProductRepository`
  - `ServerRepository`
  - `CustomerServiceRepository`
  - `DomainRepository`
  - `HostingRepository`
  - `RenewalRepository`
  - `InvoiceRepository`
  - `PaymentRepository`
  - `ReceiptRepository`

We will bind these interfaces to concrete classes in a repository service provider:
* `Providers/RepositoryServiceProvider.php`

---

### 4. Service Classes (Business Logic)

Service classes will contain core transaction rules, generation of invoices, handling renewals, processing payments, and calculating balances.

#### [NEW] [Services](file:///e:/PROG/BILLING/app/Services)
* `CustomerService`: Code generation (`CUST-00001`), Ledger generation.
* `BillingService`: Automatically compiles active or renewal-due services, generates Invoice and line items, manages invoice totals, discounts, taxes.
* `RenewalService`: Renews a service, computes new expiry, records renewal history, and triggers updates.
* `PaymentService`: Records a payment (full/partial), updates invoice balances, updates invoice status, generates a receipt.
* `ReceiptService`: Computes receipt number and manages receipt data.
* `AlertService`: Generates daily expiry alerts.

---

### 5. Policies & Authorization

#### [NEW] [Policies](file:///e:/PROG/BILLING/app/Policies)
Laravel policies will map roles and permissions to CRUD actions.
* `CustomerPolicy`, `CustomerServicePolicy`, `InvoicePolicy`, `PaymentPolicy`, `ReportPolicy`, `DomainPolicy`, `ServerPolicy`, `HostingPolicy`.

We will register these policies and utilize Spatie roles/permissions to enforce access gates.

---

### 6. Controllers, Form Requests, and DataTables

We will use Form Requests for all input validation and Yajra DataTables for server-side search and filtering of large datasets.

#### [NEW] [Controllers](file:///e:/PROG/BILLING/app/Http/Controllers)
* `DashboardController`: Compiles system metrics, charts, upcoming renewals, and recent payments.
* `CustomerController`: Handles customer actions and customer ledger display.
* `ServiceCategoryController`, `ServiceProductController`: Service catalogue configuration.
* `CustomerServiceController`: Core customer services panel, handles activation and trigger points.
* `DomainController`, `HostingController`, `ServerController`: IT asset configurations.
* `InvoiceController`: Invoice CRUD, PDF generation, invoice emails.
* `PaymentController`: Enters payments, lists history.
* `ReceiptController`: Generates and exports PDF receipts.
* `ReportController`: Computes and exports (Excel/HTML) Customer Ledger, Outstanding, Collections, Renewals, and Services reports.
* `AuthController`: Manages internal employee logins.

#### [NEW] [Requests](file:///e:/PROG/BILLING/app/Http/Requests)
* Predefined requests like `StoreCustomerRequest`, `UpdateCustomerRequest`, `StoreCustomerServiceRequest`, `StoreInvoiceRequest`, `StorePaymentRequest`, etc.

---

### 7. Automation & Scheduler

#### [NEW] [Commands](file:///e:/PROG/BILLING/app/Console/Commands)
* `ServicesCheckExpiryCommand.php` (`services:check-expiry`):
  - Checks if services should be marked as `Expired`.
  - Scans services expiring in exactly 60, 30, 15, 7, and 1 days.
  - Inserts alerts into `expiry_alerts` database.
  - Recalculates metrics for dashboard indicators.

---

### 8. UI Layout & Blade Views

We will use a modern, responsive admin dashboard using Bootstrap 5, FontAwesome 6, jQuery, Select2, and DataTables.

#### [NEW] [Views](file:///e:/PROG/BILLING/resources/views)
* `layouts/app.blade.php`: Header, Sidebar (collapsible, styled, modern dark theme), Navbar, and scripts container.
* `layouts/auth.blade.php`: Sleek card-centered login interface.
* `dashboard/index.blade.php`: Metrics blocks (colored glassmorphism cards), tables for upcoming renewals, domain alerts, and collection charts.
* `customers/`: Index (DataTable), Create, Edit, Ledger.
* `services/`: Categories, Products, and Customer Services lists.
* `invoices/`: Grid list, Create Invoice (dynamic item rows via jQuery), Show, PDF Template.
* `payments/`: Record payment modal/view, history table.
* `receipts/`: Show receipt, PDF template.
* `reports/`: View tables for outstanding invoices, ledger balance, date-range collections, and expired lists. Includes "Export to Excel" buttons.

---

## Verification Plan

### Automated Tests
* We will write HTTP and Feature tests in `tests/Feature`:
  - `CustomerServiceTest.php`: Tests service registration, expiry calculations, and renewal triggers.
  - `InvoiceBillingTest.php`: Tests generating invoices, item sums, taxes, and recording payments (partial/full).
  - `ExpiryCheckCommandTest.php`: Tests that the daily console command logs alerts at 60/30/15/7/1 days and marks expired services.
  - `RolePermissionTest.php`: Tests role gates (`Accounts` can view reports but not edit servers; `Support Staff` can edit servers but not see payments/invoices).

Run command:
```bash
php artisan test
```

### Manual Verification
1. **Database Seeding**: Run migrations and seeders (`DatabaseSeeder`, `RoleAndPermissionSeeder`) to establish roles, permissions, default categories, and dummy entries.
2. **Login Verification**: Log in as `Super Admin`, `Accounts`, and `Support Staff` to check sidebar menus and action button visibility.
3. **Billing Workflow**:
   - Create a Customer.
   - Assign a Service Product (e.g. Hosting).
   - Generate Invoice, check INV number pattern.
   - Generate PDF and check style/totals.
   - Record a Partial Payment, check status changes to `Partial`.
   - Record remaining Payment, verify status changes to `Paid`, verify Receipt PDF generation and serial number.
4. **Renewal Workflow**:
   - Locate an expiring/expired service, click "Renew".
   - Set new expiry date, verify renewal log in `renewals` table.
5. **Scheduler Run**: Run `php artisan services:check-expiry` with mocked dates and verify alerts populate dashboard renewal flags.

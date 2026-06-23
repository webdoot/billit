# Roadmap & Milestone Mapping - Billit

This document maps the project roadmap across 8 core milestones leading to the stable **Version 1.0** release. All listed features have been fully implemented, verified, and tagged.

---

## 🗺️ Release Milestones (v1.0)

### 📍 Milestone 1 – Foundation (Completed)
Establish the system's core runtime environment, authentication layers, security constraints, and main navigation framework.
- [x] **Laravel 12 Installation**: Initialized skeleton workspace running PHP 8.2+, config configurations, and folder structure.
- [x] **Authentication**: Custom authentication templates utilizing Bootstrap 5 cards (Login, Logout, Session persistence).
- [x] **Roles & Permissions**: Spatie RBAC integration defining `Super Admin`, `Accounts`, and `Support Staff` roles.
- [x] **Dashboard Layout**: Collapsible modern dark-themed sidebar, header notifications, profile dropdowns, and dashboard indicator panels.
- [x] **Settings Module**: Configuration options stored dynamically or via `.env` (like `APP_NAME` and default GST rates).

---

### 📍 Milestone 2 – Customer Management (Completed)
Implement the central directory of clients and track client transactions.
- [x] **Customers CRUD**: Form validation and controllers for registering, editing, and listing clients.
- [x] **Customer Ledger**: Dynamic tabular view displaying the customer financial statement, including list of total invoices, total payments, and Net Outstanding Balance.
- [x] **Customer Search**: Server-side searching, sorting, and pagination via Yajra DataTables.

---

### 📍 Milestone 3 – Service Management (Completed)
Configure the service catalog products and subscribe customers to recurring contracts.
- [x] **Service Categories**: Catalog groupings (Hosting, Domains, Maintenance) in [ServiceCategorySeeder.php](file:///e:/PROG/BILLING/database/seeders/ServiceCategorySeeder.php).
- [x] **Service Products**: Price listings and billing cycles (Annually, Monthly) in catalog.
- [x] **Customer Services**: Subscription mappings linking customers to service catalog products with custom override pricing.
- [x] **Service Expiry Tracking**: Date math calculations establishing start dates, billing cycles, and precise renewal dates.

---

### 📍 Milestone 4 – Domain Management (Completed)
Manage domain assets, nameservers, registrar credentials, and expiry alerts.
- [x] **Domain Registry**: Record domain names, registrars, purchase dates, and nameservers 1–4.
- [x] **Expiry Tracking**: Specific color-coded grid lists highlighting domains expiring soon.
- [x] **Domain Reports**: Expiry registries and domain listing summaries.

---

### 📍 Milestone 5 – Billing (Completed)
Generate and print statements of accounts to charge customers.
- [x] **Invoices**: Create draft/sent invoices for customers with due-date alerts.
- [x] **Invoice Items**: Support multiple items within a single invoice (e.g. Server + Domain renewal).
- [x] **PDF Invoice**: Visual PDF statement templates generated using `laravel-dompdf`.
- [x] **Invoice Number Generator**: Secure, sequential invoice numbering matching Indian fiscal year boundaries (e.g. `INV-2627-000001`).

---

### 📍 Milestone 6 – Payments (Completed)
Record invoice payments and verify receipts.
- [x] **Payment Collection**: Record UPI, Net Banking, Razorpay, or Cash transactions against invoices.
- [x] **Receipts**: Auto-generation of a corresponding receipt upon payment.
- [x] **Receipt PDF**: Exportable receipt statements with official billing headers.
- [x] **Outstanding Tracking**: Automatically transition invoices to `Partial` or `Paid` and decrement the balance in real-time.

---

### 📍 Milestone 7 – Renewals (Completed)
Log subscription renewal actions and automate daily scheduler tasks.
- [x] **Renewal History**: Detail tables mapping renewal dates, previous expiries, and payment transactions.
- [x] **Renewal Workflow**: Interative single-click renewal interface calculating new expiries.
- [x] **Expiry Reports**: Lists of expired and upcoming subscriptions.
- [x] **Scheduler Commands**: Artisan console command `services:check-expiry` integrated with Laravel Schedule to transition expired accounts and alert staff at 60/30/15/7/1 days.

---

### 📍 Milestone 8 – Reports (Completed)
Aggregate metrics for company financial monitoring.
- [x] **Collection Reports**: Date-filtered reports tracking net collections.
- [x] **Renewal Reports**: Future renewal projections.
- [x] **Customer Ledger Reports**: Summarized outstanding balances.

---

## 📈 Future Roadmaps (Post-v1.0)
Planned features for upcoming versions:
- [ ] Direct automated client payment gateway integration (Razorpay / Stripe webhook automation).
- [ ] Email and WhatsApp auto-notifications for expiring services using SMTP / SMS APIs.
- [ ] Customer login portal to view outstanding invoices and purchase renewals directly.

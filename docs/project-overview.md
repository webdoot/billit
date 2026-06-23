# Project Overview - Billit

## 🎯 Introduction
**Billit** is a professional, high-performance web application designed for IT service providers, digital agencies, and hosting companies to streamline their recurring subscription billing, IT asset registries, invoice generation, payment collection, and customer accounting.

In typical IT operations, managing varying renewal cycles for domains, cloud hosting servers, AMC contracts, and SSL certificates across dozens of clients is highly error-prone. Billit addresses this by unifying infrastructure registration, client catalog mappings, automatic expiry checks, milestone warnings, payment collections, and accounting ledger generation under a single dashboard.

---

## 🛠️ Technology Stack
The application is engineered using modern, stable web technologies:

- **Backend**: **Laravel 12 (PHP 8.2+)** utilizing Eloquent ORM, Service Providers, and Artisan Commands.
- **Frontend**: Clean and modern dashboard built with **Bootstrap 5**, styled with custom **Vanilla CSS** tokens (Outfit typography, smooth card animations, and responsive sidebars) tuned to lighter font-weights (reduced by 100 on text and 200 on icons for improved readability), using **FontAwesome 6** for icons.
- **Data Rendering**: **Yajra DataTables** for server-side rendering, search, pagination, and sorting of customer listings and logs.
- **PDF Engine**: **Barryvdh Laravel DomPDF** for composing and exporting commercial-grade invoices and payment receipts.
- **Form Controls & Alerts**: **Select2** for searchable select inputs and **SweetAlert2** for elegant user alerts and confirmations.
- **Testing Suite**: **PHPUnit** with in-memory SQLite configurations for clean, decoupled feature testing.

---

## 🎨 Branding & Visual Assets
Billit features a unified corporate identity across the application interface and repository assets:

- **Dashboard Logo**: Located at [logo.png](file:///e:/PROG/BILLING/public/images/logo.png). A cropped, centered version of the light blue "B" emblem, designed for high visibility inside the dark-themed sidebar menu and login cards.
- **Browser Favicon**: Mapped to [favicon.png](file:///e:/PROG/BILLING/public/images/favicon.png) and the root [favicon.ico](file:///e:/PROG/BILLING/public/favicon.ico). Uses the dark blue "B" emblem with orange ticks for browser tab identification.
- **Corporate Banner Logo**: Located at [billit-logo.png](file:///e:/PROG/BILLING/public/images/billit-logo.png). The complete corporate brand logo containing the "Billit" text and tagline "Bill. Renew. Grow.", integrated into the repository documentation.

---

## 🏗️ Architecture & Design Patterns
Billit follows a strict **Repository and Service Pattern** to achieve high modularity and testability, preventing controllers from becoming bloated with SQL queries or transaction logic.

```mermaid
graph TD
    User[Web Browser] -->|HTTP Request| Controller[Controller]
    Controller -->|Calls API| Service[Service Class]
    Service -->|Uses Interfaces| Repository[Repository Interface]
    Repository -->|Performs CRUD| Eloquent[Eloquent Model]
    Eloquent -->|Queries| DB[(Database)]
```

### 1. Controllers
Controllers handle HTTP requests, invoke input validation via Form Requests, and delegate all business actions to specialized Service classes. They return JSON for DataTables or render blade templates.

### 2. Service Classes (Business Logic Layer)
All transactional math, invoice compilation, payment state transitions, and renewal expiry policies reside here. Examples include:
- `BillingService`: Combines active subscriptions into invoices, applies tax (GST), and tracks invoice balances.
- `PaymentService`: Records invoice payments, updates balances, and initiates receipt creation.
- `AlertService`: Generates daily warning alerts for services about to expire.

### 3. Repository Layer
Models are accessed through Repositories. Interfaces (like `CustomerRepositoryInterface`) decouple the actual data-source (Eloquent ORM) from the business layer, ensuring we can swap database engines or mock data easily during testing.

### 4. Eloquent Models & Migrations
Represent database tables and define database relations, attributes, soft-delete behaviors, and timestamp audits.

---

## 🔐 Key Modules & Capabilities

1. **Dashboard Analytics**: Glassmorphism cards presenting metrics for active customers, active services, unpaid invoices, and collections. Includes charts and tables listing upcoming domain and service expiries.
2. **Customer Ledger**: Comprehensive database profile for clients. Displays a customer ledger statement representing invoice entries, payments, and outstanding balances.
3. **Infrastructure & Asset Registry**: Tracks cloud servers, IP addresses, hosting accounts, and domain names, ensuring that support staff can trace hosting accounts to physical servers.
4. **Billing & Receipts**: Invoicing engine that issues serial-numbered invoice PDFs, processes payments (cash, UPI, cheque, bank transfer), and generates official receipts.
5. **Scheduler & Automation**: Scans the database daily via `php artisan services:check-expiry` to alert staff and suspend services automatically.

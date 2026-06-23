# Billit - Service Billing & Renewal Management System

<p align="center">
  <img src="public/images/logo.png" width="120" height="120" alt="Billit Logo" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
</p>

<p align="center">
  <strong>A premium, automated subscription billing, IT asset management, and renewal tracking system built on Laravel 12.</strong>
</p>

---

## 🚀 Overview

**Billit** is an enterprise-grade web application built to automate customer subscriptions, recurring IT service billing (such as domains, hosting accounts, SSL certificates, AMCs), infrastructure assets (servers), invoice generation, collections, receipts, and reporting. 

It is engineered with a strict **Repository and Service Pattern** to decouple database interactions from business logic, powered by a custom Bootstrap 5 interface featuring interactive charts, Yajra DataTables, and a robust daily automation scheduler.

---

## ✨ Key Features

### 📁 Customer Directory & Ledger
- **Profile Management**: Complete customer records including GSTIN, PAN, billing details, and unique system-generated codes (`CUST-00001` onwards).
- **Financial Ledger**: Instant lookup of outstanding balances, payments, and invoice histories per customer.

### 💼 Service Catalogue & Subscription Management
- **Catalog Management**: Categorized service products (e.g., Domain, Cloud Server, AMC) with defined billing cycles (Monthly, Quarterly, Semi-Annually, Annually, Custom).
- **Active Subscriptions**: Easy enrollment of customers into multiple concurrent services with custom pricing and automated renewal dates.

### 🖥️ IT Asset & Infrastructure Tracking
- **Server Registry**: Keep track of physical/cloud servers (AWS, DigitalOcean, Hetzner, etc.), IP addresses, monthly costs, and server renewal dates.
- **Hosting & Domain Association**: Link hosting accounts (disk space, bandwidth, control panel credentials) and domains (registrar details, nameservers, purchase dates) directly to customer services.

### 📅 Service Expiry Automation & Scheduler
- **Daily Daemon**: Built-in Laravel Console command (`php artisan services:check-expiry`) checks for upcoming expiries daily.
- **Milestone Alerts**: Automatically records dashboard alerts for subscriptions expiring in `60`, `30`, `15`, `7`, and `1` days.
- **Auto-Suspension**: Gracefully transitions expired services to `Expired` status automatically when the due date passes.

### 🧾 Invoice, Payment & Receipt Ledger
- **Dynamic Invoicing**: Create multi-line invoices with itemized rates, automatic subtotaling, configurable discounts, and standard 18% GST tax compilation.
- **Professional PDF Generation**: Clean, corporate invoice layouts exported as PDF statements via `dompdf`.
- **Payment Collections**: Record UPI, Net Banking, Card, or Cash payments. Supports partial payment tracking (marking invoices as `Partial`) and full payment transitions (marking invoices as `Paid`).
- **Official Receipts**: Auto-generate payment receipt PDFs with unique receipt reference numbers.

### 🔐 RBAC (Role-Based Access Control)
- Secured via `spatie/laravel-permission` with three default roles:
  - **Super Admin**: Bypasses all authority checks dynamically and has full system control.
  - **Accounts**: Authorized for invoicing, payments, receipt generation, and financial reports, but blocked from editing infrastructure (servers).
  - **Support Staff**: Authorized for managing servers, domains, and hosting details, but blocked from viewing invoices, payments, and financial reports.

### 📊 Analytics & Reporting
- Real-time financial reports including:
  - **Outstanding Invoices**: Filtered lists of unpaid and partially paid balances.
  - **Collection Reports**: Date-range filtered payment logs.
  - **Renewal Forecasts**: Lists of services due for renewal in upcoming months.

---

## 🛠️ Architecture & Technology Stack

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL / PostgreSQL (SQLite supported for testing)
- **Frontend UI**: Bootstrap 5, FontAwesome 6, jQuery, Select2, and SweetAlert2
- **Data Rendering**: Yajra DataTables (Server-side rendering for speed)
- **PDF Engine**: Barryvdh Laravel DomPDF
- **Design Pattern**: Controller ➔ Service Class ➔ Repository ➔ Eloquent Model

---

## 📦 Installation & Setup

Follow these steps to set up Billit on your local environment:

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL or SQLite

### Setup Guide

1. **Clone the Repository**
   ```bash
   git clone https://github.com/webdoot/billit.git
   cd billit
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` to configure your database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

4. **Run Migrations & Seed Database**
   ```bash
   php artisan migrate --seed
   ```
   *This seeds roles, permissions, 16 service catalog items, and a default Super Admin account:*
   - **Username**: `admin@company.com`
   - **Password**: `Password@123`

5. **Start Dev Server**
   ```bash
   php artisan serve
   ```
   *Visit `http://localhost:8000` to log in.*

---

## ⏰ Automated Task Scheduling

To run the automated subscription expiry checks and milestone alert generation, set up a cron job on your server to execute the Laravel scheduler every minute:

```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

You can test the expiry logic manually at any time by running the console command:
```bash
php artisan services:check-expiry
```

---

## 🧪 Testing

The system is shipped with a comprehensive suite of unit and feature tests validating billing calculations, GST taxes, payments, scheduler alerts, and role authorization:

```bash
php artisan test
```

---

## 📄 License
This project is open-source software licensed under the [MIT License](LICENSE).

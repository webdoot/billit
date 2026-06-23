# Business Rules - Billit

This document outlines the operational workflows, automation policies, accounting logics, and security rules implemented in the Billit application.

---

## 🔐 1. Role-Based Access Control (RBAC)
We enforce role permissions using `spatie/laravel-permission`. There are three pre-configured roles:

| Module / Route | Super Admin | Accounts | Support Staff |
| :--- | :---: | :---: | :---: |
| **Manage Catalog** (Products & Categories) | ✅ Yes | ✅ Yes | ❌ No |
| **Manage Customers** (Directory & CRUD) | ✅ Yes | ✅ Yes | ❌ No |
| **Billing & Accounts** (Invoices & Payments) | ✅ Yes | ✅ Yes | ❌ No |
| **Infrastructure Assets** (Servers, Domains, Hosting) | ✅ Yes | ❌ No | ✅ Yes |
| **Financial & Ledger Reports** | ✅ Yes | ✅ Yes | ❌ No |

### Technical Highlights
- **Gate Bypass**: A `Gate::before` callback is registered in `AppServiceProvider.php`. If the authenticated user holds the `Super Admin` role, all authorization requests automatically pass without evaluating individual permissions.
- **Controller Security**: Laravel Policies (e.g. `ServerPolicy`, `InvoicePolicy`) enforce Spatie permissions. In Laravel 12, the `AuthorizesRequests` trait is explicitly imported into the base `Controller` to resolve `$this->authorize()` invocations.

---

## 📅 2. Service Expiry & Alert Timelines
A daily daemon runs inside the Laravel task scheduler to track expiring client subscriptions.

### Expiry Check Command
- **Command**: `php artisan services:check-expiry`
- **Execution Interval**: Scheduled daily (`daily()`) in `routes/console.php`.

### Warning Alerts & Suspension Timeline
- **Milestone Checks**: The scheduler scans for active subscriptions that expire in exactly:
  - **60 Days**
  - **30 Days**
  - **15 Days**
  - **7 Days**
  - **1 Day**
- **De-duplication**: An alert is only inserted into the `expiry_alerts` table if an alert for that specific subscription and `days_before` milestone does not already exist.
- **Auto-Suspension**: Any subscription whose `expiry_date` is less than the current date is automatically updated from `Active` to `Expired` (or `Suspended`), halting automatic operations.

---

## 🔢 3. Invoice & Receipt Number Generation
Serial numbers are generated using transactional locking to prevent race conditions and duplicate codes under heavy usage.

### Fiscal Year Boundary
Serial numbers calculate the current Indian Financial Year (April 1st to March 31st):
- If the month is $\ge 4$ (April or later), the financial year starts at the current year $Y$ and ends at $Y+1$ (e.g. June 2026 -> `2627`).
- If the month is $< 4$ (January to March), the financial year starts at $Y-1$ and ends at $Y$ (e.g. Feb 2027 -> `2627`).

### Numbering Formats
- **Invoice Number**: `INV-YYZZ-XXXXXX`
  - *Example*: `INV-2627-000001`
- **Receipt Number**: `REC-YYZZ-XXXXXX`
  - *Example*: `REC-2627-000001`

### Suffix Logic
1. A database search checks for the latest record matching the specific fiscal year prefix (e.g. `INV-2627-%`).
2. The query uses a pessimistic lock `lockForUpdate()` to prevent other database threads from generating numbers simultaneously.
3. If no matching record is found, the sequence starts at `000001`.
4. Otherwise, the last number is extracted, incremented by 1, and zero-padded to 6 digits.

---

## 🧮 4. Invoicing & Ledger Mathematics

### Totals Calculations
- **Subtotal**: The sum of all invoice line item amounts ($Qty \times Rate$).
- **Tax Base**: Calculated as $Subtotal - Discount$.
- **GST (Goods & Services Tax)**: Set to a default rate of **18%**.
  $$\text{Tax} = (\text{Subtotal} - \text{Discount}) \times 0.18$$
- **Invoice Total**:
  $$\text{Total} = \text{Subtotal} - \text{Discount} + \text{Tax}$$

### Payment Collection & Invoice Status
When a payment of amount $P$ is recorded against an invoice:
1. The invoice's `balance` is updated:
   $$\text{New Balance} = \text{Current Balance} - P$$
2. **Status Resolution**:
   - If $\text{New Balance} = 0$, the invoice status transitions to `Paid`.
   - If $\text{New Balance} > 0$, the invoice status transitions to `Partial`.
3. **Receipt Trigger**: The system automatically generates a corresponding `receipt` with its own unique serial number matching the payment amount.

---

## 🎨 5. UI Typography & Icon Styling
To ensure high readability and a clean dashboard design, we enforce strict styling rules:
- **Font-Weight Reductions**: Standard Bootstrap 5 values are modified by custom CSS to reduce text thickness by 100 for premium typography:
  - Sidebar Brand Title: `700` (instead of 800)
  - Sidebar Menu Headers: `600` (instead of 700)
  - Active Navigation Links & Knowledgebase Links: `400` (instead of 500)
  - Badge Roles & Accordion Headers: `500` (instead of 600)
- **Lighter Icon Rendering**: All FontAwesome icons in layout sections have their CSS weight set to `700 !important` (reduced by 200 from the default `900` solid weight) to make them thinner, sharper, and visually aligned with the Outfit font weights.

@extends('layouts.app')

@section('title', 'Help & Support')
@section('page_title', 'Knowledge Base & Support')

@section('styles')
<style>
    .kb-search-container {
        background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 3rem 2rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .kb-search-container::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(99, 102, 241, 0.15);
        border-radius: 50%;
        top: -100px;
        right: -100px;
        filter: blur(50px);
        pointer-events: none;
    }
    .kb-category-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: #fff;
    }
    .kb-category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
        border-color: var(--primary-color);
    }
    .kb-category-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .kb-article-link {
        color: #475569;
        text-decoration: none;
        display: block;
        padding: 0.6rem 0.8rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    .kb-article-link:hover {
        background-color: rgba(99, 102, 241, 0.05);
        color: var(--primary-color);
        padding-left: 1.2rem;
    }
    .kb-detail-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
    }
    .kb-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<!-- Search & Header Section -->
<div class="kb-search-container text-center text-white">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-3"><i class="fa-solid fa-graduation-cap me-2 text-indigo"></i>How can we help you today?</h2>
            <p class="text-white-50 mb-4">Search our comprehensive Knowledge Base for step-by-step guides, billing schedules, and system instructions.</p>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-0 text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="kb-search-input" class="form-control border-0" placeholder="Search articles, guides, policies..." aria-label="Search">
            </div>
            <div class="mt-3 text-white-50 small">
                Popular topics: <a href="#billing-cycle" class="text-white text-decoration-underline mx-1">Billing Cycle</a>, <a href="#inv-receipts" class="text-white text-decoration-underline mx-1">Invoice Generation</a>, <a href="#domain-alerts" class="text-white text-decoration-underline mx-1">Expiry Alerts</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Categories Grid -->
<div class="row g-4 mb-5" id="kb-categories-grid">
    <div class="col-md-4">
        <div class="card h-100 kb-category-card p-4 border-0 shadow-sm" onclick="scrollToSection('getting-started')">
            <div class="kb-category-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-flag"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Getting Started</h5>
            <p class="text-secondary small mb-3">Learn the core modules, roles, dashboard overview, and permission hierarchies.</p>
            <span class="text-primary fw-semibold small">View 3 articles <i class="fa-solid fa-arrow-right ms-1"></i></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 kb-category-card p-4 border-0 shadow-sm" onclick="scrollToSection('customer-management')">
            <div class="kb-category-icon bg-success-subtle text-success">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Customer & Service Registry</h5>
            <p class="text-secondary small mb-3">Adding customers, configuring package items, and provisioning hosting & domains.</p>
            <span class="text-success fw-semibold small">View 4 articles <i class="fa-solid fa-arrow-right ms-1"></i></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 kb-category-card p-4 border-0 shadow-sm" onclick="scrollToSection('billing-invoices')">
            <div class="kb-category-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Billing & Invoicing</h5>
            <p class="text-secondary small mb-3">Fiscal prefixes, invoice creation, taxes, partial/full payment, and receipt generation.</p>
            <span class="text-warning fw-semibold small">View 4 articles <i class="fa-solid fa-arrow-right ms-1"></i></span>
        </div>
    </div>
</div>

<!-- Knowledge Base Detailed Sections -->
<div class="row">
    <!-- Left Navigation Sticky Column -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card border-0 shadow-sm p-3 sticky-top" style="top: 90px; border-radius: 12px; z-index: 10;">
            <h6 class="fw-bold text-uppercase text-secondary small px-2 mb-3">KB Categories</h6>
            <nav class="nav flex-column">
                <a href="#getting-started" class="kb-article-link mb-1"><i class="fa-solid fa-flag me-2 small"></i>Getting Started</a>
                <a href="#customer-management" class="kb-article-link mb-1"><i class="fa-solid fa-users-gear me-2 small"></i>Customer & Services</a>
                <a href="#billing-invoices" class="kb-article-link mb-1"><i class="fa-solid fa-file-invoice-dollar me-2 small"></i>Billing & Invoices</a>
                <a href="#renewals-alerts" class="kb-article-link mb-1"><i class="fa-solid fa-clock-rotate-left me-2 small"></i>Renewals & Alerts</a>
                <a href="#troubleshooting" class="kb-article-link mb-1"><i class="fa-solid fa-circle-exclamation me-2 small"></i>Troubleshooting FAQs</a>
            </nav>
        </div>
    </div>

    <!-- Right Articles Detail Panel -->
    <div class="col-lg-9 col-md-12">
        
        <!-- Getting Started Section -->
        <section id="getting-started" class="kb-section mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fa-solid fa-flag"></i>
                </div>
                <h3 class="fw-bold m-0 text-dark">Getting Started</h3>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">1.1 Core System Roles & Authorization</h5>
                    <p class="text-secondary">The system enforces access control via three predefined functional user groups:</p>
                    <ul>
                        <li><strong>Super Admin</strong>: Absolute permissions bypass. Can add, edit, or delete any record, configure security parameters, view financial analytics, and override system balances.</li>
                        <li><strong>Accounts</strong>: Primarily focused on customer directory, ledger reports, collections, invoice generation, and official receipts. They have read-only access to infrastructure products and cannot configure servers.</li>
                        <li><strong>Support Staff</strong>: Responsible for provisioning domains, configuring hosting parameters, and assigning servers. They are blocked from financial indices, invoicing records, collection balances, and reports.</li>
                    </ul>
                </div>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">1.2 Dashboard Metrics Overview</h5>
                    <p class="text-secondary">The Dashboard acts as the primary analytical center. Key modules include:</p>
                    <ul>
                        <li><strong>Financial Stats Cards</strong>: Total outstanding receivables, current month collections, active billing agreements, and active domain indices.</li>
                        <li><strong>Pending & Overdue Invoices</strong>: Focuses collection efforts on overdue customers.</li>
                        <li><strong>Domain & Hosting Expiry Alerts</strong>: Highlights upcoming resource expirations within 60 days.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Customer & Service Registry Section -->
        <section id="customer-management" class="kb-section mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <h3 class="fw-bold m-0 text-dark">Customer & Service Registry</h3>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">2.1 Customer Records & Ledger Compilation</h5>
                    <p class="text-secondary">Each customer possesses a unique system-generated identifier (`CUST-XXXXX`). All transactions (invoices, payments, and credit adjustments) automatically flow into their unified Ledger, compiling a real-time account statement that can be exported directly to Excel.</p>
                </div>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">2.2 Provisioning Domains vs Hostings</h5>
                    <p class="text-secondary">When assigning active products to a customer service agreement:</p>
                    <ul>
                        <li>If the product category is <strong>Domain</strong>, the system will prompt support staff to configure domain registry parameters (registrar, registrar account, purchase/expiry dates, nameservers, auto-renew flag).</li>
                        <li>If the product category is <strong>Hosting</strong>, support staff must assign it to a registered infrastructure server (e.g. AWS, DigitalOcean droplet) and input credentials, disk limits, and bandwidth caps.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Billing & Invoicing Section -->
        <section id="billing-invoices" class="kb-section mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h3 class="fw-bold m-0 text-dark">Billing & Invoicing</h3>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" id="inv-receipts">3.1 Invoice Prefixing and Fiscal Alignment</h5>
                    <p class="text-secondary">Invoice numbers follow a strict format matching the financial year pattern: `INV-YYZZ-XXXXXX` (e.g., `INV-2627-000001` represents the 1st invoice issued during the April 1st, 2026 to March 31st, 2027 fiscal year).</p>
                    <p class="text-secondary">Upon creation, a standard 18% GST tax rate is applied automatically on the subtotal (can be customized upon drafting). Generated invoices are saved in PDF form for mailing.</p>
                </div>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" id="billing-cycle">3.2 Recording Payments & Receipts</h5>
                    <p class="text-secondary">Payments can be collected in full or in part. The invoice balances are recalculated instantly:</p>
                    <ul>
                        <li><strong>Partial Payment</strong>: If the recorded amount is less than the invoice outstanding balance, the invoice status changes to <code>Partial</code>, updating the ledger with the new balance. An official payment receipt is generated.</li>
                        <li><strong>Full Payment</strong>: If the paid amount clears the invoice balance, the invoice status transitions to <code>Paid</code>. An official receipt is issued (`REC-YYZZ-XXXXXX`), and any pending renewal logs associated with this invoice are marked as complete.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Renewals & Expiry Alerts Section -->
        <section id="renewals-alerts" class="kb-section mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="fw-bold m-0 text-dark">Renewals & Expiry Alerts</h3>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" id="domain-alerts">4.1 Automatic Expiry Checking</h5>
                    <p class="text-secondary">The system schedules a daily scheduler command (`php artisan services:check-expiry`) to check the active customer services:</p>
                    <ul>
                        <li>If a service exceeds its expiry date, its status is updated to <strong>Expired</strong> automatically.</li>
                        <li>If the service has an associated domain or hosting account, the domain status changes to <strong>Expired</strong>, and the hosting account status changes to <strong>Suspended</strong>.</li>
                        <li>If a service expires in exactly <strong>60, 30, 15, 7, or 1 day(s)</strong>, a system alert is triggered and populated in the dashboard notification pane to warn staff.</li>
                    </ul>
                </div>
            </div>

            <div class="card kb-detail-card border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">4.2 Renewing a Service</h5>
                    <p class="text-secondary">When renewing an expiring service, the user enters a new renewal expiry date and renewal price. Clicking "Renew" executes the following actions:</p>
                    <ol>
                        <li>Generates a record in the <code>renewals</code> history log.</li>
                        <li>Shifts the start date of the service to the day after the old expiry.</li>
                        <li>Updates the service status back to <strong>Active</strong>.</li>
                        <li>Restores hosting status to <strong>Active</strong> and domain status to <strong>Active</strong>.</li>
                        <li>Generates a fresh renewal invoice (if selected).</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Troubleshooting FAQs Section -->
        <section id="troubleshooting" class="kb-section mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <h3 class="fw-bold m-0 text-dark">Troubleshooting FAQs</h3>
            </div>

            <div class="accordion shadow-sm" id="accordionTrouble" style="border-radius: 12px; overflow: hidden;">
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold bg-white text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1">
                            Q: What happens if a customer service is suspended?
                        </button>
                    </h2>
                    <div id="faq-1" class="accordion-collapse collapse show" data-bs-parent="#accordionTrouble">
                        <div class="accordion-body bg-light text-secondary">
                            A: Marking a service agreement as "Suspended" retains all database records but flags it as inactive. Any associated web hosting or domain settings will also change their status to Suspended, warning the support team to take down the service until payment resolves.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-white text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2">
                            Q: How do we fix an invoice drafted with incorrect pricing?
                        </button>
                    </h2>
                    <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#accordionTrouble">
                        <div class="accordion-body bg-light text-secondary">
                            A: Invoices in "Sent" or "Draft" status can be edited by Accounts team members to adjust line items, discounts, or tax rates. However, once a payment is recorded against the invoice, editing is locked to maintain financial ledger compliance.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-white text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3">
                            Q: How can I run the expiry checker command manually?
                        </button>
                    </h2>
                    <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#accordionTrouble">
                        <div class="accordion-body bg-light text-secondary">
                            A: System administrators can run the command via the terminal inside the project directory: <code>php artisan services:check-expiry</code>. This scans and updates all records instantly.
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function scrollToSection(id) {
        $('html, body').animate({
            scrollTop: $('#' + id).offset().top - 85
        }, 300);
    }

    $(document).ready(function() {
        // Quick dynamic filtering logic for KB Search
        $('#kb-search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            // Filter all detail cards
            $('.kb-detail-card').each(function() {
                var cardContent = $(this).text().toLowerCase();
                if (cardContent.indexOf(value) > -1) {
                    $(this).show(200);
                } else {
                    $(this).hide(200);
                }
            });

            // Filter FAQ accordion items
            $('.accordion-item').each(function() {
                var faqContent = $(this).text().toLowerCase();
                if (faqContent.indexOf(value) > -1) {
                    $(this).show(200);
                } else {
                    $(this).hide(200);
                }
            });

            // Hide headers if all children are hidden
            $('.kb-section').each(function() {
                var visibleCards = $(this).find('.kb-detail-card:visible').length;
                var visibleFaqs = $(this).find('.accordion-item:visible').length;
                if (visibleCards === 0 && visibleFaqs === 0) {
                    $(this).hide(200);
                } else {
                    $(this).show(200);
                }
            });
        });
    });
</script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Billit - Service Billing & Renewal Management</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 70px;
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --dark-bg: #0b0f19;
            --card-border: rgba(0, 0, 0, 0.05);
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #0f172a;
            color: #94a3b8;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 1.5rem;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            padding: 1.5rem 0.75rem;
        }

        .menu-header {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            padding: 0.75rem 1rem 0.5rem 1rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            font-weight: 400;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }

        .nav-link-custom i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }

        .nav-link-custom.active {
            background-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .nav-link-custom.active i {
            color: #fff;
        }

        /* Topbar Styling */
        .topbar {
            height: var(--topbar-height);
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 99;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-top: var(--topbar-height);
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Custom Cards */
        .card-custom {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .topbar {
                left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .sidebar.mobile-show {
                transform: translateX(0);
            }
        }

        /* Badges */
        .badge-role {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
        }

        /* Reduce icon font-weight by 200 (from solid 900 to 700) */
        .sidebar i, .topbar i, .main-wrapper i {
            font-weight: 700 !important;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Billit Logo" style="height: 32px; width: auto; margin-right: 10px; border-radius: 6px;">
            <span>Billit</span>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            @can('customers.view')
            <div class="menu-header">Customers</div>
            <a href="{{ route('customers.index') }}" class="nav-link-custom {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Customer Directory</span>
            </a>
            @endcan

            @if(auth()->user()->can('services.view') || auth()->user()->can('servers.view'))
            <div class="menu-header">Services & Catalog</div>
            @can('services.view')
            <a href="{{ route('customer-services.index') }}" class="nav-link-custom {{ request()->routeIs('customer-services.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cubes"></i>
                <span>Active Services</span>
            </a>
            @endcan
            
            <a href="{{ route('service-categories.index') }}" class="nav-link-custom {{ request()->routeIs('service-categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="{{ route('service-products.index') }}" class="nav-link-custom {{ request()->routeIs('service-products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i>
                <span>Products</span>
            </a>
            @endif

            @if(auth()->user()->can('services.view') || auth()->user()->can('servers.view'))
            <div class="menu-header">Infrastructure</div>
            <a href="{{ route('servers.index') }}" class="nav-link-custom {{ request()->routeIs('servers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-server"></i>
                <span>Servers</span>
            </a>
            <a href="{{ route('hostings.index') }}" class="nav-link-custom {{ request()->routeIs('hostings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-network-wired"></i>
                <span>Hosting Accounts</span>
            </a>
            <a href="{{ route('domains.index') }}" class="nav-link-custom {{ request()->routeIs('domains.*') ? 'active' : '' }}">
                <i class="fa-solid fa-globe"></i>
                <span>Domain Registry</span>
            </a>
            @endif

            @if(auth()->user()->can('invoices.view') || auth()->user()->can('payments.view'))
            <div class="menu-header">Billing & Accounts</div>
            @can('invoices.view')
            <a href="{{ route('invoices.index') }}" class="nav-link-custom {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Invoices</span>
            </a>
            @endcan
            @can('payments.view')
            <a href="{{ route('payments.index') }}" class="nav-link-custom {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>Payments</span>
            </a>
            <a href="{{ route('receipts.index') }}" class="nav-link-custom {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Receipts</span>
            </a>
            @endcan
            @endif

            @can('reports.view')
            <div class="menu-header">Analytics</div>
            <a href="{{ route('reports.index') }}" class="nav-link-custom {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Financial Reports</span>
            </a>
            @endcan

            <div class="menu-header">Support</div>
            <a href="{{ route('help.index') }}" class="nav-link-custom {{ request()->routeIs('help.*') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-question"></i>
                <span>Help & Support</span>
            </a>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark p-0 me-3 d-lg-none" id="sidebar-toggle">
                <i class="fa-solid fa-bars fa-lg"></i>
            </button>
            <h4 class="m-0 fw-bold">@yield('page_title', 'Dashboard')</h4>
        </div>
        <div class="d-flex align-items-center">
            <!-- Role Badge -->
            @php
                $role = auth()->user()->roles->first()->name ?? 'Staff';
                $badgeClass = 'bg-secondary';
                if ($role === 'Super Admin') $badgeClass = 'bg-danger';
                if ($role === 'Accounts') $badgeClass = 'bg-success';
                if ($role === 'Support Staff') $badgeClass = 'bg-primary';
            @endphp
            <span class="badge {{ $badgeClass }} badge-role me-3">{{ $role }}</span>
            
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none text-dark dropdown-toggle p-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar me-2 bg-light text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; border: 1px solid #cbd5e1;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="fw-semibold d-none d-md-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 8px;">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Toast Notifications / Alert Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #10b981 !important;">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #ef4444 !important;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar mobile toggle
            $('#sidebar-toggle').on('click', function() {
                $('#sidebar').toggleClass('mobile-show');
            });

            // Initialize global tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize Select2 globally on matching elements
            $('.select2-enable').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Close sidebar when clicking outside on mobile
            $(document).click(function(event) {
                var $target = $(event.target);
                if (!$target.closest('#sidebar').length && !$target.closest('#sidebar-toggle').length && $('#sidebar').hasClass('mobile-show')) {
                    $('#sidebar').removeClass('mobile-show');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Smart Inventory'))</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <style>
        :root {
            --bs-primary: #4e73df;
            --bs-primary-rgb: 78, 115, 223;
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --body-bg: #f8f9fc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #4e5e6a;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        /* Sidebar Styles */
        #sidebar-wrapper {
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            transition: all 0.25s ease;
            position: fixed;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar-nav {
            overflow-y: auto;
            flex-grow: 1;
            padding-bottom: 2rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        #sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 2rem 1.25rem;
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem;
            text-align: left;
            letter-spacing: 0.02rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.08);
        }

        .sidebar-brand img {
            height: 42px;
            width: 42px;
            object-fit: cover;
            border-radius: 10px;
            background: #fff;
            padding: 2px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .nav-link {
            padding: 0.8rem 1.5rem;
            color: #fff !important;
            font-weight: 600;
            font-size: 0.85rem;
            opacity: 0.9;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: inset 4px 0 0 #fff;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-heading {
            padding: 1.5rem 1.5rem 0.6rem;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.65);
            letter-spacing: 0.12rem;
        }

        /* Page Content Styles */
        #page-content-wrapper {
            width: 100%;
            padding-left: var(--sidebar-width);
            transition: all 0.25s ease;
        }

        .topbar {
            height: var(--topbar-height);
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 2rem;
        }

        /* Main Content Container */
        .container-fluid {
            padding: 0 1.5rem 2rem;
        }

        /* Professional Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: var(--bs-primary);
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* Badge Styles */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        /* Table Customization */
        .table thead th {
            background-color: #f8f9fc;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 800;
            color: #4e5e6a;
            border-top: none;
            padding: 1rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
            font-size: 0.9rem;
        }

        /* Profile Dropdown */
        .nav-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .profile-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .page-item .page-link {
            border: 1px solid #e3e6f0;
            color: #4e73df;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.5rem 0.85rem;
            border-radius: 8px !important;
            transition: all 0.2s;
        }

        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
            color: #fff;
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);
        }

        .page-link:hover {
            background-color: #f8f9fc;
            color: #224abe;
        }

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            flex-wrap: wrap;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .pagination-container {
                justify-content: center;
                text-align: center;
            }
        }

        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                padding-left: 0;
            }
            #wrapper.toggled #page-content-wrapper {
                padding-left: var(--sidebar-width);
            }
        }
    </style>
</head>
<body id="page-top">

    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-brand">
                @if($logo = \App\Models\SystemSetting::get('app_logo'))
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-white text-primary rounded-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </div>
                @endif
                <div class="d-flex flex-column lh-1">
                    <span class="fs-6 fw-bold mb-0">{{ \App\Models\SystemSetting::get('short_name', 'Rozitech') }}</span>
                    <span class="extra-small opacity-75 fw-normal text-uppercase tracking-widest mt-1" style="font-size: 0.6rem;">Inventory</span>
                </div>
            </div>
            
            <div id="sidebar-nav">
                <div class="sidebar-heading">Core</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                @can('view inventory')
                <div class="sidebar-heading">Assets</div>
                <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="bi bi-archive"></i>
                    <span>Inventori</span>
                </a>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori</span>
                </a>
                @endcan

                @can('view project')
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <i class="bi bi-layers"></i>
                    <span>Projects</span>
                </a>
                @endcan

                <div class="sidebar-heading">Transactions</div>
                @can('view borrow')
                <a href="{{ route('borrow.index') }}" class="nav-link {{ request()->routeIs('borrow.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-right-circle"></i>
                    <span>Borrowing</span>
                </a>
                @endcan

                @can('view return')
                <a href="{{ route('return.index') }}" class="nav-link {{ request()->routeIs('return.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-left-circle"></i>
                    <span>Returns</span>
                </a>
                @endcan

                @can('view reports')
                <div class="sidebar-heading">Analytics</div>
                <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Inventory Report</span>
                </a>
                <a href="{{ route('reports.borrow') }}" class="nav-link {{ request()->routeIs('reports.borrow') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Borrow Report</span>
                </a>
                <a href="{{ route('reports.risk') }}" class="nav-link {{ request()->routeIs('reports.risk') ? 'active' : '' }}">
                    <i class="bi bi-shield-shaded"></i>
                    <span>Risk Analytics</span>
                </a>
                @endcan

                @can('access admin panel')
                <div class="sidebar-heading">Management</div>
                @can('manage users')
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
                @endcan
                
                @can('manage ai rules')
                <a href="{{ route('admin.ai-rules.index') }}" class="nav-link {{ request()->routeIs('admin.ai-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-robot"></i>
                    <span>AI Rules</span>
                </a>
                @endcan

                @can('manage risk rules')
                <a href="{{ route('admin.risk-rules.index') }}" class="nav-link {{ request()->routeIs('admin.risk-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-exclamation"></i>
                    <span>Risk Logic</span>
                </a>
                @endcan

                @if(auth()->user()->hasRole('Admin'))
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>System Settings</span>
                </a>
                @endif
                @endcan
            </div>
        </div>

        <!-- Content -->
        <div id="page-content-wrapper">
            <!-- Topbar -->
            <nav class="topbar">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4 text-dark"></i>
                </button>

                <div class="d-none d-sm-block ms-3">
                    <h1 class="h6 mb-0 font-weight-bold text-gray-800 text-uppercase tracking-wider">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <a class="nav-link position-relative text-dark" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill fs-5"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-top: 10px; margin-left: -5px">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 300px;">
                            <li class="dropdown-header font-weight-bold p-3">Alerts Center</li>
                            @forelse(auth()->user()->notifications->take(5) as $notif)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item p-3 d-flex align-items-center gap-3" href="#">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                        <i class="bi bi-info-circle text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">{{ $notif->created_at->diffForHumans() }}</div>
                                        <div class="fw-bold">{{ $notif->data['title'] ?? 'System Update' }}</div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-4 text-center text-muted small">No new alerts</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Language Selector -->
                    <div class="dropdown">
                        <a class="nav-link text-dark d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            @if(app()->getLocale() == 'id')
                                <span class="fi fi-id"></span> <span class="extra-small fw-black">ID</span>
                            @else
                                <span class="fi fi-us"></span> <span class="extra-small fw-black">EN</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                            <li>
                                <a class="dropdown-item rounded p-2 d-flex align-items-center gap-2 {{ app()->getLocale() == 'id' ? 'bg-light fw-bold' : '' }}" href="{{ route('lang.switch', 'id') }}">
                                    Bahasa Indonesia
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded p-2 d-flex align-items-center gap-2 {{ app()->getLocale() == 'en' ? 'bg-light fw-bold' : '' }}" href="{{ route('lang.switch', 'en') }}">
                                    English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="vr mx-2 bg-gray-200" style="height: 24px"></div>

                    <!-- User Information -->
                    <div class="dropdown">
                        <div class="nav-profile ms-2" data-bs-toggle="dropdown">
                            <span class="d-none d-lg-inline text-gray-600 small fw-bold me-2">{{ auth()->user()->name }}</span>
                            <img class="profile-img" src="{{ auth()->user()->avatar_url }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4e73df&color=fff'">
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2" style="width: 200px">
                            <li>
                                <a class="dropdown-item rounded p-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            @if(auth()->user()->hasRole('Admin'))
                            <li>
                                <a class="dropdown-item rounded p-2" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded p-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="container-fluid">
                {{-- Flash Messages --}}
                @foreach(['success', 'error', 'warning', 'info'] as $type)
                @if(session($type))
                <div class="alert alert-{{ $type === 'success' ? 'success' : ($type === 'error' ? 'danger' : ($type === 'warning' ? 'warning' : 'info')) }} alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-{{ $type === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                    {{ session($type) }}
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @endforeach

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
    
    @stack('scripts')
</body>
</html>

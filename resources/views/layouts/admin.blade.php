<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - E-Beauty')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite Assets Removed (Using CDN) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: #fff;
            width: 260px;
            position: fixed;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.8rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.05);
            border-left-color: #ff6b9d;
        }
        
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 2rem;
        }
        
        .content-wrapper {
            padding: 2rem;
            flex: 1;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border-radius: 10px;
        }
        
        .btn-primary {
            background-color: #ff6b9d;
            border-color: #ff6b9d;
        }
        
        .btn-primary:hover {
            background-color: #c44569;
            border-color: #c44569;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="py-4 text-center border-bottom border-secondary">
            <h4 class="mb-0 fw-bold text-white">
                <i class="fas fa-gem me-2" style="color: #ff6b9d;"></i> E-Beauty
            </h4>
            <small class="text-white-50">Admin Panel</small>
        </div>
        
        <nav class="nav flex-column mt-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-3 text-center" style="width: 20px;"></i> Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box me-3 text-center" style="width: 20px;"></i> Produk
            </a>
            <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <i class="fas fa-tag me-3 text-center" style="width: 20px;"></i> Brand
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder me-3 text-center" style="width: 20px;"></i> Kategori
            </a>
            <a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                <i class="fas fa-warehouse me-3 text-center" style="width: 20px;"></i> Stok
            </a>
            <a href="{{ route('admin.promos.index') }}" class="nav-link {{ request()->routeIs('admin.promos.*') ? 'active' : '' }}">
                <i class="fas fa-percent me-3 text-center" style="width: 20px;"></i> Promo
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fas fa-star me-3 text-center" style="width: 20px;"></i> Review
            </a>
            
            <div class="px-3 my-3">
                <hr class="border-secondary">
            </div>
            
            <a href="{{ route('home') }}" class="nav-link">
                <i class="fas fa-home me-3 text-center" style="width: 20px;"></i> Ke Website
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container-fluid">
                <button class="btn btn-link text-dark d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                
                <h5 class="mb-0 ms-2 text-secondary">@yield('page_title', '')</h5>
                
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 d-none d-sm-inline">
                        Selamat datang, <strong>{{ Auth::user()->name }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    
    @stack('scripts')
</body>
</html>

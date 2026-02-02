<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'E-Beauty - Katalog Produk Kecantikan')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets Removed (Using CDN) -->
    <!-- <script type="module" src="..."></script> -->
    
    <style>
        :root {
            --primary-color: #ff6b9d;
            --secondary-color: #c44569;
            --accent-color: #ffa07a;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfc;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .bg-gradient-pink {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.4);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        
        footer {
            background-color: var(--dark-color);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand text-gradient" href="{{ route('home') }}">
                <i class="fas fa-gem me-2"></i>E-Beauty
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link fw-medium {{ request()->routeIs('home') ? 'text-primary' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium {{ request()->routeIs('catalog') ? 'text-primary' : '' }}" href="{{ route('catalog') }}">Catalog</a>
                    </li>
                    @auth
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg me-2 text-secondary"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(Auth::user()->email === 'admin@ebeauty.com')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-primary rounded-pill px-4" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <h5 class="mb-3 text-gradient d-inline-block fw-bold">E-Beauty</h5>
                    <p class="text-white-50">Platform katalog produk kecantikan terlengkap dengan berbagai pilihan brand ternama.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-white">Home</a></li>
                        <li class="mb-2"><a href="{{ route('catalog') }}" class="text-white-50 text-decoration-none hover-white">Catalog</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50 hover-primary"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white-50 hover-primary"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white-50 hover-primary"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white-50 hover-primary"><i class="fab fa-tiktok fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-white-50 small">
                <p>&copy; {{ date('Y') }} E-Beauty. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>

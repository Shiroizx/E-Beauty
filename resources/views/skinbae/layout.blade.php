<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Skinbae.ID')</title>
    <meta name="description" content="@yield('meta_description', 'Perawatan kecantikan elegan — Skinbae.ID.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sb-ivory: #faf8f5;
            --sb-cream: #f3efe8;
            --sb-charcoal: #2c2a26;
            --sb-muted: #6b6560;
            --sb-rose: #b76e79;
            --sb-rose-deep: #9a5a63;
            --sb-gold: #a8946c;
            --sb-white: #ffffff;
        }

        body.skinbae-body {
            font-family: 'DM Sans', system-ui, sans-serif;
            font-weight: 400;
            color: var(--sb-charcoal);
            background: var(--sb-ivory);
            letter-spacing: 0.01em;
        }

        .skinbae-body h1, .skinbae-body h2, .skinbae-body h3, .skinbae-body h4, .skinbae-body .font-serif {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 600;
        }

        .skinbae-nav {
            background: rgba(250, 248, 245, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(44, 42, 38, 0.06);
            z-index: 1030;
        }

        .skinbae-nav .nav-link {
            color: var(--sb-charcoal);
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.5rem 0.85rem !important;
        }

        .skinbae-nav .nav-link:hover, .skinbae-nav .nav-link.active {
            color: var(--sb-rose);
        }

        .skinbae-brand {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--sb-charcoal);
            letter-spacing: 0.04em;
        }

        .skinbae-brand span { color: var(--sb-rose); }

        .btn-sb-primary {
            background: linear-gradient(135deg, var(--sb-rose) 0%, var(--sb-rose-deep) 100%);
            border: none;
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 0.65rem 1.5rem;
            border-radius: 0;
        }

        .btn-sb-primary:hover {
            background: var(--sb-rose-deep);
            color: #fff;
        }

        .btn-sb-outline {
            border: 1px solid var(--sb-charcoal);
            color: var(--sb-charcoal);
            background: transparent;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 0.65rem 1.5rem;
            border-radius: 0;
        }

        .btn-sb-outline:hover {
            background: var(--sb-charcoal);
            color: #fff;
        }

        .skinbae-footer {
            background: var(--sb-charcoal);
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
        }

        .skinbae-footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
        }

        .skinbae-footer a:hover { color: #fff; }

        .skinbae-footer-cta:hover {
            background: #fff;
            color: var(--sb-charcoal) !important;
        }

        .section-label {
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--sb-gold);
            font-weight: 600;
        }

        .divider-gold {
            width: 48px;
            height: 1px;
            background: var(--sb-gold);
            opacity: 0.7;
        }

        .letter-spacing-2 { letter-spacing: 0.12em; }
    </style>
    @stack('styles')
</head>
<body class="skinbae-body d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg skinbae-nav sticky-top py-3">
        <div class="container">
            <a class="navbar-brand skinbae-brand text-decoration-none" href="{{ route('skinbae.home') }}">
                Skinbae<span>.ID</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#skinbaeNav" aria-controls="skinbaeNav" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="skinbaeNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skinbae.home') ? 'active' : '' }}" href="{{ route('skinbae.home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skinbae.services') ? 'active' : '' }}" href="{{ route('skinbae.services') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skinbae.gallery') ? 'active' : '' }}" href="{{ route('skinbae.gallery') }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skinbae.contact*') ? 'active' : '' }}" href="{{ route('skinbae.contact') }}">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-sb-primary" href="{{ config('skinbae.booking_url') }}" target="_blank" rel="noopener">Book</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success border-0 rounded-0 shadow-sm" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="skinbae-footer pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="skinbae-brand text-white mb-3">Skinbae<span>.ID</span></div>
                    <p class="small opacity-75 mb-0">Perawatan kecantikan yang harmonis — didedikasikan untuk keseimbangan, keanggunan, dan kepercayaan diri Anda.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white text-uppercase small letter-spacing-2 mb-3">Visit</h6>
                    <p class="small mb-1"><i class="fas fa-map-marker-alt me-2 opacity-50"></i>{{ config('skinbae.address_line') }}</p>
                    <p class="small mb-1"><i class="fas fa-clock me-2 opacity-50"></i>{{ config('skinbae.hours') }}</p>
                    <p class="small mb-0"><i class="fas fa-phone me-2 opacity-50"></i>{{ config('skinbae.phone_display') }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white text-uppercase small letter-spacing-2 mb-3">Connect</h6>
                    <div class="d-flex gap-3 mb-3">
                        <a href="{{ config('skinbae.instagram_url') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="{{ config('skinbae.whatsapp_url') }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                    <a href="{{ route('skinbae.contact') }}" class="btn btn-sm text-white border border-white rounded-0 px-3 py-2 text-decoration-none text-uppercase small fw-semibold skinbae-footer-cta">Kirim pesan</a>
                    <p class="small mt-3 mb-0 opacity-50">
                        <a href="{{ route('home') }}" class="text-white-50">Toko &amp; katalog produk</a>
                    </p>
                </div>
            </div>
            <hr class="border-secondary opacity-25 my-4">
            <p class="small text-center mb-0 opacity-50">&copy; {{ date('Y') }} Skinbae.ID. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

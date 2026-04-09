<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Skinbae.ID — Katalog Produk Kecantikan')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-b from-brand-50/80 via-white to-brand-50/40 font-sans text-neutral-800 antialiased">
    <header
        class="sticky top-0 z-[1030] border-b border-brand-100/80 bg-white/90 shadow-sm shadow-brand-200/20 backdrop-blur-md"
        x-data="{ mobileOpen: false }"
        @keydown.escape.window="mobileOpen = false"
    >
        <nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8" aria-label="Utama">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-bold tracking-tight text-brand-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-md shadow-brand-400/30">
                    <i class="fas fa-gem text-sm" aria-hidden="true"></i>
                </span>
                <span>Skinbae<span class="font-semibold text-brand-500">.ID</span></span>
            </a>

            <button
                type="button"
                class="inline-flex items-center justify-center rounded-full border border-brand-200 p-2 text-brand-700 lg:hidden"
                @click="mobileOpen = !mobileOpen"
                :aria-expanded="mobileOpen"
                aria-controls="main-nav"
                aria-label="Buka menu"
            >
                <i class="fas fa-bars" x-show="!mobileOpen" x-cloak aria-hidden="true"></i>
                <i class="fas fa-times" x-show="mobileOpen" x-cloak aria-hidden="true"></i>
            </button>

            <div
                id="main-nav"
                class="w-full flex-col gap-1 lg:flex lg:w-auto lg:flex-row lg:items-center lg:gap-1"
                :class="mobileOpen ? 'flex' : 'hidden lg:flex'"
            >
                <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('home') ? 'bg-brand-100 text-brand-800' : 'text-neutral-600 hover:bg-brand-50 hover:text-brand-700' }}">Home</a>
                <a href="{{ route('catalog') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('catalog') ? 'bg-brand-100 text-brand-800' : 'text-neutral-600 hover:bg-brand-50 hover:text-brand-700' }}">Catalog</a>

                @auth
                    <a href="{{ route('cart.index') }}" class="relative rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('cart.*') ? 'bg-brand-100 text-brand-800' : 'text-neutral-600 hover:bg-brand-50 hover:text-brand-700' }}" title="Keranjang">
                        <i class="fas fa-shopping-bag me-1.5" aria-hidden="true"></i>
                        Keranjang
                        @if(($navCartCount ?? 0) > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-gradient-to-r from-brand-500 to-brand-600 px-1 text-[0.65rem] font-bold text-white">{{ $navCartCount > 99 ? '99+' : $navCartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="relative rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('wishlist.*') ? 'bg-brand-100 text-brand-800' : 'text-neutral-600 hover:bg-brand-50 hover:text-brand-700' }}" title="Wishlist">
                        <i class="far fa-heart me-1.5" aria-hidden="true"></i>
                        Wishlist
                        @if(($navWishlistCount ?? 0) > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-brand-200 px-1 text-[0.65rem] font-bold text-brand-900">{{ $navWishlistCount > 99 ? '99+' : $navWishlistCount }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ $loginCartUrl ?? route('login', ['redirect' => '/cart']) }}" class="rounded-full px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-brand-50 hover:text-brand-700" title="Login untuk keranjang">
                        <i class="fas fa-shopping-bag me-1.5" aria-hidden="true"></i>
                        Keranjang
                    </a>
                @endauth

                <div class="mt-2 flex flex-col gap-2 border-t border-brand-100 pt-2 lg:mt-0 lg:ml-2 lg:flex-row lg:border-0 lg:pt-0">
                    @auth
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                type="button"
                                class="flex w-full items-center gap-2 rounded-full border border-brand-100 bg-brand-50/50 px-4 py-2 text-left text-sm font-medium text-brand-900 transition hover:border-brand-200 hover:bg-brand-50 lg:w-auto"
                                @click="open = !open"
                                :aria-expanded="open"
                                aria-haspopup="true"
                            >
                                <i class="fas fa-user-circle text-lg text-brand-500" aria-hidden="true"></i>
                                <span class="truncate">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ms-auto text-xs text-brand-400 lg:ms-0" aria-hidden="true"></i>
                            </button>
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-cloak
                                class="absolute end-0 z-50 mt-2 min-w-[12rem] overflow-hidden rounded-2xl border border-brand-100 bg-white py-1 shadow-xl shadow-brand-200/40"
                                role="menu"
                            >
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">
                                    <i class="fas fa-receipt w-4 text-brand-500" aria-hidden="true"></i>
                                    Riwayat Pesanan
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">
                                    <i class="far fa-heart w-4 text-brand-500" aria-hidden="true"></i>
                                    Wishlist
                                </a>
                                @if(Auth::user()->email === 'admin@skinbae.id')
                                    <div class="my-1 border-t border-brand-100"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">
                                        <i class="fas fa-tachometer-alt w-4 text-brand-500" aria-hidden="true"></i>
                                        Dashboard Admin
                                    </a>
                                @endif
                                <div class="my-1 border-t border-brand-100"></div>
                                <form action="{{ route('logout') }}" method="POST" role="none">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                        <i class="fas fa-sign-out-alt w-4" aria-hidden="true"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-brand ms-0 text-center lg:ms-2">
                            <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-emerald-900 shadow-sm" role="alert">
                <i class="fas fa-check-circle mt-0.5 text-emerald-500" aria-hidden="true"></i>
                <p class="flex-1 text-sm">{{ session('success') }}</p>
                <button type="button" class="rounded-lg p-1 text-emerald-700 hover:bg-emerald-100" @click="show = false" aria-label="Tutup">
                    <i class="fas fa-times text-xs" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50/90 px-4 py-3 text-red-900 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500" aria-hidden="true"></i>
                <p class="flex-1 text-sm">{{ session('error') }}</p>
                <button type="button" class="rounded-lg p-1 text-red-700 hover:bg-red-100" @click="show = false" aria-label="Tutup">
                    <i class="fas fa-times text-xs" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-brand-900/20 bg-gradient-to-br from-brand-900 via-brand-800 to-[#4a1a2e] text-brand-100">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <p class="mb-3 text-xl font-bold text-white">Skinbae.ID</p>
                    <p class="text-sm leading-relaxed text-brand-200/90">Katalog produk kecantikan pilihan — skincare, makeup, dan perawatan dari brand terpercaya.</p>
                </div>
                <div>
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-brand-300">Tautan</h2>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-brand-200 transition hover:text-white">Home</a></li>
                        <li><a href="{{ route('catalog') }}" class="text-brand-200 transition hover:text-white">Catalog</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-brand-300">Ikuti kami</h2>
                    <div class="flex gap-4 text-lg">
                        <a href="#" class="text-brand-300 transition hover:text-white" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                        <a href="#" class="text-brand-300 transition hover:text-white" aria-label="Facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                        <a href="#" class="text-brand-300 transition hover:text-white" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                        <a href="#" class="text-brand-300 transition hover:text-white" aria-label="TikTok"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-8 text-center text-xs text-brand-300/80">
                <p>&copy; {{ date('Y') }} Skinbae.ID. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

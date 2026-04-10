<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Skinbae.ID')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans text-gray-800 antialiased" style="background:#f7f6f3" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col transition-transform duration-300 ease-out lg:translate-x-0"
           style="background: linear-gradient(180deg, #110e1c 0%, #1a1530 100%)">

        {{-- Logo --}}
        <div class="flex h-[68px] items-center gap-3 px-5 border-b border-white/[0.06]">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 shadow-lg shadow-brand-500/20">
                <i class="fas fa-gem text-white text-sm"></i>
            </div>
            <div class="leading-tight">
                <span class="text-[15px] font-bold tracking-tight text-white">Skinbae<span class="text-brand-400">.</span>ID</span>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.15em] text-white/25">{{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Admin Panel' }}</span>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto flex h-7 w-7 items-center justify-center rounded-lg text-white/30 hover:bg-white/5 hover:text-white/60 lg:hidden">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="mt-5 flex-1 space-y-0.5 overflow-y-auto px-3 pb-4 scrollbar-thin">
            <span class="mb-2.5 block px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/20">Menu Utama</span>

            @php
                $isSuper = Auth::user()->isSuperAdmin();
                $operationalNav = [
                    ['route' => 'admin.dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'pattern' => 'admin.dashboard'],
                    ['route' => 'admin.analytics.trend', 'icon' => 'fa-chart-area', 'label' => 'Analitik Tren', 'pattern' => 'admin.analytics.trend'],
                    ['route' => 'admin.products.index', 'icon' => 'fa-box', 'label' => 'Produk', 'pattern' => 'admin.products.*'],
                    ['route' => 'admin.brands.index', 'icon' => 'fa-tag', 'label' => 'Brand', 'pattern' => 'admin.brands.*'],
                    ['route' => 'admin.categories.index', 'icon' => 'fa-folder', 'label' => 'Kategori', 'pattern' => 'admin.categories.*'],
                    ['route' => 'admin.orders.index', 'icon' => 'fa-receipt', 'label' => 'Pesanan', 'pattern' => 'admin.orders.*'],
                    ['route' => 'admin.stocks.index', 'icon' => 'fa-warehouse', 'label' => 'Stok', 'pattern' => 'admin.stocks.*'],
                    ['route' => 'admin.promos.index', 'icon' => 'fa-percent', 'label' => 'Promo', 'pattern' => 'admin.promos.*'],
                    ['route' => 'admin.reviews.index', 'icon' => 'fa-star', 'label' => 'Review', 'pattern' => 'admin.reviews.*'],
                ];
                $superNav = [
                    ['route' => 'admin.super.dashboard', 'icon' => 'fa-shield-alt', 'label' => 'Dashboard', 'pattern' => 'admin.super.dashboard'],
                    ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Pengguna', 'pattern' => 'admin.users.*'],
                    ['route' => 'admin.analytics.trend', 'icon' => 'fa-chart-area', 'label' => 'Analitik Tren', 'pattern' => 'admin.analytics.trend'],
                    ['route' => 'admin.activity-log.index', 'icon' => 'fa-history', 'label' => 'Activity Log', 'pattern' => 'admin.activity-log.*'],
                ];
                $navItems = $isSuper ? $superNav : $operationalNav;
            @endphp

            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200
                          {{ $isActive
                              ? 'bg-white/[0.08] text-white shadow-sm shadow-black/10'
                              : 'text-white/40 hover:bg-white/[0.04] hover:text-white/70' }}">
                    @if($isActive)
                        <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-brand-400"></span>
                    @endif
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg transition-colors
                                {{ $isActive ? 'bg-brand-500/15 text-brand-400' : 'text-white/25 group-hover:text-white/45' }}">
                        <i class="fas {{ $item['icon'] }} text-xs"></i>
                    </span>
                    {{ $item['label'] }}
                    @if($isActive)
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400 shadow-sm shadow-brand-400/50"></span>
                    @endif
                </a>
            @endforeach

            <div class="my-5 border-t border-white/[0.05]"></div>
            <span class="mb-2.5 block px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/20">Lainnya</span>

            <a href="{{ route('home') }}" target="_blank"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium text-white/40 transition-all duration-200 hover:bg-white/[0.04] hover:text-white/70">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg text-white/25 group-hover:text-white/45 transition-colors">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </span>
                Ke Website
            </a>
        </nav>

        {{-- User at bottom --}}
        <div class="border-t border-white/[0.06] p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-400/20 to-brand-600/20 text-xs font-bold text-brand-400 ring-1 ring-white/10">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white/75">{{ Auth::user()->name }}</p>
                    <p class="truncate text-[10px] text-white/25">{{ Auth::user()->email }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="flex h-8 w-8 items-center justify-center rounded-lg text-white/20 transition hover:bg-red-500/10 hover:text-red-400">
                        <i class="fas fa-sign-out-alt text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:pl-[260px]">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 flex h-[60px] items-center gap-4 border-b border-stone-200/60 bg-white/70 px-4 backdrop-blur-xl sm:px-6">
            <button @click="sidebarOpen = true" class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-stone-100 hover:text-gray-600 lg:hidden">
                <i class="fas fa-bars"></i>
            </button>

            <h1 class="text-sm font-semibold text-gray-600">@yield('page_title', '')</h1>

            <div class="ml-auto flex items-center gap-3">
                <span class="hidden text-xs text-gray-400 sm:inline">
                    Halo, <strong class="font-semibold text-gray-600">{{ Auth::user()->name }}</strong>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 bg-red-50/80 px-3 py-1.5 text-[11px] font-semibold text-red-500 transition hover:bg-red-100 hover:border-red-200 hover:text-red-600">
                        <i class="fas fa-sign-out-alt text-[9px]"></i>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-4 sm:p-6 lg:p-7">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4500)" class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-teal-50/50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check text-xs"></i>
                    </span>
                    <span class="flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-300 hover:text-emerald-500 transition"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-5 flex items-center gap-3 rounded-xl border border-red-200/80 bg-gradient-to-r from-red-50 to-rose-50/50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation text-xs"></i>
                    </span>
                    <span class="flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-300 hover:text-red-500 transition"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200/80 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="mb-1.5 flex items-center gap-2 font-semibold"><i class="fas fa-exclamation-triangle text-xs"></i> Terjadi Kesalahan</p>
                    <ul class="ml-5 list-disc space-y-0.5 text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

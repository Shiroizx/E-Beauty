@extends('layouts.app')

@section('title', 'Lacak Pesanan — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <h1 class="text-3xl font-bold tracking-tight text-brand-900 sm:text-4xl">Lacak Pesanan Anda</h1>
        <p class="mt-4 text-lg text-neutral-500">Masukkan nomor resi atau nomor pesanan Anda untuk mengetahui status pengiriman terkini.</p>
    </div>

    <div class="mt-10 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-xl shadow-brand-200/20">
        <div class="p-8">
            <form action="{{ route('track.search') }}" method="POST" class="flex flex-col gap-4 sm:flex-row">
                @csrf
                <div class="flex-1">
                    <label for="order_number" class="sr-only">Nomor Pesanan / Resi</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <i class="fas fa-search text-neutral-400"></i>
                        </div>
                        <input type="text" name="order_number" id="order_number" class="input-brand block w-full pl-11 py-3 text-lg" placeholder="Contoh: EB-20260407-XXXXXX" required>
                    </div>
                </div>
                <button type="submit" class="btn-brand px-8 py-3 text-lg sm:w-auto">
                    Lacak
                </button>
            </form>
            
            @if(session('error'))
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif
        </div>
        <div class="bg-brand-50/50 px-8 py-6 border-t border-brand-100">
            <h3 class="text-sm font-semibold text-brand-800">Pertanyaan Umum</h3>
            <ul class="mt-4 space-y-3 text-sm text-neutral-600">
                <li class="flex gap-3">
                    <i class="fas fa-check-circle text-brand-400 mt-0.5"></i>
                    <span>Nomor pesanan dapat ditemukan pada email konfirmasi atau di halaman Riwayat Pesanan Anda.</span>
                </li>
                <li class="flex gap-3">
                    <i class="fas fa-check-circle text-brand-400 mt-0.5"></i>
                    <span>Status pengiriman mungkin membutuhkan waktu hingga 1x24 jam untuk diperbarui di sistem pihak kurir.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
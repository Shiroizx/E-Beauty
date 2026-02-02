@extends('layouts.admin')

@section('title', 'Manajemen Promo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-percent me-2"></i> Manajemen Promo</h2>
        <a href="{{ route('admin.promos.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i> Buat Promo Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Promo</th>
                            <th>Kode</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promos as $promo)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $promo->name }}</div>
                                    <small class="text-muted">{{ Str::limit($promo->description, 30) }}</small>
                                </td>
                                <td><span class="badge bg-dark font-monospace">{{ $promo->code }}</span></td>
                                <td>
                                    @if($promo->discount_type === 'percentage')
                                        <span class="text-success fw-bold">{{ $promo->discount_value }}%</span>
                                    @else
                                        <span class="text-success fw-bold">Rp {{ number_format($promo->discount_value, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    {{ $promo->used_count }}
                                    @if($promo->usage_limit)
                                        / {{ $promo->usage_limit }}
                                    @endif
                                </td>
                                <td>
                                    @if($promo->is_active && $promo->end_date->isFuture())
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif(!$promo->is_active)
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @else
                                        <span class="badge bg-dark">Expired</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.promos.edit', $promo->id) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus promo ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada promo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                {{ $promos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

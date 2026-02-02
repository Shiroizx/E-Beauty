@extends('layouts.admin')

@section('title', 'Moderasi Review')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-star me-2"></i> Moderasi Review</h2>
        <div class="btn-group">
            <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" class="btn btn-outline-secondary {{ $status == 'all' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ $status == 'pending' ? 'active' : '' }}">Menunggu persetujuan</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="btn btn-outline-success {{ $status == 'approved' ? 'active' : '' }}">Disetujui</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 25%;">Produk</th>
                            <th style="width: 15%;">User</th>
                            <th style="width: 15%;">Rating</th>
                            <th style="width: 30%;">Komentar</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ Str::limit($review->product->name, 40) }}</div>
                                    <small class="text-muted">{{ $review->created_at->format('d M Y H:i') }}</small>
                                </td>
                                <td>
                                    {{ $review->user->name }}
                                    @if($review->is_verified_purchase)
                                        <div class="badge bg-info text-white" style="font-size: 0.6rem;">Verified</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="rating-stars text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </span>
                                </td>
                                <td>
                                    <p class="mb-0 small fst-italic">"{{ Str::limit($review->comment, 100) }}"</p>
                                </td>
                                <td class="text-end pe-4">
                                    @if(!$review->is_approved)
                                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus review ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete/Reject">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Tidak ada review ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

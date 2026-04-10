@extends('layouts.admin')

@section('title', 'Pengguna — Super Admin Skinbae.ID')
@section('page_title', 'Pengguna')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Manajemen pengguna</h2>
            <p class="mt-0.5 text-sm text-gray-400">Role: super admin, admin, pelanggan</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
            <i class="fas fa-plus text-xs"></i> Tambah pengguna
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Nama</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Email</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Role</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3 font-semibold text-gray-800">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-3 py-3">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex rounded-full bg-violet-50 px-2 py-0.5 text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-200/60">Super Admin</span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200/60">Admin</span>
                                @else
                                    <span class="inline-flex rounded-full bg-stone-100 px-2 py-0.5 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-stone-200/60">Pelanggan</span>
                                @endif
                            </td>
                            <td class="py-3 pl-3 pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-500" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500" title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-sm text-gray-400">Tidak ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $users->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

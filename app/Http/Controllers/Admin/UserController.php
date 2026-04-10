<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'phone' => $request->validated('phone'),
            'address' => $request->validated('address'),
        ]);
        $user->role = $request->validated('role');
        $user->save();

        ActivityLogger::log(
            'user.created',
            'Membuat pengguna: '.$user->email.' ('.$user->role.')',
            User::class,
            $user->id,
            ['email' => $user->email, 'role' => $user->role]
        );

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $newRole = $request->validated('role');

        if ($user->isSuperAdmin() && $newRole !== 'super_admin') {
            $superCount = User::where('role', 'super_admin')->count();
            if ($superCount <= 1) {
                return back()->withInput()->with('error', 'Tidak dapat mengubah role satu-satunya Super Admin.');
            }
        }

        $before = ['role' => $user->role, 'email' => $user->email];

        $user->fill([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'address' => $request->validated('address'),
        ]);

        if ($password = $request->validated('password')) {
            $user->password = Hash::make($password);
        }

        $user->role = $newRole;
        $user->save();

        ActivityLogger::log(
            'user.updated',
            'Memperbarui pengguna: '.$user->email,
            User::class,
            $user->id,
            ['before' => $before, 'after' => ['role' => $user->role, 'email' => $user->email]]
        );

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isSuperAdmin()) {
            $superCount = User::where('role', 'super_admin')->count();
            if ($superCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus satu-satunya Super Admin.');
            }
        }

        $snapshot = ['email' => $user->email, 'role' => $user->role];
        $userId = $user->id;

        $user->delete();

        ActivityLogger::log(
            'user.deleted',
            'Menghapus pengguna: '.$snapshot['email'],
            User::class,
            $userId,
            $snapshot
        );

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}

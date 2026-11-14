<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users (Super Admin only).
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Hanya Super Admin yang berhak mengelola akun admin.');

        $query = User::latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $isActive = $request->input('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'active' => User::where('is_active', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create(): View
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('admin.users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['nullable', 'string', 'max:32', 'unique:users,nip'],
            'role' => ['required', 'string', 'in:admin,super_admin'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini telah digunakan oleh akun lain.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'role.in' => 'Peran akun tidak valid.',
            'password.required' => 'Kata sandi akun wajib dibuat.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('admin_user_created', $user, [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun admin "' . $user->name . '" berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $user): View
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:32', Rule::unique('users', 'nip')->ignore($user->id)],
            'role' => ['required', 'string', 'in:admin,super_admin'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $currentUser = $request->user();
        $targetIsSoleSuperAdmin = ($user->role === 'super_admin' && User::where('role', 'super_admin')->where('is_active', true)->count() <= 1);

        // Jangan izinkan menurunkan role Super Admin terakhir
        if ($targetIsSoleSuperAdmin && $validated['role'] !== 'super_admin') {
            return back()
                ->withInput(collect($validated)->except(['password', 'password_confirmation'])->all())
                ->with('error', 'Tidak dapat mengubah peran Super Admin terakhir. Sistem wajib memiliki minimal satu Super Admin aktif.');
        }

        // Jangan izinkan menonaktifkan Super Admin terakhir
        $newIsActive = $request->boolean('is_active', true);
        if ($targetIsSoleSuperAdmin && ! $newIsActive) {
            return back()
                ->withInput(collect($validated)->except(['password', 'password_confirmation'])->all())
                ->with('error', 'Tidak dapat menonaktifkan Super Admin terakhir. Sistem wajib memiliki minimal satu Super Admin aktif.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nip = $validated['nip'] ?? null;
        $user->role = $validated['role'];
        $user->is_active = $newIsActive;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLogger::log('admin_user_updated', $user, [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun admin "' . $user->name . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir pada sistem.');
        }

        $userName = $user->name;
        $userId = $user->id;

        // Data historis wajib dipertahankan: alihkan kepemilikan pertemuan ke Super Admin
        // yang melakukan penghapusan sebelum akun dihapus (menghindari cascade delete).
        $reassignedMeetings = $user->createdMeetings()->withTrashed()->count();
        if ($reassignedMeetings > 0) {
            $user->createdMeetings()->withTrashed()->update(['created_by' => $request->user()->id]);
        }

        AuditLogger::log('admin_user_deleted', null, [
            'target_user_id' => $userId,
            'name' => $userName,
            'email' => $user->email,
            'meetings_reassigned' => $reassignedMeetings,
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun admin "' . $userName . '" berhasil dihapus.');
    }

    /**
     * Toggle active state of admin user.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($user->role === 'super_admin' && $user->is_active && User::where('role', 'super_admin')->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Tidak dapat menonaktifkan Super Admin terakhir.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        AuditLogger::log('admin_user_status_toggled', $user, [
            'user_id' => $user->id,
            'is_active' => $user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}

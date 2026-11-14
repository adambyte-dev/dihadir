<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display settings page (Profile & System Configuration).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'profil');
        $avatarStyles = \App\Services\AvatarService::getStyles();

        $systemSettings = [
            'school_name' => SystemSetting::get('school_name', 'SMK Negeri 1 Majalengka'),
            'school_address' => SystemSetting::get('school_address', 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401'),
            'school_phone' => SystemSetting::get('school_phone', '(0233) 281484'),
            'school_email' => SystemSetting::get('school_email', 'info@smkn1majalengka.sch.id'),
            'headmaster_name' => SystemSetting::get('headmaster_name', 'Drs. H. Nono Mardiono, M.Pd.'),
            'headmaster_nip' => SystemSetting::get('headmaster_nip', '19680312 199403 1 008'),
            'default_organizer' => SystemSetting::get('default_organizer', 'SMKN 1 Majalengka'),
        ];

        $serverInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Aktif (Development)' : 'Nonaktif (Production)',
            'timezone' => config('app.timezone'),
            'db_connection' => config('database.default'),
            'current_time' => now()->translatedFormat('l, d F Y - H:i:s') . ' WIB',
        ];

        return view('admin.settings.index', compact('user', 'tab', 'systemSettings', 'serverInfo', 'avatarStyles'));
    }

    /**
     * Update current authenticated admin user profile and password.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip' => ['nullable', 'string', 'max:32', Rule::unique('users', 'nip')->ignore($user->id)],
            'avatar_style' => ['nullable', 'string', Rule::in(array_keys(\App\Services\AvatarService::getStyles()))],
            'avatar_seed' => ['nullable', 'string', 'max:60'],
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'nip.unique' => 'NIP tersebut sudah digunakan oleh akun pengelola lain.',
            'avatar_style.in' => 'Gaya avatar yang dipilih tidak tersedia.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nip = filled($validated['nip'] ?? null) ? $validated['nip'] : null;
        $user->avatar_style = $validated['avatar_style'] ?? ($user->avatar_style ?: 'lorelei');
        $user->avatar_seed = $validated['avatar_seed'] ?? ($user->avatar_seed ?: $user->name);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLogger::log('profile_updated', $user, [
            'user_id' => $user->id,
            'email' => $user->email,
            'password_changed' => ! empty($validated['password']),
        ]);

        return redirect()->route('admin.pengaturan.index', ['tab' => 'profil'])
            ->with('success', 'Profil dan akun Anda berhasil diperbarui.');
    }

    /**
     * Update school institutional settings (Super Admin only).
     */
    public function updateSystem(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Pengaturan sistem hanya dapat diubah oleh Super Admin.');
        }

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:255',
            'school_phone' => 'nullable|string|max:30',
            'school_email' => 'nullable|email|max:100',
            'headmaster_name' => 'required|string|max:255',
            'headmaster_nip' => 'required|string|max:32',
            'default_organizer' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value, 'institution');
        }

        AuditLogger::log('system_settings_updated', null, $validated);

        return redirect()->route('admin.pengaturan.index', ['tab' => 'sistem'])
            ->with('success', 'Pengaturan identitas sekolah dan sistem berhasil disimpan.');
    }
}

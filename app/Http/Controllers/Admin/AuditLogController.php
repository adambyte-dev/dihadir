<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Label manusiawi untuk setiap kode aksi pada tabel audit_logs.
     */
    public const ACTION_LABELS = [
        'meeting_created' => 'Pertemuan dibuat',
        'meeting_updated' => 'Pertemuan diperbarui',
        'meeting_deleted' => 'Pertemuan dihapus',
        'meeting_duplicated' => 'Pertemuan diduplikasi',
        'form_status_toggled' => 'Status formulir diubah',
        'form_template_applied' => 'Template formulir diterapkan',
        'form_field_created' => 'Pertanyaan formulir ditambahkan',
        'form_field_updated' => 'Pertanyaan formulir diperbarui',
        'form_field_deleted' => 'Pertanyaan formulir dihapus',
        'participant_created' => 'Peserta ditambahkan',
        'participant_updated' => 'Peserta diperbarui',
        'participant_deleted' => 'Peserta dihapus',
        'participant_imported' => 'Peserta diimpor',
        'attendance_status_updated' => 'Status kehadiran diubah',
        'report_exported_pdf' => 'Laporan PDF diunduh',
        'report_exported_csv' => 'Laporan CSV diunduh',
        'profile_updated' => 'Profil akun diperbarui',
        'system_settings_updated' => 'Pengaturan sistem diperbarui',
        'admin_user_created' => 'Akun admin dibuat',
        'admin_user_updated' => 'Akun admin diperbarui',
        'admin_user_deleted' => 'Akun admin dihapus',
        'admin_user_status_toggled' => 'Status akun admin diubah',
        'meeting_participants_synced' => 'Peserta diundang ke pertemuan',
        'meeting_participants_invited' => 'Peserta diundang ke pertemuan',
        'meeting_all_participants_invited' => 'Seluruh peserta aktif diundang',
        'meeting_participant_removed' => 'Undangan peserta dihapus dari pertemuan',
        'admin_login_succeeded' => 'Login pengelola berhasil',
        'admin_login_failed' => 'Login pengelola gagal',
        'participant_login_succeeded' => 'Login peserta berhasil',
        'participant_login_failed' => 'Login peserta gagal',
        'unauthorized_access_attempt' => 'Percobaan akses tidak sah',
    ];

    /**
     * Tampilkan jejak audit sistem (khusus Super Admin, hanya baca).
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengakses Audit Log.');

        $search = trim((string) $request->query('q', ''));
        $actionFilter = trim((string) $request->query('action', ''));

        $query = AuditLog::query()->with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Terima filter aksi apa pun yang berupa slug valid, bukan hanya yang ada di label,
        // agar aksi baru tetap dapat difilter sebelum labelnya ditambahkan.
        if ($actionFilter !== '' && preg_match('/^[a-z0-9_]{3,64}$/', $actionFilter) === 1) {
            $query->where('action', $actionFilter);
        }

        $logs = $query->latest('id')->paginate(30)->onEachSide(1)->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->mapWithKeys(fn (string $action) => [
                $action => self::ACTION_LABELS[$action] ?? str_replace('_', ' ', $action),
            ])
            ->toArray();

        $totalLogs = AuditLog::query()->count();

        return view('admin.audit.index', compact('logs', 'actions', 'search', 'actionFilter', 'totalLogs'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard overview.
     * ZERO DEMO DATA: seluruh metrik dihitung murni dari data riil di database.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Seluruh pertemuan yang boleh dikelola user ini, lengkap dengan agregat nyata.
        $meetings = Meeting::forUser($user)
            ->with(['creator'])
            ->withCount([
                'attendances as valid_attendances_count' => fn ($q) => $q->where('status', 'valid'),
                'participants as participants_count',
            ])
            ->orderByDesc('meeting_date')
            ->orderByDesc('created_at')
            ->get();

        $meetingIds = $meetings->pluck('id');

        $totalPertemuan = $meetings->count();
        $totalPeserta = Participant::where('is_active', true)->count();
        $sudahHadir = Attendance::valid()->whereIn('meeting_id', $meetingIds)->count();

        // Peserta aktif yang belum pernah hadir pada satu pun pertemuan (dihitung per orang, bukan per kejadian).
        $belumHadir = Participant::where('is_active', true)
            ->whereDoesntHave('attendances', function ($q) use ($meetingIds) {
                $q->whereIn('meeting_id', $meetingIds)->where('status', 'valid');
            })
            ->count();

        $stats = [
            'total_peserta' => $totalPeserta,
            'sudah_hadir' => $sudahHadir,
            'belum_hadir' => $belumHadir,
            'total_pertemuan' => $totalPertemuan,
        ];

        // Rata-rata kehadiran: hanya dihitung dari pertemuan yang memiliki peserta terdaftar.
        $ratedMeetings = $meetings->filter(fn (Meeting $m) => $m->participants_count > 0);

        $attendanceRate = $ratedMeetings->isNotEmpty()
            ? round($ratedMeetings->avg(fn (Meeting $m) => ($m->valid_attendances_count / $m->participants_count) * 100), 1)
            : null;

        // Laporan yang benar-benar berisi data presensi valid.
        $reportsAvailable = $meetings->where('valid_attendances_count', '>', 0)->count();

        // Tren 6 bulan terakhir berdasarkan tanggal pelaksanaan pertemuan.
        $monthlyTrend = collect(range(5, 0))->map(function (int $offset) use ($meetings) {
            $month = now()->startOfMonth()->subMonthsNoOverflow($offset);
            $key = $month->format('Y-m');

            $inMonth = $meetings->filter(
                fn (Meeting $m) => $m->meeting_date && $m->meeting_date->format('Y-m') === $key
            );

            $invited = (int) $inMonth->sum('participants_count');
            $attended = (int) $inMonth->sum('valid_attendances_count');

            return [
                'label' => $month->translatedFormat('M'),
                'full' => $month->translatedFormat('F Y'),
                'invited' => $invited,
                'attended' => $attended,
                'rate' => $invited > 0 ? round(($attended / $invited) * 100, 1) : null,
                'meetings' => $inMonth->count(),
            ];
        });

        $hasTrendData = $monthlyTrend->contains(fn (array $point) => $point['rate'] !== null);

        // Ringkasan status respons.
        $breakdownRaw = Attendance::query()
            ->whereIn('meeting_id', $meetingIds)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $breakdown = [
            'valid' => (int) ($breakdownRaw['valid'] ?? 0),
            'flagged' => (int) ($breakdownRaw['flagged'] ?? 0),
            'cancelled' => (int) ($breakdownRaw['cancelled'] ?? 0),
        ];

        $totalSubmissions = array_sum($breakdown);

        // Pertemuan yang sedang berlangsung hari ini atau formulirnya masih terbuka.
        $activeMeeting = $meetings->first(function (Meeting $m) {
            return $m->status === 'ongoing'
                || ($m->form_status === 'published' && $m->meeting_date && $m->meeting_date->isToday());
        });

        $publishedForms = $meetings->where('form_status', 'published')->count();

        $recentMeetings = $meetings->take(5);

        return view('admin.dashboard', compact(
            'stats',
            'recentMeetings',
            'attendanceRate',
            'reportsAvailable',
            'monthlyTrend',
            'hasTrendData',
            'breakdown',
            'totalSubmissions',
            'activeMeeting',
            'publishedForms',
            'ratedMeetings',
        ));
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Event, Tarian, Pelatih, Galeri, Kehadiran, PendaftaranTari};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic Stats
        $stats = [
            'anggota'     => User::where('role','anggota')->count(),
            'event'       => Event::count(),
            'tarian'      => Tarian::count(),
            'pelatih'     => Pelatih::where('aktif', true)->count(),
            'galeri'      => Galeri::count(),
            'event_mendatang' => Event::mendatang()->count(),
        ];

        // Weekly Attendance Stats (last 7 days)
        $weekStart = Carbon::now()->subDays(7)->startOfDay();
        $weeklyAttendance = Kehadiran::where('tanggal', '>=', $weekStart)->count();
        $weeklyHadir = Kehadiran::where('tanggal', '>=', $weekStart)->where('status', 'hadir')->count();

        // Recent Activity Feed
        $recentEvents  = Event::orderByDesc('created_at')->limit(5)->get()->map(function($e) {
            $e->type = 'event';
            $e->icon = '📅';
            $e->color = '#2563EB';
            return $e;
        });

        $recentAnggota = User::where('role','anggota')->orderByDesc('created_at')->limit(5)->get()->map(function($u) {
            $u->type = 'anggota';
            $u->icon = '👤';
            $u->color = '#16A34A';
            return $u;
        });

        $recentAttendance = Kehadiran::with(['user', 'jadwal'])
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get()
            ->map(function($a) {
                $a->type = 'attendance';
                $a->icon = $a->status === 'hadir' ? '✓' : '✗';
                $a->color = $a->status === 'hadir' ? '#16A34A' : '#DC2626';
                return $a;
            });

        // Merge and sort by date
        $activities = $recentEvents
            ->concat($recentAnggota)
            ->concat($recentAttendance)
            ->sortByDesc(function($item) {
                return $item->created_at ?? $item->tanggal ?? now();
            })
            ->take(8);

        $recentEvents  = Event::orderByDesc('tanggal')->limit(5)->get();
        $recentAnggota = User::where('role','anggota')->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard.index', compact(
            'stats','recentEvents','recentAnggota','activities','weeklyAttendance','weeklyHadir'
        ));
    }
}
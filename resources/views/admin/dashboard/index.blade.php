@extends('admin.layouts.app')
@section('title','Dashboard')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ Auth::user()->name }}! Berikut ringkasan aktivitas sanggar.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.event.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Event
        </a>
    </div>
</div>

{{-- STAT CARDS with Attendance Highlight --}}
<div class="stat-grid" style="margin-bottom: 24px;">
    <div class="stat-card" onclick="location.href='{{ route('admin.anggota.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--orange">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $stats['anggota'] }}</div>
            <div class="stat-label">Total Anggota</div>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('admin.kehadiran.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $weeklyHadir }}</div>
            <div class="stat-label">Hadir Minggu Ini</div>
            <div style="font-size:.7rem;color:var(--muted);margin-top:2px">dari {{ $weeklyAttendance }} total</div>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('admin.event.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $stats['event_mendatang'] }}</div>
            <div class="stat-label">Event Mendatang</div>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('admin.tarian.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $stats['tarian'] }}</div>
            <div class="stat-label">Arsip Tarian</div>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('admin.profil.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $stats['pelatih'] }}</div>
            <div class="stat-label">Pelatih Aktif</div>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('admin.galeri.index') }}'" style="cursor:pointer">
        <div class="stat-icon stat-icon--yellow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $stats['galeri'] }}</div>
            <div class="stat-label">Foto & Media</div>
        </div>
    </div>
</div>

{{-- Quick Actions + Activity Feed --}}
<div class="dash-grid-aside" style="display:grid;grid-template-columns:1fr 1.5fr;gap:20px;margin-bottom:28px">

    {{-- QUICK ACTIONS --}}
    <div class="card">
        <div class="card-header"><span class="card-title">⚡ Quick Actions</span></div>
        <div class="card-body" style="padding:16px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <a href="{{ route('admin.kehadiran.input') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border-color:#86EFAC">
                    <div class="qa-icon" style="background:#16A34A">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <span>Input Kehadiran</span>
                </a>
                <a href="{{ route('admin.event.create') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-color:#93C5FD">
                    <div class="qa-icon" style="background:#2563EB">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <span>Tambah Event</span>
                </a>
                <a href="{{ route('admin.galeri.index') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#FDF4FF,#FAE8FF);border-color:#E879F9">
                    <div class="qa-icon" style="background:#A855F7">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <span>Upload Foto</span>
                </a>
                <a href="{{ route('admin.tarian.create') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#FFF7ED,#FFEDD5);border-color:#FDBA74">
                    <div class="qa-icon" style="background:#EA580C">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    <span>Tambah Tarian</span>
                </a>
                <a href="{{ route('admin.anggota.create') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A);border-color:#FCD34D">
                    <div class="qa-icon" style="background:#D97706">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </div>
                    <span>Tambah Anggota</span>
                </a>
                <a href="{{ route('admin.profil.index') }}" class="quick-action-btn" style="background:linear-gradient(135deg,#F5F3FF,#EDE9FE);border-color:#C4B5FD">
                    <div class="qa-icon" style="background:#7C3AED">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <span>Edit Profil</span>
                </a>
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITY FEED --}}
    <div class="card">
        <div class="card-header"><span class="card-title">📋 Aktivitas Terbaru</span></div>
        <div class="card-body" style="padding:0;max-height:320px;overflow-y:auto">
            @forelse($activities as $activity)
            <div class="activity-item">
                <div class="activity-icon" style="background:{{ $activity->color }}20;color:{{ $activity->color }}">
                    {{ $activity->icon }}
                </div>
                <div class="activity-content">
                    @if($activity->type === 'event')
                        <p class="activity-text"><strong>{{ $activity->name ?? $activity->nama }}</strong> ditambahkan</p>
                        <p class="activity-meta">{{ $activity->tanggal->format('d M Y') }} · {{ $activity->lokasi ?? '-' }}</p>
                    @elseif($activity->type === 'anggota')
                        <p class="activity-text"><strong>{{ $activity->name }}</strong> mendaftar sebagai anggota</p>
                        <p class="activity-meta">{{ $activity->created_at->diffForHumans() }}</p>
                    @elseif($activity->type === 'attendance')
                        <p class="activity-text"><strong>{{ $activity->user->name ?? 'User' }}</strong> - {{ $activity->status === 'hadir' ? 'Hadir' : 'Tidak Hadir' }}</p>
                        <p class="activity-meta">{{ $activity->tanggal->format('d M Y') }} · {{ $activity->jadwal->hari ?? '-' }}</p>
                    @endif
                </div>
                <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:40px;color:var(--muted)">
                <p>Belum ada aktivitas</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- RECENT EVENTS & ANGGOTA --}}
<div class="dash-grid-half" style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    {{-- RECENT ANGGOTA --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Anggota Terbaru</span>
            <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <tbody>
                    @forelse($recentAnggota as $a)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:.8rem;background:var(--primary)">{{ strtoupper(substr($a->name,0,1)) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.875rem">{{ $a->name }}</div>
                                    <div style="font-size:.75rem;color:var(--muted)">{{ $a->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="chip {{ $a->status==='aktif' ? 'chip--green' : 'chip--gray' }}">{{ ucfirst($a->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center;color:var(--muted);padding:20px">Belum ada anggota</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RECENT EVENTS --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 Event Terkini</span>
            <a href="{{ route('admin.event.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentEvents as $ev)
                    <tr>
                        <td style="font-weight:600;font-size:.875rem">{{ $ev->nama }}</td>
                        <td style="font-size:.8rem">{{ $ev->tanggal->format('d M Y') }}</td>
                        <td><span class="chip {{ $ev->status==='selesai' ? 'chip--green' : 'chip--orange' }}">{{ $ev->status==='selesai' ? 'Selesai' : 'Mendatang' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px">Belum ada event</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Quick Action Buttons */
.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 12px;
    border: 1.5px solid;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
    text-align: center;
}
.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.quick-action-btn span {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text);
}
.qa-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Activity Feed */
.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.activity-item:hover {
    background: var(--bg);
}
.activity-item:last-child {
    border-bottom: none;
}
.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.activity-content {
    flex: 1;
    min-width: 0;
}
.activity-text {
    font-size: 0.875rem;
    color: var(--text);
    margin: 0;
    line-height: 1.4;
}
.activity-meta {
    font-size: 0.75rem;
    color: var(--muted);
    margin: 2px 0 0;
}
.activity-time {
    font-size: 0.7rem;
    color: var(--muted);
    white-space: nowrap;
}

@media (max-width: 1024px) {
    .dash-grid-aside,
    .dash-grid-half {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .quick-action-btn { padding: 12px 10px; }
    .quick-action-btn span { font-size: .7rem; }
    .qa-icon { width: 36px; height: 36px; }

    .activity-item { padding: 12px 14px; gap: 10px; }
    .activity-text { font-size: .8rem; }
    .activity-time { display: none; }
}
</style>
@endsection

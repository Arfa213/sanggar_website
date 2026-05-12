@extends('admin.layouts.app')
@section('title', 'Ringkasan Laporan')

@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Ringkasan Laporan</h1>
        <p style="color:var(--muted);font-size:.875rem">Pantau aktivitas terbaru di Sanggar Mulya Bhakti.</p>
    </div>
    <div style="display:flex;gap:12px">
        <form method="GET" style="display:flex;gap:8px;align-items:center;background:#fff;padding:4px 12px;border-radius:50px;border:1px solid var(--border)">
            <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()" style="border:none;outline:none;font-size:.875rem;font-weight:700;color:var(--dark)">
        </form>
        <a href="{{ route('admin.kehadiran.index') }}" style="background:var(--bg);color:var(--dark);border:1px solid var(--border);padding:10px 20px;border-radius:50px;font-size:.875rem;font-weight:700;text-decoration:none">
            ← Kembali
        </a>
    </div>
</div>

{{-- REKAP PER ANGGOTA --}}
@if($rekap->count())
<div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:28px">
    <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5">
        <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">📊 Persentase Kehadiran Anggota ({{ \Carbon\Carbon::parse($bulan)->isoFormat('MMMM YYYY') }})</h3>
    </div>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="background:#FAFAF8">
                <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A">NAMA ANGGOTA</th>
                <th style="padding:12px 20px;text-align:center;font-size:.75rem;font-weight:700;color:#7A7A7A">HADIR</th>
                <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A">GRAFIK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap->take(5) as $r) {{-- Hanya ambil 5 teratas untuk ringkasan --}}
            <tr style="border-top:1px solid #F5F3F1">
                <td style="padding:14px 20px">
                    <div style="font-weight:600;font-size:.875rem">{{ $r['user']->name }}</div>
                </td>
                <td style="padding:14px 20px;text-align:center">
                    <span style="background:#E8F5E9;color:#2E7D32;font-weight:700;font-size:.8rem;padding:4px 10px;border-radius:20px">{{ $r['hadir'] }}</span>
                </td>
                <td style="padding:14px 20px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="flex:1;max-width:200px;height:6px;background:#F3F4F6;border-radius:4px;overflow:hidden">
                            <div style="height:100%;background:var(--primary);width:{{ $r['persen'] }}%;border-radius:4px"></div>
                        </div>
                        <span style="font-size:.8rem;font-weight:700">{{ $r['persen'] }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    
    {{-- AKTIVITAS ANGGOTA TERBARU --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5;display:flex;justify-content:space-between;align-items:center">
            <h3 style="font-size:.9rem;font-weight:700;color:#1A1A1A">🎭 3 Absensi Anggota Terbaru</h3>
            <a href="{{ route('admin.kehadiran.laporan.anggota') }}" style="font-size:.75rem;font-weight:700;color:var(--primary);text-decoration:none">Lihat Semua →</a>
        </div>
        <div style="padding:0">
            @forelse($kehadiranLatest as $k)
            <div style="padding:16px 24px;border-bottom:1px solid #F5F3F1;display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div style="font-weight:700;font-size:.875rem">{{ $k->user->name }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">{{ $k->tarian->nama }} · {{ $k->tanggal->format('d M') }}</div>
                </div>
                <span style="background:#E8F5E9;color:#2E7D32;font-size:.7rem;font-weight:800;padding:4px 8px;border-radius:4px">HADIR</span>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:var(--muted);font-size:.875rem">Belum ada data.</div>
            @endforelse
        </div>
    </div>

    {{-- AKTIVITAS TAMU TERBARU --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5;display:flex;justify-content:space-between;align-items:center">
            <h3 style="font-size:.9rem;font-weight:700;color:#1A1A1A">👥 3 Kunjungan Tamu Terbaru</h3>
            <a href="{{ route('admin.kehadiran.laporan.pengunjung') }}" style="font-size:.75rem;font-weight:700;color:var(--primary);text-decoration:none">Lihat Semua →</a>
        </div>
        <div style="padding:0">
            @forelse($pengunjungLatest as $p)
            <div style="padding:16px 24px;border-bottom:1px solid #F5F3F1;display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div style="font-weight:700;font-size:.875rem">{{ $p->nama }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">{{ $p->tujuan }} · {{ $p->tanggal->format('d M') }}</div>
                </div>
                <span style="font-size:.7rem;color:var(--muted)">{{ date('H:i', strtotime($p->jam)) }}</span>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:var(--muted);font-size:.875rem">Belum ada tamu.</div>
            @endforelse
        </div>
    </div>

</div>

@endsection
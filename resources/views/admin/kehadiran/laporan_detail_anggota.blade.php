@extends('admin.layouts.app')
@section('title', 'Detail Laporan Anggota')

@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Detail Laporan Anggota</h1>
        <p style="color:var(--muted)">Data absensi lengkap periode {{ \Carbon\Carbon::parse($bulan)->isoFormat('MMMM YYYY') }}</p>
    </div>
    <div style="display:flex;gap:12px">
        <a href="{{ route('admin.kehadiran.pdf', ['type' => 'anggota', 'bulan' => $bulan]) }}" class="btn btn-primary" style="background:var(--dark);color:#fff;text-decoration:none;padding:10px 20px;border-radius:50px;font-weight:700;display:flex;align-items:center;gap:8px">
            📥 Export PDF
        </a>
        <a href="{{ route('admin.kehadiran.laporan') }}" class="btn btn-secondary" style="text-decoration:none;padding:10px 20px;border-radius:50px;font-weight:700">← Kembali</a>
    </div>
</div>

{{-- FILTER --}}
<div style="background:#fff;border-radius:16px;border:1px solid var(--border);padding:20px;margin-bottom:24px">
    <form method="GET" style="display:flex;gap:14px;align-items:flex-end">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <div style="flex:1">
            <label style="font-size:.75rem;font-weight:700;margin-bottom:6px;display:block">Jadwal</label>
            <select name="jadwal_id" style="width:100%;padding:10px;border-radius:10px;border:1.5px solid var(--border);outline:none">
                <option value="">Semua Jadwal</option>
                @foreach($jadwalList as $j)
                    <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>{{ $j->hari }} · {{ $j->jam_mulai }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1">
            <label style="font-size:.75rem;font-weight:700;margin-bottom:6px;display:block">Tarian</label>
            <select name="tarian_id" style="width:100%;padding:10px;border-radius:10px;border:1.5px solid var(--border);outline:none">
                <option value="">Semua Tarian</option>
                @foreach($tarianList as $t)
                    <option value="{{ $t->id }}" {{ request('tarian_id') == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1">
            <label style="font-size:.75rem;font-weight:700;margin-bottom:6px;display:block">Tipe Anggota</label>
            <select name="tipe" style="width:100%;padding:10px;border-radius:10px;border:1.5px solid var(--border);outline:none">
                <option value="">Semua Tipe</option>
                <option value="tetap" {{ request('tipe') == 'tetap' ? 'selected' : '' }}>🎭 Tetap</option>
                <option value="sementara" {{ request('tipe') == 'sementara' ? 'selected' : '' }}>🎯 Sementara</option>
            </select>
        </div>
        <button type="submit" style="background:var(--primary);color:#fff;padding:10px 24px;border-radius:50px;border:none;font-weight:700;cursor:pointer">Filter</button>
    </form>
</div>

<div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead style="background:#FAFAF8">
            <tr>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">TANGGAL</th>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">NAMA & TIPE</th>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">JADWAL / KELAS</th>
                <th style="padding:16px 24px;text-align:center;font-size:.75rem;color:var(--muted)">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kehadiran as $k)
            <tr style="border-top:1px solid #F5F3F1">
                <td style="padding:16px 24px;font-size:.875rem">{{ $k->tanggal->format('d/m/Y') }}</td>
                <td style="padding:16px 24px">
                    <div style="font-weight:700;font-size:.875rem">{{ $k->user->name }}</div>
                    <span style="font-size:.65rem;font-weight:800;color:{{ $k->user->tipe_anggota === 'tetap' ? '#2E7D32' : '#7C3AED' }};text-transform:uppercase">
                        {{ $k->user->tipe_anggota === 'tetap' ? '• Tetap' : '• Sementara' }}
                    </span>
                </td>
                <td style="padding:16px 24px;font-size:.875rem;color:var(--muted)">{{ $k->tarian->nama }} ({{ $k->jadwal->hari }})</td>
                <td style="padding:16px 24px;text-align:center">
                    <span style="background:#E8F5E9;color:#2E7D32;font-size:.7rem;font-weight:800;padding:4px 10px;border-radius:50px">{{ strtoupper($k->status) }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:40px;text-align:center;color:var(--muted)">Data tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #F5F3F1">
        {{ $kehadiran->links() }}
    </div>
</div>

@endsection

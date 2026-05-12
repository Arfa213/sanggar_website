@extends('admin.layouts.app')
@section('title', 'Detail Laporan Pengunjung')

@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Detail Laporan Pengunjung</h1>
        <p style="color:var(--muted)">Data tamu lengkap periode {{ \Carbon\Carbon::parse($bulan)->isoFormat('MMMM YYYY') }}</p>
    </div>
    <div style="display:flex;gap:12px">
        <a href="{{ route('admin.kehadiran.pdf', ['type' => 'pengunjung', 'bulan' => $bulan]) }}" class="btn btn-primary" style="background:#1565C0;color:#fff;text-decoration:none;padding:10px 20px;border-radius:50px;font-weight:700;display:flex;align-items:center;gap:8px">
            📥 Export PDF Tamu
        </a>
        <a href="{{ route('admin.kehadiran.laporan') }}" class="btn btn-secondary" style="text-decoration:none;padding:10px 20px;border-radius:50px;font-weight:700">← Kembali</a>
    </div>
</div>

<div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead style="background:#FAFAF8">
            <tr>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">WAKTU</th>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">NAMA TAMU</th>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">NO. HP</th>
                <th style="padding:16px 24px;text-align:left;font-size:.75rem;color:var(--muted)">TUJUAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengunjung as $p)
            <tr style="border-top:1px solid #F5F3F1">
                <td style="padding:16px 24px;font-size:.875rem">
                    <div style="font-weight:700">{{ $p->tanggal->format('d/m/Y') }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">{{ date('H:i', strtotime($p->jam)) }} WIB</div>
                </td>
                <td style="padding:16px 24px;font-weight:700;font-size:.875rem;color:#1565C0">{{ $p->nama }}</td>
                <td style="padding:16px 24px;font-size:.875rem;color:var(--muted)">{{ $p->no_hp ?? '-' }}</td>
                <td style="padding:16px 24px;font-size:.875rem;color:#444">{{ $p->tujuan }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:40px;text-align:center;color:var(--muted)">Belum ada data tamu.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 24px;border-top:1px solid #F5F3F1">
        {{ $pengunjung->links() }}
    </div>
</div>

@endsection

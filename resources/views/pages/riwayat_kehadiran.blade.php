@extends('layouts.member')

@section('title', 'Riwayat Kehadiran')

@section('content')
{{-- PAGE HEADER --}}
<div class="m-page-header">
    <span class="m-badge">Rekap Pribadi</span>
    <h1>Riwayat Kehadiran</h1>
    <p>Catatan kehadiran latihan kamu secara keseluruhan.</p>
</div>

<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}"
        style="display:inline-flex;align-items:center;gap:8px;color:var(--primary);font-size:.875rem;font-weight:700;text-decoration:none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Dashboard
    </a>
</div>

@if($kehadiran->isEmpty())
<div style="background:#fff;border-radius:20px;border:1px solid var(--border);padding:80px 40px;text-align:center;box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
    <div style="font-size:4rem;margin-bottom:20px;filter: grayscale(1);opacity:0.3">📋</div>
    <h3 style="font-weight:800;color:var(--dark);font-size:1.25rem;">Belum ada riwayat kehadiran</h3>
    <p style="color:var(--muted);font-size:.9rem;margin-top:8px;max-width:400px;margin-inline:auto;">Kehadiran akan tercatat otomatis di sini setelah Anda melakukan scan QR atau admin mencatat absensi.</p>
</div>
@else

<div style="background:#fff;border-radius:20px;border:1px solid var(--border);overflow:hidden;box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
    <div style="padding:20px 28px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fcfcfc;">
        <span style="font-weight:800;color:var(--dark);font-size:0.9rem;">Total: {{ $kehadiran->total() }} Sesi Latihan</span>
    </div>

    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr style="background:#fafafa">
                    <th style="padding:16px 28px;text-align:left;font-size:.7rem;font-weight:800;color:var(--muted);letter-spacing:1px;text-transform:uppercase;">TANGGAL</th>
                    <th style="padding:16px 28px;text-align:left;font-size:.7rem;font-weight:800;color:var(--muted);letter-spacing:1px;text-transform:uppercase;">TARIAN</th>
                    <th style="padding:16px 28px;text-align:left;font-size:.7rem;font-weight:800;color:var(--muted);letter-spacing:1px;text-transform:uppercase;">JADWAL</th>
                    <th style="padding:16px 28px;text-align:center;font-size:.7rem;font-weight:800;color:var(--muted);letter-spacing:1px;text-transform:uppercase;">STATUS</th>
                    <th style="padding:16px 28px;text-align:left;font-size:.7rem;font-weight:800;color:var(--muted);letter-spacing:1px;text-transform:uppercase;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kehadiran as $k)
                @php
                    $chip = [
                        'hadir' => ['bg'=>'#E8F5E9','color'=>'#2E7D32','label'=>'✓ Hadir'],
                        'izin'  => ['bg'=>'#FFF3E0','color'=>'#E65100','label'=>'~ Izin'],
                        'alpa'  => ['bg'=>'#FEF2F2','color'=>'#DC2626','label'=>'✗ Alpa'],
                    ][$k->status] ?? ['bg'=>'#F3F4F6','color'=>'#6B7280','label'=>$k->status];
                @endphp
                <tr style="border-top:1px solid #f5f5f5; transition: background .2s;">
                    <td style="padding:18px 28px;font-size:.875rem;white-space:nowrap;color:var(--dark);font-weight:500;">
                        {{ $k->tanggal->isoFormat('D MMM YYYY') }}
                    </td>
                    <td style="padding:18px 28px;font-size:.9rem;font-weight:700;color:var(--dark)">
                        {{ $k->tarian->nama }}
                    </td>
                    <td style="padding:18px 28px;font-size:.85rem;color:var(--muted);font-weight:500;">
                        {{ $k->jadwal->hari }} · <span style="color:var(--dark)">{{ $k->jadwal->jam_mulai }}–{{ $k->jadwal->jam_selesai }}</span>
                    </td>
                    <td style="padding:18px 28px;text-align:center">
                        <span style="background:{{ $chip['bg'] }};color:{{ $chip['color'] }};font-size:.75rem;font-weight:800;padding:6px 14px;border-radius:50px;white-space:nowrap;display:inline-block;">
                            {{ $chip['label'] }}
                        </span>
                    </td>
                    <td style="padding:18px 28px;font-size:.85rem;color:var(--muted)">
                        {{ $k->keterangan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($kehadiran->hasPages())
    <div style="padding:20px 28px;border-top:1px solid var(--border);background:#fafafa;">
        {{ $kehadiran->links() }}
    </div>
    @endif
</div>
@endif

<style>
    tr:hover { background-color: #fcfcfc; }
</style>
@endsection

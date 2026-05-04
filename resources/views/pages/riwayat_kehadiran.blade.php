@extends('layouts.member')
@section('title', 'Riwayat Kehadiran')
@section('content')

<section style="padding-top:calc(var(--nav-h) + 32px);padding-bottom:60px;background:var(--bg-soft);min-height:100vh">
<div class="container">

    <div style="margin-bottom:28px">
        <span class="badge">Rekap Pribadi</span>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900;color:var(--dark)">Riwayat Kehadiran</h1>
        <p style="color:var(--muted)">Catatan kehadiran latihan kamu secara keseluruhan.</p>
    </div>

    <a href="{{ route('dashboard') }}"
        style="display:inline-flex;align-items:center;gap:6px;color:var(--primary);font-size:.875rem;font-weight:700;text-decoration:none;margin-bottom:24px">
        ← Kembali ke Dashboard
    </a>

    @if($kehadiran->isEmpty())
    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);padding:60px;text-align:center">
        <div style="font-size:3rem;margin-bottom:12px">📋</div>
        <p style="font-weight:600;color:var(--dark)">Belum ada riwayat kehadiran</p>
        <p style="color:var(--muted);font-size:.875rem;margin-top:4px">Kehadiran akan tercatat setelah admin menginput absensi sesi latihan.</p>
    </div>
    @else

    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden">
        <div style="padding:16px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span style="font-weight:700;color:var(--dark)">Total: {{ $kehadiran->total() }} sesi</span>
        </div>

        <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#FAFAF8">
                    <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A;letter-spacing:.5px">TANGGAL</th>
                    <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A;letter-spacing:.5px">TARIAN</th>
                    <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A;letter-spacing:.5px">JADWAL</th>
                    <th style="padding:12px 20px;text-align:center;font-size:.75rem;font-weight:700;color:#7A7A7A;letter-spacing:.5px">STATUS</th>
                    <th style="padding:12px 20px;text-align:left;font-size:.75rem;font-weight:700;color:#7A7A7A;letter-spacing:.5px">KETERANGAN</th>
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
                <tr style="border-top:1px solid #F5F3F1">
                    <td style="padding:12px 20px;font-size:.875rem;white-space:nowrap">
                        {{ $k->tanggal->isoFormat('D MMM YYYY') }}
                    </td>
                    <td style="padding:12px 20px;font-size:.875rem;font-weight:600">
                        {{ $k->tarian->nama }}
                    </td>
                    <td style="padding:12px 20px;font-size:.85rem;color:#7A7A7A">
                        {{ $k->jadwal->hari }} · {{ $k->jadwal->jam_mulai }}–{{ $k->jadwal->jam_selesai }}
                    </td>
                    <td style="padding:12px 20px;text-align:center">
                        <span style="background:{{ $chip['bg'] }};color:{{ $chip['color'] }};font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:20px;white-space:nowrap">
                            {{ $chip['label'] }}
                        </span>
                    </td>
                    <td style="padding:12px 20px;font-size:.8rem;color:#7A7A7A">
                        {{ $k->keterangan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        @if($kehadiran->hasPages())
        <div style="padding:12px 24px;border-top:1px solid var(--border)">
            {{ $kehadiran->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
</section>
@endsection

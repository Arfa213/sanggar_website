@extends('admin.layouts.app')
@section('title', 'Input Nilai Ujian - ' . $event->nama)

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Input Nilai Ujian: {{ $event->nama }}</h1>
        <p style="color:var(--muted);font-size:.875rem">Input nilai ujian berdasarkan kriteria penilaian Sanggar Mulya Bhakti.</p>
    </div>
    <a href="{{ route('admin.ujian.index', $event->id) }}" class="btn btn-secondary">← Kembali</a>
</div>

<form action="{{ route('admin.ujian.simpan-nilai') }}" method="POST">
    @csrf
    <input type="hidden" name="event_id" value="{{ $event->id }}">
    
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span class="card-title">Daftar Peserta Ujian Aktif</span>
            <button type="submit" class="btn btn-primary" style="background:#2E7D32;border:none;" onclick="return confirm('Simpan nilai dan kirim notifikasi hasil kelulusan ke semua peserta ini?')">
                💾 Simpan & Kirim Notifikasi Kelulusan
            </button>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-responsive">
                <table class="table" style="margin:0;min-width:1200px;width:100%">
                    <thead style="background:#F9FAFB">
                        <tr>
                            <th style="width:250px">Nama & Tarian</th>
                            <th style="width:100px;text-align:center" title="Berdasarkan absensi terhitung saat mendaftar (10%)">Kehadiran (10%)</th>
                            <th style="width:110px">Teknik (25%)</th>
                            <th style="width:110px">Hafalan (25%)</th>
                            <th style="width:110px">Ekspresi (20%)</th>
                            <th style="width:110px">Penampilan (20%)</th>
                            <th>Catatan Pelatih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peserta as $i => $p)
                        <tr style="{{ $p->rapor ? 'background:#F0FDF4' : '' }}">
                            <td>
                                <input type="hidden" name="rapor[{{$i}}][user_id]" value="{{ $p->user_id }}">
                                <input type="hidden" name="rapor[{{$i}}][tarian_id]" value="{{ $p->tarian_id }}">
                                
                                <div style="font-weight:600">{{ $p->user->name }}</div>
                                <div style="font-size:0.8rem;color:var(--muted)">{{ $p->tarian->nama }}</div>
                                
                                @if($p->rapor)
                                <div style="margin-top:6px;display:flex;gap:4px;flex-wrap:wrap">
                                    <span class="badge" style="background:{{ $p->rapor->lulus ? '#DCFCE7' : '#FEF2F2' }};color:{{ $p->rapor->lulus ? '#166534' : '#991B1B' }};font-size:0.65rem">
                                        {{ $p->rapor->lulus ? 'Lulus' : 'Tidak Lulus' }} (Akhir: {{ $p->rapor->nilai_akhir }})
                                    </span>
                                    @if($p->rapor->notif_terkirim)
                                    <span class="badge" style="background:#E0F2FE;color:#0369A1;font-size:0.65rem">✓ Notif Terkirim</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <input type="hidden" name="rapor[{{$i}}][nilai_kehadiran]" value="{{ $p->persen_kehadiran }}">
                                <div style="font-size:1.1rem;font-weight:700;color:{{ $p->persen_kehadiran >= 80 ? '#10B981' : ($p->persen_kehadiran >= 75 ? '#F59E0B' : '#EF4444') }}">
                                    {{ $p->persen_kehadiran }}%
                                </div>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_teknik]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_teknik', $p->rapor->nilai_teknik ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_hafalan]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_hafalan', $p->rapor->nilai_hafalan ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_ekspresi]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_ekspresi', $p->rapor->nilai_ekspresi ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_penampilan]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_penampilan', $p->rapor->nilai_penampilan ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <textarea name="rapor[{{$i}}][catatan]" class="form-control" rows="2" placeholder="Catatan perkembangan..." style="width:100%;font-size:0.875rem">{{ old('rapor.'.$i.'.catatan', $p->rapor->catatan ?? '') }}</textarea>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($peserta->isEmpty())
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">Belum ada peserta yang berstatus diterima untuk mengikuti ujian saat ini.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<style>
.table td { vertical-align: middle; padding: 12px 16px; }
.form-control {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    outline: none;
    transition: all .2s;
}
.form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
</style>
@endsection

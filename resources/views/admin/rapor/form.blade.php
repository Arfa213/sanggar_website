@extends('admin.layouts.app')

@section('title', 'Input Rapor - ' . $pagelaran->nama)

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Input Nilai: {{ $pagelaran->nama }}</h1>
        <p>Tanggal Event: {{ $pagelaran->tanggal->format('d M Y') }} | Periode Absensi: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal' }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.rapor.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form action="{{ route('admin.rapor.store') }}" method="POST">
    @csrf
    <input type="hidden" name="event_id" value="{{ $pagelaran->id }}">
    
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
            <span class="card-title">Daftar Anggota Tetap</span>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Simpan rapor dan kirim notifikasi FCM ke semua murid ini?')">
                💾 Simpan & Kirim Notifikasi
            </button>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-responsive">
                <table class="table" style="margin:0;min-width:1200px">
                    <thead style="background:#F9FAFB">
                        <tr>
                            <th style="width:250px">Nama & Tarian</th>
                            <th style="width:100px;text-align:center" title="Berdasarkan absensi otomatis (10%)">Kehadiran</th>
                            <th style="width:110px">Teknik (25%)</th>
                            <th style="width:110px">Hafalan (25%)</th>
                            <th style="width:110px">Ekspresi (20%)</th>
                            <th style="width:110px">Penampilan (20%)</th>
                            <th>Catatan Pelatih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anggotaTetap as $i => $anggota)
                        <tr style="{{ $anggota->rapor ? 'background:#F0FDF4' : '' }}">
                            <td>
                                <input type="hidden" name="rapor[{{$i}}][user_id]" value="{{ $anggota->id }}">
                                <input type="hidden" name="rapor[{{$i}}][tarian_id]" value="{{ $anggota->tarian_id }}">
                                
                                <div style="font-weight:600">{{ $anggota->name }}</div>
                                <div style="font-size:0.8rem;color:var(--muted)">{{ $anggota->tarian_nama }}</div>
                                
                                @if($anggota->rapor && $anggota->rapor->notif_terkirim)
                                <span class="badge" style="background:#DCFCE7;color:#166534;font-size:0.65rem;margin-top:4px">✓ Notif Terkirim</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <input type="hidden" name="rapor[{{$i}}][nilai_kehadiran]" value="{{ $anggota->persen_kehadiran }}">
                                <div style="font-size:1.1rem;font-weight:700;color:{{ $anggota->persen_kehadiran >= 80 ? '#10B981' : ($anggota->persen_kehadiran >= 50 ? '#F59E0B' : '#EF4444') }}">
                                    {{ $anggota->persen_kehadiran }}%
                                </div>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_teknik]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_teknik', $anggota->rapor->nilai_teknik ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_hafalan]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_hafalan', $anggota->rapor->nilai_hafalan ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_ekspresi]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_ekspresi', $anggota->rapor->nilai_ekspresi ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <input type="number" name="rapor[{{$i}}][nilai_penampilan]" class="form-control" style="width:100%" min="0" max="100" value="{{ old('rapor.'.$i.'.nilai_penampilan', $anggota->rapor->nilai_penampilan ?? '') }}" placeholder="0-100" required>
                            </td>
                            <td>
                                <textarea name="rapor[{{$i}}][catatan]" class="form-control" rows="2" placeholder="Catatan perkembangan..." style="width:100%">{{ old('rapor.'.$i.'.catatan', $anggota->rapor->catatan ?? '') }}</textarea>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($anggotaTetap->isEmpty())
                        <tr>
                            <td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Belum ada anggota tetap yang aktif saat ini.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<style>
.table td { vertical-align: middle; }
.form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
</style>
@endsection

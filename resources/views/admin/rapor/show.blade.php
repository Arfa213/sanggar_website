@extends('admin.layouts.app')

@section('title', 'Riwayat Rapor - ' . $murid->name)

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Riwayat Rapor Pagelaran 📊</h1>
        <p>Atas nama: <strong>{{ $murid->name }}</strong> ({{ $murid->tipe_anggota_label }})</p>
    </div>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">← Kembali ke Anggota</a>
</div>

@if($rapors->isEmpty())
<div class="card">
    <div class="card-body" style="text-align:center;padding:40px;color:var(--muted)">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:16px;opacity:0.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <p>Belum ada data rapor pagelaran untuk murid ini.</p>
    </div>
</div>
@else

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start">
    
    {{-- Kiri: List Rapor --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        @foreach($rapors as $rapor)
        <div class="card" style="border-left: 4px solid {{ $rapor->nilai_akhir >= 80 ? '#10B981' : ($rapor->nilai_akhir >= 60 ? '#3B82F6' : '#EF4444') }}">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                    <div>
                        <h3 style="margin:0;font-size:1.1rem;font-weight:700">{{ $rapor->event->nama }}</h3>
                        <p style="margin:4px 0 0;font-size:0.85rem;color:var(--muted)">{{ $rapor->event->tanggal->format('d M Y') }}</p>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:1.5rem;font-weight:800;color:{{ $rapor->nilai_akhir >= 80 ? '#10B981' : ($rapor->nilai_akhir >= 60 ? '#3B82F6' : '#EF4444') }}">{{ $rapor->nilai_akhir }}</div>
                        <span class="badge" style="background:#F3F4F6;color:#374151">{{ $rapor->predikat }}</span>
                    </div>
                </div>
                
                <div style="background:#F9FAFB;border-radius:6px;padding:12px;margin-bottom:12px;display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:0.85rem">
                    <div style="display:flex;justify-content:space-between"><span>Tarian:</span> <strong>{{ $rapor->tarian->nama }}</strong></div>
                    <div style="display:flex;justify-content:space-between"><span>Kehadiran:</span> <strong>{{ $rapor->nilai_kehadiran }}%</strong></div>
                    <div style="display:flex;justify-content:space-between"><span>Teknik:</span> <strong>{{ $rapor->nilai_teknik }}</strong></div>
                    <div style="display:flex;justify-content:space-between"><span>Hafalan:</span> <strong>{{ $rapor->nilai_hafalan }}</strong></div>
                    <div style="display:flex;justify-content:space-between"><span>Ekspresi:</span> <strong>{{ $rapor->nilai_ekspresi }}</strong></div>
                    <div style="display:flex;justify-content:space-between"><span>Penampilan:</span> <strong>{{ $rapor->nilai_penampilan }}</strong></div>
                </div>
                
                @if($rapor->catatan)
                <div style="font-size:0.85rem;color:#4B5563;font-style:italic">
                    "{{ $rapor->catatan }}"
                </div>
                @endif
                <div style="margin-top:10px;font-size:0.75rem;color:#9CA3AF;text-align:right">
                    Dinilai oleh: {{ $rapor->pelatih->name }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Kanan: Grafik --}}
    <div class="card" style="position:sticky;top:24px">
        <div class="card-header"><span class="card-title">Grafik Perkembangan Nilai Akhir</span></div>
        <div class="card-body">
            <canvas id="raporChart" height="250"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('raporChart').getContext('2d');
    
    const labels = [
        @foreach($rapors as $r) "{{ $r->event->nama }}", @endforeach
    ];
    
    const data = [
        @foreach($rapors as $r) {{ $r->nilai_akhir }}, @endforeach
    ];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai Akhir',
                data: data,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { borderDash: [4, 4] }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endif
@endsection

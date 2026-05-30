@extends('admin.layouts.app')

@section('title', 'Manajemen Rapor Pagelaran')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Manajemen Rapor Pagelaran 📊🎭</h1>
        <p>Pilih event pagelaran untuk menginput atau melihat nilai rapor anggota tetap.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Event Pagelaran</span>
    </div>
    <div class="card-body">
        @if($pagelarans->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--muted)">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:16px;opacity:0.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <p>Belum ada event dengan kategori <b>Pagelaran</b>.</p>
            <p style="font-size:0.875rem">Silakan buat event baru terlebih dahulu di menu <a href="{{ route('admin.event.index') }}">Event</a>.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Event Pagelaran</th>
                        <th>Tanggal</th>
                        <th>Status Event</th>
                        <th>Progres Penilaian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagelarans as $event)
                    <tr>
                        <td>
                            <strong>{{ $event->nama }}</strong>
                            @if($event->lokasi)
                            <br><span style="font-size:0.8rem;color:var(--muted)">📍 {{ $event->lokasi }}</span>
                            @endif
                        </td>
                        <td>{{ $event->tanggal->format('d M Y') }}</td>
                        <td>
                            @if($event->status === 'selesai')
                                <span class="badge" style="background:#E8F5E9;color:#2E7D32">Selesai</span>
                            @else
                                <span class="badge" style="background:#FFF3E0;color:#E65100">Akan Datang</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="flex:1;height:8px;background:#E5E7EB;border-radius:4px;overflow:hidden">
                                    @php
                                        $persen = $event->total_anggota > 0 ? ($event->total_dinilai / $event->total_anggota) * 100 : 0;
                                    @endphp
                                    <div style="height:100%;background:{{ $persen == 100 ? '#10B981' : '#3B82F6' }};width:{{ $persen }}%"></div>
                                </div>
                                <span style="font-size:0.85rem;font-weight:600">{{ $event->total_dinilai }} / {{ $event->total_anggota }}</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.rapor.form', $event->id) }}" class="btn btn-sm btn-primary">
                                @if($event->total_dinilai == 0)
                                    Input Nilai Baru
                                @else
                                    Edit / Lanjut Input
                                @endif
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

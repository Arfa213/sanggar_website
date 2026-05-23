@extends('admin.layouts.app')
@section('title','Event & Pentas')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Event & Pentas</h1>
        <p>Kelola semua event, festival, dan pentas yang diikuti sanggar.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.event.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Event
        </a>
    </div>
</div>

@if(session('wa_link'))
<script>
    window.open("{{ session('wa_link') }}", "_blank");
</script>
@endif

@if(isset($pendingEvents) && $pendingEvents->count())
<div class="card" style="margin-bottom: 24px; border: 2px solid #f59e0b;">
    <div class="card-header" style="background: #fffbeb;">
        <span class="card-title" style="color: #d97706;">⚠️ Pengajuan Event Baru (Menunggu Persetujuan)</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Event</th><th>Pengaju</th><th>No WA</th><th>Tanggal</th><th>Tipe</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingEvents as $pe)
                <tr>
                    <td style="font-weight:600">{{ $pe->nama }}</td>
                    <td>{{ $pe->nama_pengaju }}</td>
                    <td><a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pe->no_hp_pengaju)) }}" target="_blank" style="color: #10b981;">{{ $pe->no_hp_pengaju }}</a></td>
                    <td>{{ $pe->tanggal->format('d M Y') }}</td>
                    <td><span class="chip chip--purple">{{ ucfirst($pe->kategori) }}</span></td>
                    <td class="td-actions">
                        <form method="POST" action="{{ route('admin.event.approve',$pe->id) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" style="background: #10b981; border: none;" data-confirm="Setujui dan tayangkan event ini?">Setujui & Kabari via WA</button>
                        </form>
                        <form method="POST" action="{{ route('admin.event.destroy',$pe->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Tolak dan hapus pengajuan ini?">Tolak</button>
                        </form>
                    </td>
                </tr>
                @if($pe->catatan_pengaju || $pe->portofolio_link || $pe->sinopsis_link || $pe->foto_pengaju)
                <tr style="background: #f8fafc;">
                    <td colspan="6" style="padding: 10px 16px; font-size: 0.85rem; color: #475569;">
                        @if($pe->foto_pengaju)<strong>Foto Diri:</strong> <a href="{{ asset('storage/'.$pe->foto_pengaju) }}" target="_blank">Lihat Foto</a><br>@endif
                        @if($pe->portofolio_link)<strong>Logo Komunitas:</strong> <a href="{{ $pe->portofolio_link }}" target="_blank">Lihat Logo</a><br>@endif
                        @if($pe->sinopsis_link)<strong>Sinopsis:</strong> <a href="{{ $pe->sinopsis_link }}" target="_blank">Buka Dokumen</a><br>@endif
                        @if($pe->catatan_pengaju)<strong>Catatan:</strong> {{ $pe->catatan_pengaju }}@endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Event Resmi ({{ $events->total() }})</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Foto</th><th>Nama Event</th><th>Tanggal</th><th>Lokasi</th>
                    <th>Kategori</th><th>Hasil</th><th>Unggulan</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $ev)
                @php $catColor = ['internasional'=>'chip--blue','nasional'=>'chip--green','festival'=>'chip--orange','pentas'=>'chip--purple','kompetisi'=>'chip--yellow','workshop'=>'chip--purple','kelas_khusus'=>'chip--purple']; @endphp
                <tr>
                    <td>
                        @if($ev->foto)
                            <img src="{{ asset('storage/'.$ev->foto) }}" class="thumb">
                        @else
                            <div class="thumb-placeholder">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </td>
                    <td style="font-weight:600;max-width:200px">
                        {{ $ev->nama }}
                        @if($ev->is_external)<br><span style="font-size:0.75rem; color:#6366f1;">Oleh: {{ $ev->nama_pengaju }}</span>@endif
                    </td>
                    <td style="white-space:nowrap">{{ $ev->tanggal->format('d M Y') }}</td>
                    <td style="max-width:160px">{{ $ev->lokasi }}</td>
                    <td><span class="chip {{ $catColor[$ev->kategori] ?? 'chip--gray' }}">{{ ucfirst(str_replace('_', ' ', $ev->kategori)) }}</span></td>
                    <td style="max-width:120px;font-size:.8rem">{{ $ev->hasil ?? '-' }}</td>
                    <td><span class="chip {{ $ev->unggulan ? 'chip--orange' : 'chip--gray' }}">{{ $ev->unggulan ? '★ Ya' : 'Tidak' }}</span></td>
                    <td><span class="chip {{ $ev->status==='selesai' ? 'chip--green' : 'chip--orange' }}">{{ $ev->status==='selesai' ? 'Selesai' : 'Mendatang' }}</span></td>
                    <td class="td-actions">
                        <a href="{{ route('admin.event.edit',$ev->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.event.destroy',$ev->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus event '{{ $ev->nama }}'?">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <h3>Belum ada event</h3>
                        <p>Tambahkan event pertama sanggar.</p>
                        <a href="{{ route('admin.event.create') }}" class="btn btn-primary">+ Tambah Event</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 16px">{{ $events->links() }}</div>
</div>
@endsection
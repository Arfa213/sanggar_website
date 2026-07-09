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

{{-- FLASH: WA NOTIFICATION --}}
@if(session('wa_link'))
<div style="background:linear-gradient(135deg,#25D366,#128C7E);border-radius:16px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;gap:16px;box-shadow:0 8px 24px rgba(37,211,102,.2)">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <div style="flex:1">
        <div style="color:#fff;font-weight:800;font-size:1rem">{{ session('wa_name') ?? 'Event' }} telah disetujui!</div>
        <div style="color:rgba(255,255,255,.8);font-size:.85rem">Klik tombol di bawah untuk kirim notifikasi WhatsApp ke pengaju.</div>
    </div>
    <a href="{{ session('wa_link') }}" target="_blank"
       style="background:#fff;color:#25D366;padding:10px 24px;border-radius:50px;font-weight:800;text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:8px;font-size:.875rem">
        📲 Kirim Notifikasi WA
    </a>
</div>
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
                        @if($ev->kategori === 'midhang_sore')
                            <a href="{{ route('admin.ujian.index', $ev->id) }}" class="btn btn-primary btn-sm" style="background:#4f46e5;border:none;">👥 Peserta Ujian</a>
                        @endif
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
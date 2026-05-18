@extends('admin.layouts.app')
@section('title','Arsip Digital Tarian')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Arsip Digital Tarian</h1>
        <p>Kelola koleksi tarian tradisional yang ditampilkan di website.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.tarian.pdf') }}" class="btn btn-secondary" target="_blank">🖨 Cetak PDF</a>
        <a href="{{ route('admin.tarian.create') }}" class="btn btn-primary">+ Tambah Tarian</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Foto</th><th>Nama Tarian</th><th>Asal</th><th>Jenis</th><th>Kategori</th><th>Video</th><th>Unggulan</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($tarian as $t)
                @php
                    $catColor=['sakral'=>'chip--green','hiburan'=>'chip--blue','penyambutan'=>'chip--purple','ritual'=>'chip--orange','perang'=>'chip--red'];
                    $jenisColor=['tari'=>'chip--orange','gamelan'=>'chip--purple','drama'=>'chip--blue','srimpi'=>'chip--green'];
                    $jenisIcon=['tari'=>'🩰','gamelan'=>'🥁','drama'=>'🎭','srimpi'=>'🌸'];
                @endphp
                <tr>
                    <td>
                        @if($t->foto)
                            <img src="{{ asset('storage/'.$t->foto) }}" class="thumb">
                        @else
                            <div class="thumb-placeholder"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                        @endif
                    </td>
                    <td style="font-weight:600">{{ $t->nama }}</td>
                    <td style="font-size:.85rem">{{ $t->asal }}</td>
                    <td><span class="chip {{ $jenisColor[$t->jenis_kegiatan ?? 'tari'] ?? 'chip--gray' }}">{{ ($jenisIcon[$t->jenis_kegiatan ?? 'tari'] ?? '') }} {{ ucfirst($t->jenis_kegiatan ?? 'tari') }}</span></td>
                    <td><span class="chip {{ $catColor[$t->kategori] ?? 'chip--gray' }}">{{ ucfirst($t->kategori) }}</span></td>
                    <td>@if($t->video_url)<span class="chip chip--green">▶ Ada</span>@else<span class="chip chip--gray">-</span>@endif</td>
                    <td><span class="chip {{ $t->unggulan ? 'chip--orange' : 'chip--gray' }}">{{ $t->unggulan ? '★ Ya' : 'Tidak' }}</span></td>
                    <td><span class="chip {{ $t->aktif ? 'chip--green' : 'chip--gray' }}">{{ $t->aktif ? 'Tampil' : 'Tersembunyi' }}</span></td>
                    <td class="td-actions">
                        <a href="{{ route('admin.tarian.edit',$t->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.tarian.destroy',$t->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus tarian \'{{ $t->nama }}\'? Tindakan ini tidak dapat dibatalkan.')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><div class="empty-state"><h3>Belum ada tarian</h3><p>Tambahkan tarian tradisional pertama.</p><a href="{{ route('admin.tarian.create') }}" class="btn btn-primary">+ Tambah Tarian</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 16px">{{ $tarian->links() }}</div>
</div>
@endsection
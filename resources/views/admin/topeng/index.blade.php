@extends('admin.layouts.app')
@section('title','Koleksi Topeng')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Koleksi Topeng</h1>
        <p>Kelola koleksi topeng tradisional (Pancawanda) yang ditampilkan di website.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.topeng.create') }}" class="btn btn-primary">+ Tambah Topeng</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Topeng</th>
                    <th>Warna</th>
                    <th>Karakter</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topeng as $t)
                <tr>
                    <td>
                        @if($t->foto)
                            <img src="{{ asset('storage/'.$t->foto) }}" class="thumb">
                        @else
                            <div class="thumb-placeholder"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                        @endif
                    </td>
                    <td style="font-weight:600">{{ $t->nama }}</td>
                    <td><span class="chip chip--blue">{{ $t->warna }}</span></td>
                    <td>{{ $t->karakter }}</td>
                    <td><span class="chip {{ $t->aktif ? 'chip--green' : 'chip--gray' }}">{{ $t->aktif ? 'Tampil' : 'Tersembunyi' }}</span></td>
                    <td class="td-actions">
                        <a href="{{ route('admin.topeng.edit',$t->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.topeng.destroy',$t->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus topeng \'{{ $t->nama }}\'?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><h3>Belum ada data topeng</h3><p>Tambahkan koleksi topeng tradisional pertama.</p><a href="{{ route('admin.topeng.create') }}" class="btn btn-primary">+ Tambah Topeng</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 16px">{{ $topeng->links() }}</div>
</div>
@endsection

@extends('admin.layouts.app')
@section('title','Kelola Anggota')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Kelola Anggota</h1>
        <p>Daftar seluruh anggota yang terdaftar di Sanggar Mulya Bhakti.</p>
    </div>
    <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary">+ Tambah Anggota</a>
</div>

{{-- FILTER --}}
<form method="GET" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}" style="max-width:280px">
    <select name="status" class="form-control" style="max-width:160px">
        <option value="">Semua Status</option>
        <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
        <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Non-aktif</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request()->anyFilled(['search','status']))
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">Total: {{ $anggota->total() }} anggota</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Alamat</th><th>Terdaftar</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($anggota as $a)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($a->foto)
                                <img src="{{ asset('storage/'.$a->foto) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                            @else
                                <div class="user-avatar" style="width:34px;height:34px;font-size:.8rem;flex-shrink:0">{{ strtoupper(substr($a->name,0,1)) }}</div>
                            @endif
                            <span style="font-weight:600">{{ $a->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:.875rem">{{ $a->email }}</td>
                    <td style="font-size:.875rem">{{ $a->no_hp ?? '-' }}</td>
                    <td style="font-size:.8rem;max-width:160px;color:var(--muted)">{{ Str::limit($a->alamat ?? '-', 40) }}</td>
                    <td style="font-size:.8rem;white-space:nowrap">{{ $a->created_at->format('d M Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.anggota.toggle',$a->id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="chip {{ $a->status==='aktif' ? 'chip--green' : 'chip--gray' }}" style="border:none;cursor:pointer" title="Klik untuk toggle status">
                                {{ $a->status==='aktif' ? '● Aktif' : '○ Non-aktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="td-actions">
                        <a href="{{ route('admin.anggota.edit',$a->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.anggota.destroy',$a->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus anggota {{ $a->name }}? Tindakan ini tidak dapat dibatalkan.">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><h3>Tidak ada anggota ditemukan</h3><p>Coba ubah filter pencarian.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 16px">{{ $anggota->links() }}</div>
</div>
@endsection
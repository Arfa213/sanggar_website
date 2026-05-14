@extends('admin.layouts.app')

@section('title', 'Manajemen Booking Anggota Sementara')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Booking Anggota Sementara</h1>
        <p>Kelola pendaftaran sesi latihan dari calon anggota (pengunjung).</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Booking Sesi</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Pengunjung</th>
                    <th>WhatsApp</th>
                    <th>Tarian & Catatan</th>
                    <th>Tanggal Latihan</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $b->user->name }}</div>
                        <div style="font-size:.75rem; color:var(--muted)">{{ $b->user->email }}</div>
                    </td>
                    <td style="font-size:.875rem">{{ $b->user->no_hp ?? '-' }}</td>
                    <td>
                        <div style="font-weight:600">{{ $b->tarian->nama ?? 'N/A' }}</div>
                        <div style="font-size:.75rem; color:#7A7A7A">{{ $b->catatan ?? '-' }}</div>
                    </td>
                    <td style="font-size:.875rem">{{ \Carbon\Carbon::parse($b->tanggal_latihan)->format('d M Y') }}</td>
                    <td style="font-size:.875rem">{{ $b->jam_latihan }}</td>
                    <td>
                        @if($b->status === 'aktif')
                            <span class="chip chip--green">Terkonfirmasi</span>
                        @elseif($b->status === 'nonaktif')
                            <span class="chip chip--red">Ditolak</span>
                        @else
                            <span class="chip chip--purple">Menunggu</span>
                        @endif
                    </td>
                    <td class="td-actions">
                        @if($b->status !== 'aktif')
                        <form action="{{ route('admin.booking.confirm', $b->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Konfirmasi</button>
                        </form>
                        @endif
                        
                        @if($b->status !== 'nonaktif')
                        <form action="{{ route('admin.booking.reject', $b->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Tolak</button>
                        </form>
                        @endif

                        <form action="{{ route('admin.booking.destroy', $b->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data booking ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <h3>Belum ada booking</h3>
                            <p>Pendaftaran sesi latihan dari anggota sementara akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

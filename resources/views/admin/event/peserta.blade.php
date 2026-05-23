@extends('admin.layouts.app')
@section('title','Peserta Event Umum')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Peserta Event & Workshop (Umum)</h1>
        <p>Kelola pendaftaran orang luar / umum yang mengikuti event sanggar.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Pendaftar ({{ $peserta->total() }})</span>
    </div>
    
    @if(session('wa_link'))
    <script>
        window.open("{{ session('wa_link') }}", "_blank");
    </script>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Peserta</th>
                    <th>No WA</th>
                    <th>Asal Instansi</th>
                    <th>Event</th>
                    <th>Status Pembayaran</th>
                    <th>Bukti TF & ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peserta as $p)
                <tr>
                    <td style="font-weight:600">{{ $p->nama_peserta }}</td>
                    <td><a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p->no_hp)) }}" target="_blank" style="color: #10b981;">{{ $p->no_hp }}</a></td>
                    <td>{{ $p->asal_instansi ?? '-' }}</td>
                    <td>
                        <strong>{{ $p->event->nama ?? 'Event Dihapus' }}</strong><br>
                        <small style="color: #64748b;">
                            {{ $p->event ? ($p->event->is_berbayar ? 'Berbayar (Rp '.number_format($p->event->harga_tiket,0,',','.').')' : 'Gratis') : '' }}
                        </small>
                    </td>
                    <td>
                        @if($p->status_pembayaran == 'gratis') <span class="chip chip--blue">Gratis</span>
                        @elseif($p->status_pembayaran == 'menunggu_verifikasi') <span class="chip chip--orange">Menunggu Cek</span>
                        @elseif($p->status_pembayaran == 'lunas') <span class="chip chip--green">Lunas</span>
                        @else <span class="chip chip--gray">Ditolak/Batal</span>
                        @endif
                    </td>
                    <td>
                        @if($p->bukti_transfer)
                            <a href="{{ asset('storage/'.$p->bukti_transfer) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Lihat Struk</a>
                        @else
                            -
                        @endif
                        <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">{{ $p->order_id }}</div>
                    </td>
                    <td class="td-actions">
                        @if($p->status_pembayaran == 'menunggu_verifikasi')
                        <form method="POST" action="{{ route('admin.event.peserta.update', $p->id) }}" style="display:inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="status_pembayaran" value="lunas">
                            <button type="submit" class="btn btn-primary btn-sm" style="background: #10b981; border: none;" data-confirm="Set manual jadi Lunas?">✅ Lunas</button>
                        </form>
                        @elseif($p->status_pembayaran == 'lunas' || $p->status_pembayaran == 'gratis')
                            @if($p->order_id)
                            <a href="{{ route('event.tiket', $p->order_id) }}" target="_blank" class="btn btn-secondary btn-sm" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">📄 E-Tiket</a>
                            @else
                            <span class="btn btn-secondary btn-sm" style="background: #e2e8f0; color: #94a3b8; border: 1px solid #cbd5e1; cursor: not-allowed;" title="Tidak ada Order ID (Data Lama)">📄 E-Tiket</span>
                            @endif
                        @endif
                        
                        <form method="POST" action="{{ route('admin.event.peserta.destroy', $p->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus peserta ini?">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">Belum ada peserta dari luar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $peserta->links() }}</div>
</div>

@endsection

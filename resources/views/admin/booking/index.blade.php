@extends('admin.layouts.app')
@section('title', 'Booking Anggota Sementara')

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Booking Sementara</h1>
        <p style="color:var(--muted);font-size:.875rem">Kelola pendaftaran sesi latihan dari anggota sementara.</p>
    </div>
</div>

{{-- FLASH: WA NOTIFICATION --}}
@if(session('wa_link'))
<div style="background:linear-gradient(135deg,#25D366,#128C7E);border-radius:16px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;gap:16px;box-shadow:0 8px 24px rgba(37,211,102,.2)">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <div style="flex:1">
        <div style="color:#fff;font-weight:800;font-size:1rem">{{ session('wa_name') }} telah dikonfirmasi!</div>
        <div style="color:rgba(255,255,255,.8);font-size:.85rem">Klik tombol di bawah untuk kirim notifikasi WhatsApp ke anggota.</div>
    </div>
    <a href="{{ session('wa_link') }}" target="_blank"
       style="background:#fff;color:#25D366;padding:10px 24px;border-radius:50px;font-weight:800;text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:8px;font-size:.875rem">
        📲 Kirim Notifikasi WA
    </a>
</div>
@endif

{{-- STATISTIK --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    <a href="?status=pending" style="text-decoration:none">
        <div style="background:#fff;border-radius:14px;border:2px solid {{ $filterStatus==='pending'?'#f97316':'var(--border)' }};padding:20px;display:flex;align-items:center;gap:16px;transition:.2s">
            <div style="width:44px;height:44px;background:#FFF7ED;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem">⏳</div>
            <div>
                <div style="font-size:1.8rem;font-weight:900;color:#f97316">{{ $countPending }}</div>
                <div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase">Menunggu</div>
            </div>
        </div>
    </a>
    <a href="?status=aktif" style="text-decoration:none">
        <div style="background:#fff;border-radius:14px;border:2px solid {{ $filterStatus==='aktif'?'#22C55E':'var(--border)' }};padding:20px;display:flex;align-items:center;gap:16px;transition:.2s">
            <div style="width:44px;height:44px;background:#F0FDF4;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem">✅</div>
            <div>
                <div style="font-size:1.8rem;font-weight:900;color:#22C55E">{{ $countAktif }}</div>
                <div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase">Terkonfirmasi</div>
            </div>
        </div>
    </a>
    <a href="?status=ditolak" style="text-decoration:none">
        <div style="background:#fff;border-radius:14px;border:2px solid {{ $filterStatus==='ditolak'?'#EF4444':'var(--border)' }};padding:20px;display:flex;align-items:center;gap:16px;transition:.2s">
            <div style="width:44px;height:44px;background:#FEF2F2;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem">❌</div>
            <div>
                <div style="font-size:1.8rem;font-weight:900;color:#EF4444">{{ $countDitolak }}</div>
                <div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase">Ditolak</div>
            </div>
        </div>
    </a>
</div>

{{-- FILTER BAR --}}
<div style="display:flex;gap:8px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
    <span style="font-size:.875rem;font-weight:700;color:var(--muted)">Filter:</span>
    @foreach(['semua'=>'🔍 Semua','pending'=>'⏳ Menunggu','aktif'=>'✅ Terkonfirmasi','ditolak'=>'❌ Ditolak'] as $val=>$label)
    <a href="?status={{ $val }}"
       style="padding:7px 18px;border-radius:50px;font-size:.8rem;font-weight:700;text-decoration:none;transition:.2s;
              {{ $filterStatus===$val ? 'background:var(--dark);color:#fff' : 'background:#F5F3F1;color:var(--muted);border:1px solid var(--border)' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Booking Sesi</span>
        <span style="font-size:.8rem;color:var(--muted)">{{ $bookings->count() }} data ditampilkan</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>WhatsApp</th>
                    <th>Tarian</th>
                    <th>Tanggal & Jam</th>
                    <th>Kadaluarsa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td>
                        <div style="font-weight:700;color:var(--dark)">{{ $b->user->name }}</div>
                        <div style="font-size:.75rem;color:var(--muted)">{{ $b->user->email }}</div>
                        <div style="font-size:.7rem;color:var(--muted);margin-top:2px">
                            Daftar: {{ \Carbon\Carbon::parse($b->user->created_at)->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        @if($b->user->no_hp)
                            @php
                                $noHpClean = preg_replace('/\D/', '', $b->user->no_hp);
                                $noHpWa = str_starts_with($noHpClean, '0') ? '62'.substr($noHpClean,1) : $noHpClean;
                            @endphp
                            <a href="https://wa.me/{{ $noHpWa }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:5px;color:#25D366;font-weight:700;font-size:.8rem;text-decoration:none">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ $b->user->no_hp }}
                            </a>
                        @else
                            <span style="color:var(--muted);font-size:.8rem">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--dark)">{{ $b->tarian->nama ?? 'N/A' }}</span>
                        @if($b->catatan)
                            <div style="font-size:.72rem;color:var(--muted);margin-top:2px">{{ $b->catatan }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700">{{ \Carbon\Carbon::parse($b->tanggal_latihan)->format('d M Y') }}</div>
                        <div style="font-size:.8rem;color:var(--muted)">🕒 {{ $b->jam_latihan }} WIB</div>
                    </td>
                    <td>
                        @if($b->user->tgl_kadaluarsa)
                            @php $kadaluarsa = \Carbon\Carbon::parse($b->user->tgl_kadaluarsa); @endphp
                            <span style="font-size:.8rem;color:{{ $kadaluarsa->isPast() ? '#EF4444' : '#22C55E' }};font-weight:700">
                                {{ $kadaluarsa->format('d M Y') }}
                            </span>
                            @if($kadaluarsa->isPast())
                                <div style="font-size:.7rem;color:#EF4444">Sudah Lewat</div>
                            @endif
                        @else
                            <span style="color:var(--muted);font-size:.8rem">—</span>
                        @endif
                    </td>
                    <td>
                        @if($b->status === 'aktif')
                            <span class="chip chip--green">✅ Terkonfirmasi</span>
                        @elseif($b->status === 'pending')
                            <span class="chip chip--yellow" style="background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA">⏳ Menunggu</span>
                        @elseif($b->status === 'ditolak')
                            <span class="chip chip--red">❌ Ditolak</span>
                        @else
                            <span class="chip chip--gray">{{ $b->status }}</span>
                        @endif
                    </td>
                    <td class="td-actions" style="white-space:nowrap">
                        {{-- Konfirmasi --}}
                        @if($b->status !== 'aktif')
                        <form action="{{ route('admin.booking.confirm', $b->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" title="Konfirmasi booking">✅ Konfirmasi</button>
                        </form>
                        @endif

                        {{-- Tolak --}}
                        @if($b->status !== 'ditolak')
                        <form action="{{ route('admin.booking.reject', $b->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Tolak booking">❌ Tolak</button>
                        </form>
                        @endif

                        {{-- Hapus --}}
                        <form action="{{ route('admin.booking.destroy', $b->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus data booking ini?')" title="Hapus">🗑️</button>
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

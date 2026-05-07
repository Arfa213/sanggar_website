@extends('layouts.member')
@section('title', 'Dashboard Saya')

@section('content')

{{-- WELCOME HEADER --}}
<div class="m-page-header" style="display:flex;align-items:center;gap:20px;margin-bottom:30px">
    @if($user->foto)
        <img src="{{ asset('storage/'.$user->foto) }}" style="width:80px;height:80px;border-radius:20px;object-fit:cover;box-shadow:var(--shadow-md);border:3px solid #fff">
    @else
        <div style="width:80px;height:80px;background:var(--primary);color:#fff;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;box-shadow:var(--shadow-md);border:3px solid #fff;font-family:'Playfair Display',serif">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    @endif
    <div>
        <div class="m-badge">Member Area</div>
        <h1 style="margin-top:4px">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
        <p>Selamat datang di dashboard anggota Sanggar Mulya Bhakti.</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px">

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#FDF0EA;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#1A1A1A;font-family:'Playfair Display',serif;line-height:1">{{ $jadwalAktif->count() }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Kelas Aktif</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#E8F5E9;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#2E7D32;font-family:'Playfair Display',serif;line-height:1">{{ $hadir }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Hadir Bulan Ini</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        @php $pColor = $persenHadir >= 75 ? '#2E7D32' : ($persenHadir >= 50 ? '#E65100' : '#DC2626');
             $pBg    = $persenHadir >= 75 ? '#E8F5E9' : ($persenHadir >= 50 ? '#FFF3E0' : '#FEF2F2'); @endphp
        <div style="width:44px;height:44px;background:{{ $pBg }};border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $pColor }}" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $pColor }};font-family:'Playfair Display',serif;line-height:1">{{ $persenHadir }}%</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Tingkat Kehadiran</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#E8F4FD;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#1565C0;font-family:'Playfair Display',serif;line-height:1">{{ $totalLatihan }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Total Sesi Bulan Ini</div>
        </div>
    </div>

</div>

{{-- GRID UTAMA --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

<div>

    {{-- JADWAL AKTIF --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:20px">
        <div style="padding:16px 20px;border-bottom:1px solid #F0EBE5;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">LATIHAN SAYA</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700;color:#1A1A1A">Kelas yang Sedang Diikuti</h3>
            </div>
            <a href="{{ route('penjadwalan') }}"
               style="background:#C65D2E;color:#fff;font-size:.75rem;font-weight:700;padding:7px 14px;border-radius:50px;text-decoration:none;display:flex;align-items:center;gap:5px">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kelas
            </a>
        </div>

        @if($jadwalAktif->isEmpty())
        <div style="padding:40px;text-align:center">
            <div style="width:64px;height:64px;background:#FDF0EA;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
            </div>
            <p style="font-weight:600;color:#1A1A1A;margin-bottom:4px">Belum ada kelas terdaftar</p>
            <p style="font-size:.825rem;color:#7A7A7A;margin-bottom:16px">Pilih tarian yang ingin kamu pelajari dan daftarkan diri!</p>
            <a href="{{ route('penjadwalan') }}"
               style="display:inline-block;background:#C65D2E;color:#fff;font-size:.875rem;font-weight:700;padding:10px 22px;border-radius:50px;text-decoration:none">
                Pilih Kelas Tari →
            </a>
        </div>
        @else
        <div>
            @foreach($jadwalAktif as $p)
            <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #FAFAF8">
                {{-- Hari box --}}
                <div style="width:52px;height:52px;background:#C65D2E;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:#fff;font-size:.6rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase">{{ strtoupper(substr($p->jadwal->hari,0,3)) }}</span>
                    <span style="color:rgba(255,255,255,.6);font-size:.6rem;font-weight:600">{{ $p->jadwal->jam_mulai }}</span>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;font-size:.9rem;color:#1A1A1A">{{ $p->tarian->nama }}</div>
                    <div style="font-size:.78rem;color:#7A7A7A;margin-top:2px">
                        📍 {{ $p->jadwal->tempat }}
                        &nbsp;·&nbsp;
                        ⏰ {{ $p->jadwal->jam_mulai }}–{{ $p->jadwal->jam_selesai }}
                    </div>
                    <div style="font-size:.72rem;color:#ADADAD;margin-top:2px">Terdaftar {{ $p->tanggal_daftar->format('d M Y') }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                    <span style="background:#E8F5E9;color:#2E7D32;font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px">Aktif</span>
                    <form method="POST" action="{{ route('penjadwalan.batalkan', $p->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Batalkan pendaftaran Tari {{ $p->tarian->nama }}?')"
                            style="background:none;border:1px solid #FECACA;color:#DC2626;font-size:.7rem;font-weight:700;padding:4px 10px;border-radius:8px;cursor:pointer">
                            Batalkan
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ABSENSI TERAKHIR --}}
    @if($absensiTerakhir->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:20px">
        <div style="padding:16px 20px;border-bottom:1px solid #F0EBE5;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">REKAP</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700">Absensi Terakhir</h3>
            </div>
            <a href="{{ route('penjadwalan.kehadiran') }}"
               style="font-size:.78rem;color:#C65D2E;font-weight:700;text-decoration:none">
                Lihat semua →
            </a>
        </div>
        @foreach($absensiTerakhir as $ab)
        @php
            $abColor = ['hadir'=>'#2E7D32','izin'=>'#E65100','alpa'=>'#DC2626'][$ab->status] ?? '#7A7A7A';
            $abBg    = ['hadir'=>'#E8F5E9','izin'=>'#FFF3E0','alpa'=>'#FEF2F2'][$ab->status]  ?? '#F3F4F6';
            $abIcon  = ['hadir'=>'✓','izin'=>'~','alpa'=>'✗'][$ab->status] ?? '?';
        @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:11px 20px;border-bottom:1px solid #FAFAF8">
            <div style="width:32px;height:32px;background:{{ $abBg }};border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:900;color:{{ $abColor }};font-size:.9rem;flex-shrink:0">
                {{ $abIcon }}
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-size:.85rem;font-weight:600;color:#1A1A1A">{{ $ab->tarian->nama }}</div>
                <div style="font-size:.75rem;color:#7A7A7A">{{ $ab->tanggal->isoFormat('D MMM YYYY') }} · {{ $ab->jadwal->hari }}</div>
            </div>
            <span style="font-size:.75rem;font-weight:700;color:{{ $abColor }}">{{ ucfirst($ab->status) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- REKOMENDASI TARIAN --}}
    @if($tarianRekomendasi->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #F0EBE5">
            <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">UNTUK KAMU</div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700">Tarian yang Bisa Dipelajari</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px 20px">
            @foreach($tarianRekomendasi as $t)
            <a href="{{ route('penjadwalan') }}?tarian={{ $t->id }}"
               style="display:block;background:#FAFAF8;border-radius:12px;border:1px solid #F0EBE5;padding:14px;text-decoration:none;transition:all .2s"
               onmouseover="this.style.borderColor='#C65D2E';this.style.background='#FDF0EA'"
               onmouseout="this.style.borderColor='#F0EBE5';this.style.background='#FAFAF8'">
                <div style="font-size:.7rem;font-weight:700;color:#C65D2E;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">{{ ucfirst($t->kategori) }}</div>
                <div style="font-weight:700;font-size:.875rem;color:#1A1A1A">{{ $t->nama }}</div>
                <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">📍 {{ $t->asal }}</div>
                <div style="font-size:.75rem;color:#C65D2E;font-weight:700;margin-top:8px">Daftar kelas →</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- SIDEBAR KANAN --}}
<div>

    {{-- Kehadiran bulan ini --}}
    <div style="background:#C65D2E;border-radius:16px;padding:20px;color:#fff;margin-bottom:16px">
        <div style="font-size:.65rem;font-weight:700;letter-spacing:1px;opacity:.7;text-transform:uppercase;margin-bottom:10px">KEHADIRAN BULAN INI</div>
        @if($totalLatihan > 0)
        <div style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;line-height:1;margin-bottom:4px">{{ $persenHadir }}%</div>
        <div style="font-size:.8rem;opacity:.75;margin-bottom:14px">dari {{ $totalLatihan }} sesi latihan</div>
        {{-- Progress bar --}}
        <div style="height:8px;background:rgba(255,255,255,.2);border-radius:4px;overflow:hidden;margin-bottom:14px">
            <div style="height:100%;background:#fff;border-radius:4px;width:{{ $persenHadir }}%;transition:width .6s"></div>
        </div>
        <div style="display:flex;flex-direction:column;gap:7px">
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">✓ Hadir</span>
                <span style="font-weight:700">{{ $hadir }} sesi</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">~ Izin</span>
                <span style="font-weight:700">{{ $izin }} sesi</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">✗ Alpa</span>
                <span style="font-weight:700">{{ $alpa }} sesi</span>
            </div>
        </div>
        @else
        <p style="opacity:.75;font-size:.875rem;margin-top:6px">Belum ada sesi latihan bulan ini.</p>
        @endif
    </div>

    {{-- Event mendatang --}}
    @if($eventMendatang->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:16px">
        <div style="padding:14px 18px;border-bottom:1px solid #F0EBE5">
            <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">AKAN DATANG</div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700">Event Mendatang</h3>
        </div>
        @foreach($eventMendatang as $ev)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #FAFAF8">
            <div style="background:#C65D2E;color:#fff;border-radius:10px;padding:6px 10px;text-align:center;flex-shrink:0;min-width:46px">
                <div style="font-size:1.1rem;font-weight:900;line-height:1">{{ $ev->tanggal->format('d') }}</div>
                <div style="font-size:.6rem;font-weight:700;opacity:.8">{{ strtoupper($ev->tanggal->isoFormat('MMM')) }}</div>
            </div>
            <div style="min-width:0">
                <div style="font-size:.825rem;font-weight:700;color:#1A1A1A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $ev->nama }}</div>
                <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">📍 {{ Str::limit($ev->lokasi, 25) }}</div>
            </div>
        </div>
        @endforeach
        <div style="padding:10px 18px">
            <a href="{{ route('event') }}" style="font-size:.78rem;color:#C65D2E;font-weight:700;text-decoration:none">Lihat semua event →</a>
        </div>
    </div>
    @endif

    {{-- Quick actions --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid #F0EBE5">
            <h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700">Aksi Cepat</h3>
        </div>
        <div style="padding:10px 14px;display:flex;flex-direction:column;gap:6px">
            <a href="{{ route('penjadwalan') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#FDF0EA;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Daftar Kelas Baru</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Pilih tarian & jadwal</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ route('penjadwalan.kehadiran') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#E8F5E9;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Riwayat Kehadiran</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Lihat rekap absensi</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ route('digital-archive') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#E8F4FD;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Arsip Digital</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Jelajahi tarian tradisional</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>

</div>
</div>

@endsection

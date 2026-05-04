@extends('layouts.member')
@section('title', 'Penjadwalan Tari')
@section('content')

<section style="padding-top:calc(var(--nav-h) + 32px);padding-bottom:80px;background:var(--bg-soft);min-height:100vh">
<div class="container">

    {{-- HEADER --}}
    <div style="text-align:center;margin-bottom:40px">
        <span class="badge">Pendaftaran Kelas</span>
        <h1 style="font-family:var(--font-display);font-size:2.5rem;font-weight:900;color:var(--dark);margin-bottom:8px">Pilih Kelas Tari</h1>
        <p style="color:var(--muted);max-width:540px;margin:0 auto">Daftar satu atau lebih kelas tari yang ingin kamu pelajari. Jadwal akan otomatis disesuaikan.</p>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
    <div style="background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;padding:14px 20px;margin-bottom:24px;color:#15803D;display:flex;align-items:center;gap:10px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:14px 20px;margin-bottom:24px;color:#DC2626">
        {{ session('error') }}
    </div>
    @endif

    {{-- KELAS YANG SUDAH TERDAFTAR --}}
    @if($pendaftaran->count())
    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:32px">
        <div style="padding:18px 24px;border-bottom:1px solid var(--border);background:var(--bg-soft)">
            <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:700">Kelas yang Sudah Saya Ikuti</h3>
        </div>
        <div style="padding:16px 24px">
            @foreach($pendaftaran as $p)
            <div style="display:flex;align-items:center;gap:16px;padding:14px 0;border-bottom:1px solid #FAF8F6">
                <div style="width:54px;height:54px;background:var(--primary-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                <div style="flex:1">
                    <div style="font-weight:700;font-size:.95rem;color:var(--dark)">{{ $p->tarian->nama }}</div>
                    <div style="font-size:.8rem;color:var(--muted);margin-top:2px">
                        📅 {{ $p->jadwal->hari }} &nbsp;·&nbsp;
                        ⏰ {{ $p->jadwal->jam_mulai }}–{{ $p->jadwal->jam_selesai }} &nbsp;·&nbsp;
                        📍 {{ $p->jadwal->tempat }}
                    </div>
                    <div style="font-size:.75rem;color:var(--muted);margin-top:2px">
                        Terdaftar: {{ $p->tanggal_daftar->format('d M Y') }}
                    </div>
                </div>
                <span style="background:#E8F5E9;color:#2E7D32;font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:20px">Aktif</span>
                <form method="POST" action="{{ route('penjadwalan.batalkan', $p->id) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Batalkan pendaftaran Tari {{ $p->tarian->nama }}?')"
                        style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:.75rem;font-weight:600;padding:6px 12px;border-radius:8px;cursor:pointer">
                        Batalkan
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- HEADER DAFTAR TARIAN & PENCARIAN --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <h3 style="font-family:var(--font-display);font-size:1.5rem;font-weight:700;color:var(--dark);">Pilihan Kelas Tari</h3>
            <p style="color:var(--muted);font-size:.9rem;">Temukan tarian yang ingin kamu pelajari.</p>
        </div>
        <div style="position:relative; width:100%; max-width:300px;">
            <svg style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9CA3AF;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Cari tarian..." 
                style="width:100%; padding:12px 16px 12px 40px; border:1.5px solid var(--border); border-radius:50px; font-size:.9rem; outline:none; background:#fff; transition:border .3s;">
        </div>
    </div>

    {{-- DAFTAR TARIAN TERSEDIA --}}
    <div id="tarianGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px">
        @foreach($tarianTersedia as $t)
        @php
            $sudahDaftar = $pendaftaran->where('tarian_id', $t->id)->count() > 0;
            $jadwalTarian = $jadwalLatihan; // semua jadwal tersedia
        @endphp

        <div style="background:#fff;border-radius:20px;border:2px solid {{ $sudahDaftar ? '#C65D2E' : 'var(--border)' }};overflow:hidden;transition:all .25s"
             id="card-tarian-{{ $t->id }}">

            {{-- Thumbnail --}}
            <div style="height:140px;background:var(--primary-pale);position:relative;display:flex;align-items:center;justify-content:center">
                @if($t->foto)
                    <img src="{{ asset('storage/'.$t->foto) }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                @endif
                @if($sudahDaftar)
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(198,93,46,.12);display:flex;align-items:center;justify-content:center">
                    <span style="background:#C65D2E;color:#fff;font-weight:800;font-size:.8rem;padding:6px 14px;border-radius:20px">✓ Sudah Terdaftar</span>
                </div>
                @endif
                {{-- Category badge --}}
                <div style="position:absolute;top:10px;right:10px;background:rgba(255,255,255,.9);color:#C65D2E;font-size:.7rem;font-weight:800;padding:3px 10px;border-radius:20px">
                    {{ ucfirst($t->kategori) }}
                </div>
            </div>

            <div style="padding:18px; display:flex; flex-direction:column; flex:1;">
                <h4 class="tarian-title" style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:4px">{{ $t->nama }}</h4>
                <p style="font-size:.8rem;color:var(--muted);margin-bottom:4px">📍 <span class="tarian-asal">{{ $t->asal }}</span></p>
                @if($t->durasi)
                <p style="font-size:.8rem;color:var(--muted);margin-bottom:12px">⏱ Durasi: {{ $t->durasi }}</p>
                @endif
                <p style="font-size:.85rem;color:var(--text);line-height:1.6;margin-bottom:20px;flex:1;">
                    {{ Str::limit($t->deskripsi, 100) }}
                </p>

                @if(!$sudahDaftar)
                <button type="button" onclick="openDaftarModal({{ $t->id }}, '{{ addslashes($t->nama) }}')"
                    style="width:100%;background:var(--primary);color:#fff;font-family:var(--font-body);font-size:.9rem;font-weight:700;padding:12px;border-radius:50px;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 12px rgba(198,93,46,.2);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(198,93,46,.3)';" 
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(198,93,46,.2)';">
                    Daftar Kelas Ini →
                </button>
                @else
                <div style="text-align:center;padding:12px;background:var(--bg-soft);border-radius:50px;border:1px solid var(--border);">
                    <p style="font-size:.85rem;font-weight:600;color:var(--muted);margin:0;">✓ Terdaftar</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>
</section>

{{-- MODAL PENDAFTARAN --}}
<div id="daftarModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; padding:20px; opacity:0; transition:opacity .3s;">
    <div style="background:#fff; border-radius:24px; width:100%; max-width:480px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,.2); transform:translateY(20px); transition:transform .3s;" id="daftarModalContent">
        <div style="padding:24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:#FAF8F6;">
            <div>
                <span style="color:var(--primary); font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:1px;">Pendaftaran Kelas</span>
                <h3 id="modalTariTitle" style="font-family:var(--font-display); font-size:1.4rem; font-weight:700; color:var(--dark); margin-top:4px;">Nama Tarian</h3>
            </div>
            <button onclick="closeDaftarModal()" style="background:none; border:none; font-size:1.5rem; color:var(--muted); cursor:pointer;">✕</button>
        </div>
        
        <form method="POST" action="{{ route('penjadwalan.daftar') }}" style="padding:24px;">
            @csrf
            <input type="hidden" name="tarian_id" id="modalTariId" value="">

            <div style="margin-bottom:20px">
                <label style="font-size:.85rem;font-weight:700;color:var(--dark);display:block;margin-bottom:8px">Pilih Jadwal Latihan <span style="color:#C65D2E">*</span></label>
                <div style="position:relative;">
                    <select name="jadwal_id" required
                        style="width:100%;padding:14px 16px;border:1.5px solid var(--border);border-radius:12px;font-size:.9rem;background:#fff;outline:none;appearance:none;cursor:pointer;">
                        <option value="">-- Pilih hari & jam --</option>
                        @foreach($jadwalLatihan as $j)
                        <option value="{{ $j->id }}">
                            {{ $j->hari }} · {{ $j->jam_mulai }}–{{ $j->jam_selesai }}
                        </option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;right:16px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted)" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <p style="font-size:.75rem; color:var(--muted); margin-top:6px;">Pilih jadwal yang paling sesuai dengan waktu luangmu.</p>
            </div>

            <div style="margin-bottom:30px">
                <label style="font-size:.85rem;font-weight:700;color:var(--dark);display:block;margin-bottom:8px">Catatan (opsional)</label>
                <textarea name="catatan" placeholder="Misal: Saya pemula dan belum pernah menari sebelumnya..." rows="3"
                    style="width:100%;padding:14px 16px;border:1.5px solid var(--border);border-radius:12px;font-size:.9rem;background:#fff;outline:none;resize:vertical;"></textarea>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="button" onclick="closeDaftarModal()"
                    style="flex:1; background:var(--bg-soft); color:var(--text); font-weight:700; padding:14px; border-radius:50px; border:1px solid var(--border); cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="flex:2; background:var(--primary); color:#fff; font-weight:700; padding:14px; border-radius:50px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(198,93,46,.3);">
                    Konfirmasi Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// --- LIVE SEARCH LOGIC ---
const searchInput = document.getElementById('searchInput');
if(searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('#tarianGrid > div');
        
        cards.forEach(card => {
            const title = card.querySelector('.tarian-title').textContent.toLowerCase();
            const asal = card.querySelector('.tarian-asal').textContent.toLowerCase();
            if(title.includes(query) || asal.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// --- MODAL LOGIC ---
const modal = document.getElementById('daftarModal');
const modalContent = document.getElementById('daftarModalContent');

function openDaftarModal(tariId, tariNama) {
    document.getElementById('modalTariId').value = tariId;
    document.getElementById('modalTariTitle').textContent = tariNama;
    
    modal.style.display = 'flex';
    // Small delay to allow display:flex to apply before animating opacity
    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'translateY(0)';
    }, 10);
}

function closeDaftarModal() {
    modal.style.opacity = '0';
    modalContent.style.transform = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal when clicking outside
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        closeDaftarModal();
    }
});

// Auto-select tarian jika ada query param
const tarianParam = new URLSearchParams(window.location.search).get('tarian');
if (tarianParam) {
    const card = document.getElementById('card-tarian-' + tarianParam);
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
@endsection
@extends('layouts.member')
@section('title', 'Pendaftaran Latihan')
@section('content')

<style>
    :root {
        --p-color: #C65D2E;
        --p-soft: #FDF0EA;
        --border-color: #F1F1F1;
    }

    .discovery-container {
        padding-top: calc(var(--nav-h) + 60px);
        padding-bottom: 120px;
        background: #fafaf8;
        min-height: 100vh;
    }

    /* HEADER STYLE */
    .page-header {
        text-align: center;
        margin-bottom: 64px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    .page-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 12px;
    }
    .page-header p {
        color: #6b7280;
        font-size: 1.1rem;
    }

    /* PROGRAM RUTIN CARDS */
    .routine-section {
        margin-bottom: 80px;
    }
    .routine-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }
    .routine-card {
        background: #fff;
        padding: 32px;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        text-align: center;
    }
    .routine-card h4 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
    }
    .routine-card .time-info {
        font-weight: 700;
        color: var(--p-color);
        font-size: 0.9rem;
        margin-bottom: 16px;
        display: block;
    }
    .routine-card p {
        font-size: 0.9rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    /* GENERAL BUTTONS */
    .btn-main {
        background: var(--p-color);
        color: #fff;
        font-weight: 800;
        padding: 14px 28px;
        border-radius: 100px;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-main:hover {
        background: #a44d26;
        transform: translateY(-2px);
    }
    .btn-success {
        background: #ecfdf5;
        color: #059669;
        font-weight: 800;
        padding: 14px 28px;
        border-radius: 100px;
        font-size: 0.85rem;
        display: block;
        text-align: center;
    }

    /* TAB & GRID */
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    .tari-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.4s ease;
        cursor: pointer;
    }
    .tari-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    }
    .tari-img {
        height: 200px;
        width: 100%;
        object-fit: cover;
    }
    .tari-content {
        padding: 24px;
    }
</style>

<div class="discovery-container">
    <div class="container">
        
        {{-- SIMPLE CENTERED HEADER --}}
        <div class="page-header">
            <h1>Jadwal Latihan Sanggar</h1>
            <p>Kelola pendaftaran rutin mingguan dan booking sesi tambahan Anda.</p>
        </div>

        {{-- LATIHAN RUTIN SECTION --}}
        @if(Auth::user()->tipe_anggota == 'anggota_tetap')
        <div class="routine-section">
            <h3 class="section-title">Program Latihan Rutin</h3>
            <div class="routine-grid">
                {{-- JUMAT --}}
                <div class="routine-card">
                    <h4>Jumat Siang</h4>
                    <span class="time-info">Jam: 14:00 - 17:00 WIB</span>
                    <p>Sesi latihan teknik dasar dan pemantapan koreografi mingguan bagi seluruh anggota tetap.</p>
                    
                    @php $isRegisteredJumat = $pendaftaran->where('jam_latihan', '14:00:00')->count(); @endphp
                    @if($isRegisteredJumat)
                        <span class="btn-success">✓ Terdaftar Rutin</span>
                    @else
                        <form action="{{ route('penjadwalan.daftar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tarian_id" value="{{ $tarianTersedia->first()->id ?? 1 }}">
                            <input type="hidden" name="tanggal_latihan" value="{{ date('Y-m-d', strtotime('next friday')) }}">
                            <input type="hidden" name="jam_latihan" value="14:00">
                            <input type="hidden" name="catatan" value="Latihan Rutin Jumat">
                            <button type="submit" class="btn-main">Daftar Rutin Jumat</button>
                        </form>
                    @endif
                </div>

                {{-- MINGGU --}}
                <div class="routine-card">
                    <h4>Minggu Rutin</h4>
                    <span class="time-info">Jam: 08:00 - 16:00 WIB</span>
                    <p>Sesi pendalaman materi tarian tradisional dan kreasi secara intensif sepanjang hari.</p>
                    
                    @php $isRegisteredMinggu = $pendaftaran->where('jam_latihan', '08:00:00')->count(); @endphp
                    @if($isRegisteredMinggu)
                        <span class="btn-success">✓ Terdaftar Rutin</span>
                    @else
                        <form action="{{ route('penjadwalan.daftar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tarian_id" value="{{ $tarianTersedia->first()->id ?? 1 }}">
                            <input type="hidden" name="tanggal_latihan" value="{{ date('Y-m-d', strtotime('next sunday')) }}">
                            <input type="hidden" name="jam_latihan" value="08:00">
                            <input type="hidden" name="catatan" value="Latihan Rutin Minggu">
                            <button type="submit" class="btn-main">Daftar Rutin Minggu</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- BOOKING SECTION --}}
        <div>
            <h3 class="section-title">Booking Sesi Tambahan</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 32px;">
                @foreach($tarianTersedia as $t)
                <div class="tari-card" onclick="openDaftarModal({{ $t->id }}, '{{ $t->nama }}')">
                    @if($t->foto)
                        <img src="{{ asset('storage/'.$t->foto) }}" class="tari-img">
                    @else
                        <div style="height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #d1d5db;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        </div>
                    @endif
                    <div class="tari-content">
                        <div style="font-size: 0.65rem; font-weight: 800; color: var(--p-color); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">{{ $t->kategori }}</div>
                        <h4 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 800; color: #111827; margin-bottom: 12px;">{{ $t->nama }}</h4>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f3f4f6; padding-top: 16px;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #9ca3af;">{{ $t->asal }}</div>
                            <div style="color: var(--p-color); font-weight: 800; font-size: 0.8rem;">Booking →</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div id="daftarModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(17, 24, 39, 0.5); backdrop-filter:blur(8px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:32px; width:100%; max-width:540px; overflow:hidden;" id="daftarModalContent">
        <div style="padding:40px; text-align:center;">
            <h2 id="modalTariTitle" style="font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 900; color: #111827;">Nama Tarian</h2>
            <p style="color: #6b7280; margin-top: 8px;">Tentukan jadwal booking tambahan Anda.</p>
        </div>
        <form method="POST" action="{{ route('penjadwalan.daftar') }}" style="padding:0 40px 40px;">
            @csrf
            <input type="hidden" name="tarian_id" id="modalTariId">
            <input type="hidden" name="jam_latihan" id="selectedJam">
            <div style="margin-bottom: 20px;">
                <label style="font-size:.85rem; font-weight:800; display:block; margin-bottom:10px;">Pilih Tanggal</label>
                <input type="date" name="tanggal_latihan" required min="{{ date('Y-m-d') }}" style="width:100%; padding:14px; border-radius:14px; border:1px solid #e5e7eb;">
            </div>
            <label style="font-size:.85rem; font-weight:800; display:block; margin-bottom:12px;">Pilih Jam</label>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 24px;">
                @foreach(['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00'] as $slot)
                    <div class="time-slot" onclick="selectJam('{{ $slot }}', this)" style="padding: 10px; border: 1px solid #e5e7eb; border-radius: 12px; cursor: pointer; text-align: center; font-size: 0.8rem; font-weight: 700;">{{ $slot }}</div>
                @endforeach
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeDaftarModal()" style="flex: 1; padding: 14px; border-radius: 100px; border: 1px solid #e5e7eb; background: #fff; font-weight: 800; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 1.5; padding: 14px; border-radius: 100px; border: none; background: var(--p-color); color: #fff; font-weight: 800; cursor: pointer;">Confirm Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
function selectJam(jam, el) {
    document.querySelectorAll('.time-slot').forEach(s => { s.style.borderColor = '#e5e7eb'; s.style.background = '#fff'; s.style.color = '#000'; });
    el.style.borderColor = 'var(--p-color)'; el.style.background = 'var(--p-soft)'; el.style.color = 'var(--p-color)';
    document.getElementById('selectedJam').value = jam;
}
function openDaftarModal(id, nama) {
    document.getElementById('modalTariId').value = id;
    document.getElementById('modalTariTitle').innerText = nama;
    document.getElementById('daftarModal').style.display = 'flex';
}
function closeDaftarModal() { document.getElementById('daftarModal').style.display = 'none'; }
</script>

@endsection
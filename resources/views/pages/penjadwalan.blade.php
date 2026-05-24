@extends('layouts.member')

@section('title', 'Daftar Kelas Latihan')

@section('content')
{{-- PAGE HEADER --}}
<div class="m-page-header">
    <span class="m-badge">Program Latihan</span>
    <h1>Daftar Kelas Latihan</h1>
    <p>Kelola pendaftaran rutin mingguan dan private sesi tambahan Anda.</p>
</div>

{{-- LATIHAN RUTIN SECTION (Hanya untuk Anggota Tetap) --}}
@if(Auth::user()->tipe_anggota == 'anggota_tetap')
<div style="margin-bottom: 48px;">
    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: #111827; margin-bottom: 24px; display: flex; align-items: center; gap: 15px;">
        Program Latihan Rutin
        <span style="flex: 1; height: 1px; background: #e5e7eb;"></span>
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        {{-- JUMAT --}}
        <div style="background: #fff; padding: 32px; border-radius: 24px; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.02); text-align: center;">
            <div style="width: 60px; height: 60px; background: #fff7ed; color: #c65d2e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.5rem; font-weight: 900;">J</div>
            <h4 style="font-size: 1.4rem; font-weight: 800; color: #111827; margin-bottom: 6px;">Jumat Siang</h4>
            <span style="font-weight: 700; color: #c65d2e; font-size: 0.9rem; margin-bottom: 16px; display: block;">🕒 14:00 - 17:00 WIB</span>
            <p style="font-size: 0.9rem; color: #6b7280; line-height: 1.6; margin-bottom: 28px;">Sesi latihan teknik dasar dan pemantapan koreografi bagi anggota tetap.</p>
            
            @php $isRegisteredJumat = $pendaftaran->where('jam_latihan', '14:00:00')->count(); @endphp
            @if($isRegisteredJumat)
                <div style="background: #ecfdf5; color: #059669; font-weight: 800; padding: 14px; border-radius: 100px; font-size: 0.85rem;">✓ Terdaftar Rutin</div>
            @else
                <form action="{{ route('penjadwalan.daftar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tarian_id" value="{{ $tarianTersedia->first()->id ?? 1 }}">
                    <input type="hidden" name="tanggal_latihan" value="{{ date('Y-m-d', strtotime('next friday')) }}">
                    <input type="hidden" name="jam_latihan" value="14:00">
                    <input type="hidden" name="catatan" value="Latihan Rutin Jumat">
                    <button type="submit" class="btn-daftar-rutin">Daftar Rutin Jumat</button>
                </form>
            @endif
        </div>

        {{-- MINGGU --}}
        <div style="background: #fff; padding: 32px; border-radius: 24px; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.02); text-align: center;">
            <div style="width: 60px; height: 60px; background: #fff7ed; color: #c65d2e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.5rem; font-weight: 900;">M</div>
            <h4 style="font-size: 1.4rem; font-weight: 800; color: #111827; margin-bottom: 6px;">Minggu Rutin</h4>
            <span style="font-weight: 700; color: #c65d2e; font-size: 0.9rem; margin-bottom: 16px; display: block;">🕒 08:00 - 16:00 WIB</span>
            <p style="font-size: 0.9rem; color: #6b7280; line-height: 1.6; margin-bottom: 28px;">Sesi pendalaman materi tarian tradisional secara intensif sepanjang hari.</p>
            
            @php $isRegisteredMinggu = $pendaftaran->where('jam_latihan', '08:00:00')->count(); @endphp
            @if($isRegisteredMinggu)
                <div style="background: #ecfdf5; color: #059669; font-weight: 800; padding: 14px; border-radius: 100px; font-size: 0.85rem;">✓ Terdaftar Rutin</div>
            @else
                <form action="{{ route('penjadwalan.daftar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tarian_id" value="{{ $tarianTersedia->first()->id ?? 1 }}">
                    <input type="hidden" name="tanggal_latihan" value="{{ date('Y-m-d', strtotime('next sunday')) }}">
                    <input type="hidden" name="jam_latihan" value="08:00">
                    <input type="hidden" name="catatan" value="Latihan Rutin Minggu">
                    <button type="submit" class="btn-daftar-rutin">Daftar Rutin Minggu</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endif

{{-- BOOKING SECTION --}}
<div>
    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: #111827; margin-bottom: 24px; display: flex; align-items: center; gap: 15px;">
        Sesi Private Tambahan
        <span style="flex: 1; height: 1px; background: #e5e7eb;"></span>
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px;">
        @foreach($tarianTersedia as $t)
        <div style="background: #fff; border-radius: 24px; border: 1px solid var(--border); overflow: hidden; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.02);" 
             onclick="openDaftarModal({{ $t->id }}, '{{ $t->nama }}')"
             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.06)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.02)'">
            
            @if($t->foto)
                <img src="{{ asset('storage/'.$t->foto) }}" style="height: 200px; width: 100%; object-fit: cover;">
            @else
                <div style="height: 200px; background: #f9fafb; display: flex; align-items: center; justify-content: center; color: #d1d5db;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
            @endif
            
            <div style="padding: 24px;">
                <div style="font-size: 0.65rem; font-weight: 800; color: #c65d2e; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">{{ $t->kategori }}</div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 800; color: #111827; margin-bottom: 12px;">{{ $t->nama }}</h4>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f3f4f6; padding-top: 16px;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #9ca3af;">{{ $t->asal }}</div>
                    <div style="color: #c65d2e; font-weight: 800; font-size: 0.8rem;">Private →</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- MODAL --}}
<div id="daftarModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(17, 24, 39, 0.6); backdrop-filter:blur(10px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:32px; width:100%; max-width:540px; overflow:hidden; animation: modalPop 0.3s ease;" id="daftarModalContent">
        <div style="padding:40px; text-align:center; background: #fafafa; border-bottom: 1px solid #f0f0f0;">
            <h2 id="modalTariTitle" style="font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 900; color: #111827;">Nama Tarian</h2>
            <p style="color: #6b7280; margin-top: 8px;">Tentukan jadwal private tambahan Anda.</p>
        </div>
        <form method="POST" action="{{ route('penjadwalan.daftar') }}" style="padding:32px 40px 40px;">
            @csrf
            <input type="hidden" name="tarian_id" id="modalTariId">
            <input type="hidden" name="jam_latihan" id="selectedJam">
            
            <div style="margin-bottom: 24px;">
                <label style="font-size:.85rem; font-weight:800; display:block; margin-bottom:10px; color: #374151;">Pilih Tanggal</label>
                <input type="date" name="tanggal_latihan" required min="{{ date('Y-m-d') }}" style="width:100%; padding:14px; border-radius:14px; border:1px solid #e5e7eb; font-family: inherit; font-weight: 600;">
            </div>

            <label style="font-size:.85rem; font-weight:800; display:block; margin-bottom:12px; color: #374151;">Pilih Jam Latihan</label>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 32px;">
                @foreach(['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00'] as $slot)
                    <div class="time-slot" onclick="selectJam('{{ $slot }}', this)" style="padding: 12px 5px; border: 1px solid #e5e7eb; border-radius: 12px; cursor: pointer; text-align: center; font-size: 0.8rem; font-weight: 700; transition: all 0.2s;">{{ $slot }}</div>
                @endforeach
            </div>

            <div style="display: flex; gap: 16px;">
                <button type="button" onclick="closeDaftarModal()" style="flex: 1; padding: 16px; border-radius: 100px; border: 1px solid #e5e7eb; background: #fff; font-weight: 800; cursor: pointer; color: #6b7280;">Batal</button>
                <button type="submit" style="flex: 1.5; padding: 16px; border-radius: 100px; border: none; background: #c65d2e; color: #fff; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(198,93,46,0.2);">Confirm Private</button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-daftar-rutin {
        background: #c65d2e; color: #fff; font-weight: 800; padding: 14px 28px; border-radius: 100px; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.3s ease; width: 100%;
    }
    .btn-daftar-rutin:hover { background: #a44d26; transform: translateY(-2px); }
    
    @keyframes modalPop { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    
    .time-slot:hover { border-color: #c65d2e; color: #c65d2e; background: #fffcfb; }
</style>

<script>
function selectJam(jam, el) {
    document.querySelectorAll('.time-slot').forEach(s => { 
        s.style.borderColor = '#e5e7eb'; 
        s.style.background = '#fff'; 
        s.style.color = '#374151'; 
    });
    el.style.borderColor = '#c65d2e'; 
    el.style.background = '#fff7ed'; 
    el.style.color = '#c65d2e';
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
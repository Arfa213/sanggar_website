@extends('layouts.app')
@section('title', 'Event & Workshop Mendatang')
@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div class="page-hero__bg"></div>
    <div class="container page-hero__inner">
        <span class="badge">Aktivitas Kami</span>
        <h1 class="page-hero__title">Event &amp; Workshop Mendatang</h1>
        <p class="page-hero__sub">Ikuti berbagai kegiatan, kelas khusus, dan workshop seni yang diselenggarakan di Sanggar Mulya Bhakti.</p>
        <div class="page-hero__nav">
            <a href="#midhang_sore" class="phero-nav-link">Midhang Sore</a>
            <a href="#studi_budaya" class="phero-nav-link">Studi Budaya</a>
            <a href="#pagelaran" class="phero-nav-link">Pagelaran</a>
        </div>
    </div>
</section>

{{-- MODAL DAFTAR EVENT UMUM (Dipakai di semua kategori) --}}
<div id="modalDaftarEvent" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="background: #1e1b4b; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="color: white; margin: 0; font-size: 1.2rem;">Formulir Pendaftaran</h3>
            <button onclick="tutupModalDaftar()" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('event.daftar') }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
            @csrf
            <input type="hidden" name="event_id" id="modalEventId">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 5px;">Event yang dipilih:</label>
                <div id="modalEventName" style="font-weight: 700; color: #1e1b4b; font-size: 1.1rem;"></div>
            </div>

            <div id="modalHargaWrap" style="display: none; margin-bottom: 20px; padding: 15px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
                <strong style="display: block; color: #d97706; margin-bottom: 5px;">HTM / Harga Tiket: <span id="modalHargaValue"></span></strong>
                <p style="margin: 0; font-size: 0.85rem; color: #92400e;">Silakan transfer ke <strong>BCA 1234567890 a.n Sanggar Mulya Bhakti</strong> dan lampirkan buktinya di bawah ini.</p>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Nama Lengkap *</label>
                <input type="text" name="nama_peserta" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Nomor WhatsApp *</label>
                <input type="text" name="no_hp" required placeholder="08..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px;">Asal Instansi / Sekolah (Opsional)</label>
                <input type="text" name="asal_instansi" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div id="modalBuktiWrap" style="display: none; margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; color: #b91c1c;">Upload Bukti Transfer *</label>
                <input type="file" name="bukti_transfer" id="inputBukti" accept="image/*" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc;">
            </div>

            <button type="submit" class="btn-cta" style="width: 100%; border-radius: 8px;">Kirim Pendaftaran</button>
        </form>
    </div>
</div>

<script>
    function bukaModalDaftar(id, nama, isBerbayar, harga) {
        document.getElementById('modalEventId').value = id;
        document.getElementById('modalEventName').innerText = nama;
        
        const wrapHarga = document.getElementById('modalHargaWrap');
        const wrapBukti = document.getElementById('modalBuktiWrap');
        const inputBukti = document.getElementById('inputBukti');
        
        if(isBerbayar) {
            wrapHarga.style.display = 'block';
            wrapBukti.style.display = 'block';
            inputBukti.required = true;
            document.getElementById('modalHargaValue').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
        } else {
            wrapHarga.style.display = 'none';
            wrapBukti.style.display = 'none';
            inputBukti.required = false;
        }
        
        document.getElementById('modalDaftarEvent').style.display = 'flex';
    }

    function tutupModalDaftar() {
        document.getElementById('modalDaftarEvent').style.display = 'none';
    }
</script>

{{-- 1. MIDHANG SORE --}}
<section class="section" id="midhang_sore">
    <div class="container">
        <div class="section-header">
            <span class="badge" style="background: #e0e7ff; color: #4338ca; border-color: #c7d2fe;">Workshop &amp; Kelas</span>
            <h2 class="section-heading">Midhang Sore</h2>
            <p class="section-sub">Wadah bagi para seniman dan pemateri untuk berbagi ilmu melalui workshop dan kelas khusus di Sanggar Mulya Bhakti.</p>
        </div>
        
        {{-- List Event Midhang Sore --}}
        <h3 style="font-size: 1.3rem; margin-bottom: 20px; color: #1e1b4b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Jadwal Midhang Sore Mendatang</h3>
        @if($midhang->count())
        <div class="kegiatan-block" style="margin-bottom: 40px; border: none; padding: 0; background: transparent;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                @foreach($midhang as $ev)
                @include('components.event_card', ['ev' => $ev, 'color' => '#4f46e5'])
                @endforeach
            </div>
        </div>
        @else
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; margin-bottom: 40px;">
            <p style="color: #64748b; margin: 0;">Belum ada jadwal Midhang Sore terdekat.</p>
        </div>
        @endif

        <div class="split-layout" style="margin-bottom: 60px;">
            <div class="split-text">
                <span class="badge" style="background: #e0e7ff; color: #4338ca;">Panggilan Kolaborasi</span>
                <h2 class="section-heading" style="text-align: left; margin-bottom: 15px; color: #1e1b4b;">Bawa Karyamu ke Panggung Kami</h2>
                <p style="margin-bottom: 15px;">Sanggar Mulya Bhakti membuka ruang seluas-luasnya bagi para seniman, koreografer, dan penggiat budaya untuk berkolaborasi melalui platform <strong>Midhang Sore</strong>.</p>
                <p style="margin-bottom: 25px;">Ini adalah kesempatan Anda untuk membagikan ilmu, mempresentasikan karya terbaru, atau menggelar workshop eksklusif bersama komunitas kami.</p>
                
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #4338ca; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: #1e1b4b; font-size: 1.1rem;">Syarat & Ketentuan:</h4>
                    <ul style="padding-left: 20px; margin-bottom: 0;">
                        <li style="margin-bottom: 8px;">Karya/Workshop berkaitan dengan seni pertunjukan (Tari, Musik, Teater).</li>
                        <li style="margin-bottom: 8px;">Memiliki portofolio yang jelas.</li>
                        <li>Jadwal pelaksanaan akan didiskusikan lebih lanjut.</li>
                    </ul>
                </div>

                <h3 style="font-size: 1.5rem; color: #1e1b4b; margin-bottom: 16px;">Tertarik Menjadi Pemateri?</h3>
                <p style="margin-bottom: 15px; color: #475569; line-height: 1.6;">Midhang Sore membuka kesempatan luas bagi seniman, profesional, dan penggiat budaya luar untuk mengadakan kelas khusus atau workshop di tempat kami.</p>
                <p style="margin-bottom: 25px; color: #475569; line-height: 1.6;">Isi formulir pengajuan di samping untuk mendaftarkan acara Anda. Setelah disetujui, acara Anda akan langsung tayang di atas!</p>
                
                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <div style="flex: 1; min-width: 140px; padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 3px solid #10b981;">
                        <strong style="display: block; font-size: 0.9rem; color: #047857;">Audiens Tersedia</strong>
                        <span style="font-size: 1.2rem; font-weight: 800; color: #1e1b4b;">100+ Anggota Aktif</span>
                    </div>
                    <div style="flex: 1; min-width: 140px; padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 3px solid #8b5cf6;">
                        <strong style="display: block; font-size: 0.9rem; color: #5b21b6;">Fasilitas</strong>
                        <span style="font-size: 1.2rem; font-weight: 800; color: #1e1b4b;">Aula Luas &amp; Sound</span>
                    </div>
                </div>
            </div>
            
            <div class="split-form" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <h3 style="font-size: 1.5rem; margin-bottom: 20px; color: #1e1b4b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Formulir Pengajuan Kolaborasi</h3>
                
                @if(session('success'))
                    <div style="background:#dcfce7;color:#15803d;padding:15px;border-radius:10px;font-size:0.9rem;font-weight:700;margin-bottom:20px;">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('event.ajukan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">1. Nama Anda / Komunitas / Kelompok *</label>
                        <input type="text" name="nama_pengaju" required placeholder="Contoh: Teater Cendrawasih" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">2. Foto Diri / Perwakilan *</label>
                        <input type="file" name="foto_pengaju" accept="image/*" required style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">3. Logo Komunitas (Link Portofolio) *</label>
                        <input type="url" name="portofolio_link" required placeholder="https://drive.google.com/... atau https://instagram.com/..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">4. Nomor WhatsApp *</label>
                        <input type="text" name="no_hp_pengaju" required placeholder="Contoh: 08123456789" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">5. Judul Karya / Workshop *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Eksplorasi Gerak Tari Topeng" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">6. Sinopsis (Link GDrive / Dokumen) *</label>
                        <input type="url" name="sinopsis_link" required placeholder="https://docs.google.com/..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">7. Catatan Tambahan</label>
                        <textarea name="catatan_pengaju" rows="2" placeholder="Ceritakan kebutuhan panggung / peralatan..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 20px; padding: 15px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
                        <strong style="color: #d97706; font-size: 0.85rem;">8. Tanggal Pelaksanaan</strong>
                        <p style="margin: 0; font-size: 0.8rem; color: #92400e; margin-top: 5px;">Tanggal event akan ditentukan kemudian oleh pihak Sanggar Mulya Bhakti setelah proses diskusi dan kurasi selesai.</p>
                    </div>

                    <button type="submit" class="btn-cta" style="width: 100%; border-radius: 8px; font-size: 1rem; background: #4338ca;">Kirim Pengajuan 🚀</button>
                </form>
            </div>
        </div>

    </div>
</section>

{{-- 2. STUDI BUDAYA --}}
<section class="section section--alt" id="studi_budaya">
    <div class="container">
        <div class="section-header">
            <span class="badge" style="background: #fce7f3; color: #be185d; border-color: #fbcfe8;">Event Tahunan</span>
            <h2 class="section-heading">Studi Budaya</h2>
            <p class="section-sub">Kegiatan penelusuran dan pembelajaran mendalam mengenai kebudayaan spesifik yang diselenggarakan oleh Sanggar.</p>
        </div>

        @if($studi->count())
        <div class="kegiatan-block" style="border: none; padding: 0; background: transparent;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                @foreach($studi as $ev)
                    @include('components.event_card', ['ev' => $ev, 'color' => '#be185d'])
                @endforeach
            </div>
        </div>
        @else
        <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <p style="color: #64748b; margin: 0;">Jadwal Studi Budaya tahun ini belum dirilis oleh Admin.</p>
        </div>
        @endif
    </div>
</section>

{{-- 3. PAGELARAN --}}
<section class="section" id="pagelaran">
    <div class="container">
        <div class="section-header">
            <span class="badge" style="background: #fef3c7; color: #b45309; border-color: #fde68a;">Pertunjukan Spesial</span>
            <h2 class="section-heading">Pagelaran</h2>
            <p class="section-sub">Pentas seni akbar dan pertunjukan puncak yang menampilkan karya-karya terbaik dari anggota Sanggar Mulya Bhakti.</p>
        </div>

        @if($pagelaran->count())
        <div class="kegiatan-block" style="border: none; padding: 0; background: transparent;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                @foreach($pagelaran as $ev)
                    @include('components.event_card', ['ev' => $ev, 'color' => '#b45309'])
                @endforeach
            </div>
        </div>
        @else
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <p style="color: #64748b; margin: 0;">Belum ada jadwal Pagelaran terdekat.</p>
        </div>
        @endif
    </div>
</section>

@endsection

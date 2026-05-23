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
            <a href="#mendatang" class="phero-nav-link">Jadwal Event</a>
            <a href="#pengajuan" class="phero-nav-link">Ajukan Kolaborasi</a>
        </div>
    </div>
</section>

{{-- EVENT MENDATANG --}}
<section class="section" id="mendatang">
    <div class="container">
        <div class="section-header">
            <span class="badge">Segera Hadir</span>
            <h2 class="section-heading">Event yang Akan Datang</h2>
        </div>
        
        @if($mendatang->count())
        <div class="kegiatan-block">
            <div class="event-upcoming-list">
                @foreach($mendatang as $ev)
                <div class="eu-item" style="border-left: 4px solid #C65D2E; background: #fff; margin-bottom: 20px; padding: 20px; border-radius: 12px; display: flex; gap: 20px; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div class="eu-date" style="background: #1e1b4b; color: white; padding: 15px; border-radius: 10px; text-align: center; min-width: 80px;">
                        <span class="eu-day" style="display: block; font-size: 1.8rem; font-weight: 900;">{{ $ev->tanggal->format('d') }}</span>
                        <span class="eu-month" style="display: block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">{{ $ev->tanggal->isoFormat('MMM YYYY') }}</span>
                    </div>
                    <div class="eu-info" style="flex: 1;">
                        <h4 style="font-size: 1.3rem; margin-bottom: 5px; color: #1e1b4b;">{{ $ev->nama }}</h4>
                        <span class="eu-meta" style="color: #64748b; font-size: 0.9rem;">📍 {{ $ev->lokasi }}</span>
                        
                        @if($ev->nama_pengaju)
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                                <span style="font-size: 0.85rem; color: #4338ca; font-weight: 700;">🤝 Kolaborasi Spesial dengan: {{ $ev->nama_pengaju }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="eu-right" style="text-align: right;">
                        <span class="eu-tipe" style="display: inline-block; padding: 6px 12px; background: #f1f5f9; color: #475569; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 8px;">{{ ucfirst(str_replace('_',' ',$ev->kategori)) }}</span>
                        <br>
                        @auth
                            <span style="display: inline-block; padding: 8px 16px; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: 1px solid #cbd5e1;">Anda Sudah Terdaftar (Anggota)</span>
                        @else
                            <button onclick="bukaModalDaftar({{ $ev->id }}, '{{ addslashes($ev->nama) }}', {{ $ev->is_berbayar ? 'true' : 'false' }}, {{ $ev->harga_tiket ?? 0 }})" style="display: inline-block; padding: 8px 16px; background: #C65D2E; color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.2s;">Daftar Peserta Umum</button>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px solid #e2e8f0;">
            <div style="font-size: 3rem; margin-bottom: 15px;">🗓️</div>
            <h3 style="color: #1e1b4b; margin-bottom: 10px;">Belum Ada Event Terdekat</h3>
            <p style="color: #64748b; max-width: 400px; margin: 0 auto;">Saat ini belum ada jadwal event atau workshop baru. Anda bisa mengajukan diri untuk mengisi acara di bawah ini!</p>
        </div>
        @endif
    </div>
</section>

{{-- MODAL DAFTAR EVENT UMUM --}}
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

{{-- PENGAJUAN EVENT --}}
<section class="section section--alt" id="pengajuan">
    <div class="container">
        <div class="split-layout">
            <div class="split-text">
                <span class="badge">Creative Hub</span>
                <h2 class="section-heading" style="text-align: left; margin-bottom: 16px;">Tertarik Menjadi Pemateri atau Mengadakan Kolaborasi?</h2>
                <p style="margin-bottom: 15px; color: #475569; line-height: 1.6;">Sanggar Mulya Bhakti membuka kesempatan luas bagi seniman, profesional, dan penggiat budaya luar untuk mengadakan kelas khusus atau workshop di tempat kami.</p>
                <p style="margin-bottom: 25px; color: #475569; line-height: 1.6;">Isi formulir pengajuan di samping untuk mendaftarkan acara Anda. Setelah kami review dan setujui, acara Anda akan langsung tayang di halaman ini dan bisa didaftar oleh anggota kami!</p>
                
                <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                    <div style="flex: 1; padding: 15px; background: #fff; border-radius: 12px; border-left: 3px solid #10b981;">
                        <strong style="display: block; font-size: 0.9rem; color: #047857;">Audiens Tersedia</strong>
                        <span style="font-size: 1.2rem; font-weight: 800; color: #1e1b4b;">100+ Anggota Aktif</span>
                    </div>
                    <div style="flex: 1; padding: 15px; background: #fff; border-radius: 12px; border-left: 3px solid #8b5cf6;">
                        <strong style="display: block; font-size: 0.9rem; color: #5b21b6;">Fasilitas</strong>
                        <span style="font-size: 1.2rem; font-weight: 800; color: #1e1b4b;">Aula Luas &amp; Sound</span>
                    </div>
                </div>
            </div>
            
            <div class="split-form" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px; color: #1e1b4b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Formulir Pengajuan Event</h3>
                
                @if(session('success'))
                    <div style="background:#dcfce7;color:#15803d;padding:15px;border-radius:10px;font-size:0.9rem;font-weight:700;margin-bottom:20px;">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('event.ajukan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Nama Anda / Instruktur *</label>
                        <input type="text" name="nama_pengaju" required placeholder="Contoh: Budi Santoso" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Nomor WhatsApp *</label>
                        <input type="text" name="no_hp_pengaju" required placeholder="Contoh: 08123456789" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                        <small style="color: #94a3b8; font-size: 0.75rem;">Kami akan menghubungi Anda melalui nomor ini jika disetujui.</small>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Judul Event / Workshop *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Workshop Tari Topeng Lanjutan" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Rencana Tanggal *</label>
                            <input type="date" name="tanggal" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Kategori *</label>
                            <select name="kategori" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                                <option value="workshop">Workshop / Seminar</option>
                                <option value="kelas_khusus">Kelas Khusus</option>
                                <option value="pentas">Pentas Kolaborasi</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Link Portofolio / Instagram</label>
                        <input type="url" name="portofolio_link" placeholder="https://instagram.com/..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Catatan Tambahan / Kebutuhan Fasilitas</label>
                        <textarea name="catatan_pengaju" rows="3" placeholder="Ceritakan singkat tentang materi dan fasilitas yang Anda butuhkan (contoh: Butuh proyektor dan sound system)." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-cta" style="width: 100%; border-radius: 8px; font-size: 1rem;">Kirim Pengajuan Event 🚀</button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

<div class="event-card-modern" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%;">
    
    {{-- Gambar Cover & Badge --}}
    <div style="position: relative; width: 100%; aspect-ratio: 16/10; background: {{ $color }}15; overflow: hidden; display: flex; align-items: center; justify-content: center;">
        @php
            $imgSrc = null;
            if ($ev->foto) {
                $imgSrc = asset('storage/' . $ev->foto);
            } elseif ($ev->foto_pengaju) {
                $imgSrc = asset('storage/' . $ev->foto_pengaju);
            }
        @endphp
        
        @if($imgSrc)
            <img src="{{ $imgSrc }}" alt="{{ $ev->nama }}" style="width: 100%; height: 100%; object-fit: cover;">
        @else
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}44" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        @endif

        {{-- Badge Kategori --}}
        <div style="position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; color: {{ $color }}; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            {{ str_replace('_', ' ', $ev->kategori) }}
        </div>
        
        {{-- Floating Date --}}
        <div style="position: absolute; bottom: -15px; left: 20px; background: {{ $color }}; color: white; padding: 8px 14px; border-radius: 12px; text-align: center; box-shadow: 0 6px 15px {{ $color }}66; z-index: 10;">
            <span style="display: block; font-size: 1.4rem; font-weight: 900; line-height: 1;">{{ $ev->tanggal->format('d') }}</span>
            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 3px;">{{ $ev->tanggal->isoFormat('MMM') }}</span>
        </div>
    </div>

    {{-- Konten Utama --}}
    <div style="padding: 35px 20px 20px 20px; display: flex; flex-direction: column; flex: 1;">
        <h4 style="font-size: 1.25rem; font-weight: 800; color: #1e1b4b; margin-bottom: 12px; line-height: 1.3;">{{ $ev->nama }}</h4>
        
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 12px; color: #64748b; font-size: 0.85rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $ev->lokasi }}</span>
        </div>

        @if($ev->deskripsi)
            <p style="font-size: 0.9rem; color: #475569; margin-bottom: 15px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                {{ Str::limit($ev->deskripsi, 120) }}
            </p>
        @elseif($ev->sinopsis_link)
            <p style="font-size: 0.9rem; color: #475569; margin-bottom: 15px; line-height: 1.6;">
                <a href="{{ $ev->sinopsis_link }}" target="_blank" style="color: {{ $color }}; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Buka Dokumen Sinopsis
                </a>
            </p>
        @else
            <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 15px; line-height: 1.6; font-style: italic;">
                Deskripsi event belum tersedia.
            </p>
        @endif

        @if($ev->nama_pengaju)
            <div style="margin-bottom: 15px; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border-left: 3px solid {{ $color }};">
                <span style="display: block; font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Kolaborasi Spesial</span>
                <span style="display: block; font-size: 0.9rem; color: #1e1b4b; font-weight: 800; margin-top: 2px;">{{ $ev->nama_pengaju }}</span>
            </div>
        @endif
        
        {{-- Spacer agar tombol selalu di bawah --}}
        <div style="flex: 1;"></div>

        {{-- Footer / Action --}}
        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px; margin-top: 5px; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 0.85rem; font-weight: 800; color: {{ $ev->is_berbayar ? '#059669' : '#4338ca' }}; display: flex; align-items: center; gap: 4px;">
                @if($ev->is_berbayar)
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
                    Rp {{ number_format($ev->harga_tiket ?? 0, 0, ',', '.') }}
                @else
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Gratis
                @endif
            </div>

            @auth
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; padding: 6px 12px; background: #f1f5f9; border-radius: 20px;">Anggota</span>
            @else
                <button onclick="bukaModalDaftar({{ $ev->id }}, '{{ addslashes($ev->nama) }}', {{ $ev->is_berbayar ? 'true' : 'false' }}, {{ $ev->harga_tiket ?? 0 }})" style="padding: 6px 14px; background: {{ $color }}; color: white; border: none; border-radius: 20px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px {{ $color }}44;">
                    Daftar
                </button>
            @endauth
        </div>
    </div>
</div>

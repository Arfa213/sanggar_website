<div class="eu-item" style="border-left: 4px solid {{ $color }}; background: #fff; margin-bottom: 20px; padding: 20px; border-radius: 12px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <div class="eu-date" style="background: {{ $color }}; color: white; padding: 15px; border-radius: 10px; text-align: center; min-width: 80px;">
        <span class="eu-day" style="display: block; font-size: 1.8rem; font-weight: 900;">{{ $ev->tanggal->format('d') }}</span>
        <span class="eu-month" style="display: block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">{{ $ev->tanggal->isoFormat('MMM YYYY') }}</span>
    </div>
    <div class="eu-info" style="flex: 1; min-width: 200px;">
        <h4 style="font-size: 1.3rem; margin-bottom: 5px; color: #1e1b4b;">{{ $ev->nama }}</h4>
        <span class="eu-meta" style="color: #64748b; font-size: 0.9rem;">📍 {{ $ev->lokasi }}</span>
        
        @if($ev->nama_pengaju)
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                <span style="font-size: 0.85rem; color: {{ $color }}; font-weight: 700;">🤝 Kolaborasi Spesial dengan: {{ $ev->nama_pengaju }}</span>
            </div>
        @endif
    </div>
    <div class="eu-right" style="text-align: right; min-width: 150px; display: flex; flex-direction: column; align-items: flex-start; gap: 10px;">
        <span class="eu-tipe" style="display: inline-block; padding: 6px 12px; background: #f1f5f9; color: #475569; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">{{ ucfirst(str_replace('_',' ',$ev->kategori)) }}</span>
        @auth
            <span style="display: inline-block; padding: 8px 16px; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: 1px solid #cbd5e1;">Anda Terdaftar (Anggota)</span>
        @else
            <button onclick="bukaModalDaftar({{ $ev->id }}, '{{ addslashes($ev->nama) }}', {{ $ev->is_berbayar ? 'true' : 'false' }}, {{ $ev->harga_tiket ?? 0 }})" style="display: inline-block; padding: 8px 16px; background: {{ $color }}; color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.2s;">Daftar Peserta Umum</button>
        @endauth
    </div>
</div>

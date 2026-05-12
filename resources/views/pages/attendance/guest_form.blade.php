<form action="{{ route('tamu.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" placeholder="Siapa nama Anda?" required>
    </div>
    
    <div class="form-group">
        <label>Nomor WhatsApp</label>
        <input type="tel" name="no_hp" placeholder="0812..." required>
    </div>

    <div class="form-group">
        <label>Pilih Tarian</label>
        <select name="tarian_id" required>
            <option value="">-- Mau belajar tari apa? --</option>
            @foreach(\App\Models\Tarian::where('aktif', true)->get() as $t)
                <option value="{{ $t->id }}">{{ $t->nama }} ({{ $t->kategori }})</option>
            @endforeach
        </select>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label>Pilih Tanggal</label>
            <input type="date" name="tanggal_latihan" required min="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label>Jam Latihan</label>
            <input type="hidden" name="jam_latihan" id="jamInput" required>
            <div class="time-grid">
                @php $slots = ['08:00','10:00','13:00','15:00','16:00','17:00','19:00','20:00']; @endphp
                @foreach($slots as $slot)
                    <div class="time-btn" onclick="setJam('{{ $slot }}', this)">{{ $slot }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Catatan (Opsional)</label>
        <textarea name="tujuan" rows="2" placeholder="Contoh: Datang bersama teman..."></textarea>
    </div>

    <button type="submit" class="btn">Konfirmasi Booking Sesi →</button>
</form>

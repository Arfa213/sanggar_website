@extends('admin.layouts.app')
@section('title', 'Peserta Ujian - ' . $event->nama)

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Peserta Ujian: {{ $event->nama }}</h1>
        <p style="color:var(--muted);font-size:.875rem">Kelola konfirmasi pendaftaran peserta ujian Midhang Sore.</p>
    </div>
    <div style="display:flex;gap:12px">
        <a href="{{ route('admin.event.index') }}" class="btn btn-secondary">
            ← Kembali ke Event
        </a>
        @if($stats['diterima'] > 0)
        <a href="{{ route('admin.ujian.form-nilai', $event->id) }}" class="btn btn-primary" style="background:#4f46e5;color:#fff;border:none;">
            🖊️ Input & Kelola Nilai Ujian
        </a>
        @endif
    </div>
</div>

{{-- STATISTIK --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#FFF3E0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#E65100">⏳</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#E65100">{{ $stats['menunggu'] }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Menunggu Konfirmasi</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#E8F5E9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#2E7D32">✓</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#2E7D32">{{ $stats['diterima'] }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Diterima (Ikut Ujian)</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#DC2626">✗</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#DC2626">{{ $stats['ditolak'] }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Ditolak</div>
        </div>
    </div>
</div>

{{-- TAB SWITCHER --}}
<div style="display:flex;gap:4px;margin-bottom:24px;background:#F5F3F1;border-radius:14px;padding:6px;width:fit-content;border:1px solid var(--border)">
    <button onclick="switchTab('menunggu')" id="tab-menunggu" class="tab-btn active">
        ⏳ Menunggu ({{ $stats['menunggu'] }})
    </button>
    <button onclick="switchTab('diterima')" id="tab-diterima" class="tab-btn">
        ✅ Diterima ({{ $stats['diterima'] }})
    </button>
    <button onclick="switchTab('ditolak')" id="tab-ditolak" class="tab-btn">
        ❌ Ditolak ({{ $stats['ditolak'] }})
    </button>
</div>

<style>
.tab-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: .875rem;
    cursor: pointer;
    background: transparent;
    color: var(--muted);
    transition: all .2s ease;
}
.tab-btn:hover { background: rgba(0, 0, 0, 0.05); color: var(--dark); }
.tab-btn.active {
    background: var(--dark) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.action-box { display: flex; gap: 8px; }
.btn-sm-action {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    font-weight: 700;
    font-size: 0.75rem;
    cursor: pointer;
    text-decoration: none;
}
.btn-accept { background: #dcfce7; color: #15803d; }
.btn-accept:hover { background: #bbf7d0; }
.btn-reject { background: #fee2e2; color: #b91c1c; }
.btn-reject:hover { background: #fecaca; }
</style>

{{-- LIST TABLES --}}
@foreach(['menunggu', 'diterima', 'ditolak'] as $tab)
<div id="panel-{{ $tab }}" class="panel-tab" style="display: {{ $tab === 'menunggu' ? 'block' : 'none' }}">
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Anggota</th>
                        <th>Tarian Ujian</th>
                        <th>Nomor Induk</th>
                        <th>Kehadiran Saat Daftar</th>
                        <th>Tanggal Daftar</th>
                        @if($tab === 'ditolak')
                        <th>Alasan Penolakan</th>
                        @endif
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $list = $pendaftar->where('status', $tab); @endphp
                    @forelse($list as $p)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $p->user->name }}</div>
                            <div style="font-size:0.75rem;color:var(--muted)">{{ $p->user->email }}</div>
                        </td>
                        <td>
                            <span class="chip chip--purple">{{ $p->tarian->nama }}</span>
                        </td>
                        <td><code>{{ $p->user->nomor_induk }}</code></td>
                        <td>
                            <div style="font-weight:700;color:{{ $p->persen_kehadiran >= 80 ? '#2E7D32' : '#E65100' }}">
                                {{ $p->persen_kehadiran }}%
                            </div>
                        </td>
                        <td>{{ $p->created_at->format('d M Y H:i') }}</td>
                        @if($tab === 'ditolak')
                        <td style="color:#b91c1c;font-size:0.85rem;max-width:200px">{{ $p->catatan_admin ?? '-' }}</td>
                        @endif
                        <td>
                            <div class="action-box" style="justify-content:flex-end">
                                @if($tab === 'menunggu')
                                <form action="{{ route('admin.ujian.update-status', $p->id) }}" method="POST" style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="diterima">
                                    <button type="submit" class="btn-sm-action btn-accept" onclick="return confirm('Terima pendaftaran ujian anggota ini?')">Terima</button>
                                </form>
                                <button type="button" class="btn-sm-action btn-reject" onclick="openRejectModal({{ $p->id }}, '{{ $p->user->name }}')">Tolak</button>
                                @elseif($tab === 'diterima')
                                <button type="button" class="btn-sm-action btn-reject" onclick="openRejectModal({{ $p->id }}, '{{ $p->user->name }}')">Batalkan / Tolak</button>
                                @elseif($tab === 'ditolak')
                                <form action="{{ route('admin.ujian.update-status', $p->id) }}" method="POST" style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="diterima">
                                    <button type="submit" class="btn-sm-action btn-accept" onclick="return confirm('Ubah status menjadi diterima?')">Terima Kembali</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $tab === 'ditolak' ? 7 : 6 }}" style="text-align:center;padding:40px;color:var(--muted)">
                            Tidak ada data peserta ujian pada tab ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

{{-- REJECT REASON MODAL --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:white;width:100%;max-width:450px;border-radius:16px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="background:#1e1b4b;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;color:white;">
            <h3 style="margin:0;font-size:1.1rem;font-weight:700">Tolak Pendaftaran Ujian</h3>
            <button onclick="closeRejectModal()" style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>
        <form id="rejectForm" method="POST" style="padding:20px">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="ditolak">
            <p style="font-size:0.875rem;color:var(--muted);margin-bottom:12px">Tentukan alasan penolakan pendaftaran ujian untuk <strong id="rejectMemberName"></strong>:</p>
            <textarea name="catatan_admin" required placeholder="Contoh: Kehadiran tidak mencukupi, harap koordinasi kembali dengan pelatih..." style="width:100%;height:100px;padding:10px;border:1px solid var(--border);border-radius:8px;resize:none;outline:none;font-size:0.875rem;margin-bottom:18px"></textarea>
            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" onclick="closeRejectModal()" style="background:#e5e7eb;color:#374151;border:none;padding:10px 16px;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.875rem">Batal</button>
                <button type="submit" style="background:#dc2626;color:white;border:none;padding:10px 16px;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.875rem">Tolak Pendaftaran</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    const panels = ['menunggu', 'diterima', 'ditolak'];
    panels.forEach(p => {
        document.getElementById('panel-' + p).style.display = p === tab ? 'block' : 'none';
        
        const btn = document.getElementById('tab-' + p);
        if (p === tab) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

function openRejectModal(id, name) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const memberName = document.getElementById('rejectMemberName');
    
    // Set form action dynamically
    form.action = `/admin/ujian/${id}/status`;
    memberName.innerText = name;
    
    modal.style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endsection

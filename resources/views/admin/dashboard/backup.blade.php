@extends('admin.layouts.app')
@section('title', 'Backup & Restore')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Manajemen Cadangan Data (Backup & Restore)</h1>
        <p>Cadangkan database dan folder storage media Anda ke Google Drive secara otomatis atau manual.</p>
    </div>
    <div class="page-header-actions">
        <form action="{{ route('admin.backup.run') }}" method="POST" onsubmit="showLoadingBackup()">
            @csrf
            <button type="submit" class="btn btn-primary" id="btnBackup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Backup Sekarang
            </button>
        </form>
    </div>
</div>

{{-- STATUS KONEKSI GOOGLE DRIVE --}}
<div class="card" style="margin-bottom: 24px; border: 1px solid {{ $googleDriveConnected ? '#bbf7d0' : '#fee2e2' }}; background: {{ $googleDriveConnected ? '#f0fdf4' : '#fef2f2' }};">
    <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; padding:20px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; border-radius:50%; background:{{ $googleDriveConnected ? '#16a34a' : '#dc2626' }}; display:flex; align-items:center; justify-content:center; color:white; font-size:1.5rem;">
                @if($googleDriveConnected)
                    ☁️
                @else
                    ⚠️
                @endif
            </div>
            <div>
                <h3 style="margin:0; font-size:1.1rem; color:{{ $googleDriveConnected ? '#14532d' : '#7f1d1d' }}; font-weight:700;">
                    Status Google Drive: {{ $googleDriveConnected ? 'Terhubung' : 'Tidak Terhubung / Belum Dikonfigurasi' }}
                </h3>
                <p style="margin:4px 0 0 0; font-size:0.875rem; color:{{ $googleDriveConnected ? '#166534' : '#991b1b' }};">
                    @if($googleDriveConnected)
                        File backup akan otomatis diunggah secara ganda ke folder Google Drive Anda.
                    @else
                        File backup disimpan secara lokal di server. Konfigurasikan <code>GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON</code> di file <code>.env</code> untuk mengaktifkan cloud backup.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- TABEL CADANGAN DATA --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Daftar File Cadangan (.zip)</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Ukuran</th>
                    <th>Penyimpanan</th>
                    <th>Tanggal Backup</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                <tr>
                    <td style="font-weight:600; font-size:.875rem;">
                        📁 {{ $b['name'] }}
                    </td>
                    <td style="font-size:.85rem; color:var(--text);">
                        {{ $b['size'] }}
                    </td>
                    <td>
                        <span class="chip {{ $b['source'] === 'Google Drive' ? 'chip--green' : 'chip--gray' }}">
                            {{ $b['source'] }}
                        </span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">
                        {{ date('d M Y, H:i', strtotime($b['created_at'])) }}
                    </td>
                    <td style="text-align: center; vertical-align: middle; width: 140px;">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                            <!-- Tombol Unduh -->
                            <a href="{{ route('admin.backup.download', $b['name']) }}" 
                            title="Unduh File Cadangan"
                            style="width: 34px; height: 34px; border-radius: 50%; border: 1px solid #bbf7d0; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.05);"
                            onmouseover="this.style.background='#16a34a'; this.style.color='#ffffff'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='#f0fdf4'; this.style.color='#16a34a'; this.style.transform='none';">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 17V3M5 10l7 7 7-7M19 21H5"/>
                                </svg>
                            </a>
                            
                            <!-- Tombol Restore -->
                            <form action="{{ route('admin.backup.restore', $b['name']) }}" method="POST" class="restore-form" style="margin: 0; display: inline-flex;">
                                @csrf
                                <button type="submit" 
                                        title="Restore / Pulihkan Data"
                                        style="width: 34px; height: 34px; border-radius: 50%; border: 1px solid #c7d2fe; background: #e0e7ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.05);"
                                        onmouseover="this.style.background='#4f46e5'; this.style.color='#ffffff'; this.style.transform='translateY(-1px)';"
                                        onmouseout="this.style.background='#e0e7ff'; this.style.color='#4f46e5'; this.style.transform='none';">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                        <polyline points="3 3 3 8 8 8"/>
                                    </svg>
                                </button>
                            </form>
                            
                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.backup.delete', $b['name']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file cadangan ini?')" style="margin: 0; display: inline-flex;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Hapus Permanen"
                                        style="width: 34px; height: 34px; border-radius: 50%; border: 1px solid #fecdd3; background: #fff1f2; color: #e11d48; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(225, 29, 72, 0.05);"
                                        onmouseover="this.style.background='#e11d48'; this.style.color='#ffffff'; this.style.transform='translateY(-1px)';"
                                        onmouseout="this.style.background='#fff1f2'; this.style.color='#e11d48'; this.style.transform='none';">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:var(--muted); padding:40px;">
                        Belum ada file cadangan data yang tersimpan. Klik tombol "Backup Sekarang" untuk membuat cadangan baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function showLoadingBackup() {
        Swal.fire({
            title: 'Membuat Backup...',
            text: 'Proses mengekspor database dan mengompres folder media sedang berjalan. Harap tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    document.querySelectorAll('.restore-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Proses restore akan menimpa seluruh database dan folder media saat ini dengan data dari file backup yang dipilih!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Restore Sekarang! 🚀',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memulihkan Data...',
                        text: 'Jangan tutup atau segarkan halaman ini selama proses restore berlangsung.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    this.submit();
                }
            });
        });
    });
</script>

@endsection
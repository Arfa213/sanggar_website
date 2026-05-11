<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Anggota</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #C65D2E; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #C65D2E; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #FDF0EA; color: #C65D2E; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #E8E0D8; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border: 1px solid #E8E0D8; vertical-align: top; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        .nama { font-weight: bold; color: #1a1a1a; font-size: 11px; }
        .chip { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .chip-green  { background: #E8F5E9; color: #2E7D32; }
        .chip-blue   { background: #E3F2FD; color: #1565C0; }
        .chip-purple { background: #F3E5F5; color: #6A1B9A; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SANGGAR MULYA BHAKTI</h2>
        @php
            $label = match($tipe ?? 'semua') {
                'pengunjung' => 'Laporan Data Pengunjung',
                'private'    => 'Laporan Data Peserta Private',
                default      => 'Laporan Data Anggota Sanggar',
            };
        @endphp
        <p>{{ $label }}</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <p>Total: {{ $anggota->count() }} orang</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="22%">Nama Lengkap</th>
                <th width="22%">Email</th>
                <th width="13%">No. HP</th>
                <th width="12%">Tipe</th>
                <th width="10%">Status</th>
                <th width="9%">Bergabung</th>
                <th width="8%">Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($anggota as $index => $a)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td class="nama">{{ $a->name }}</td>
                <td>{{ $a->email }}</td>
                <td>{{ $a->no_hp ?? '-' }}</td>
                <td>
                    @php
                        $cls = match($a->tipe_anggota ?? 'anggota_tetap') {
                            'anggota_tetap' => 'chip-green',
                            'pengunjung'    => 'chip-blue',
                            'private'       => 'chip-purple',
                            default         => ''
                        };
                    @endphp
                    <span class="chip {{ $cls }}">{{ $a->tipe_anggota_label }}</span>
                </td>
                <td style="text-align: center;">{{ ucfirst($a->status) }}</td>
                <td style="text-align: center;">{{ $a->created_at->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $a->tanggal_keluar ? $a->tanggal_keluar->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sanggar Mulya Bhakti — Sistem Manajemen Keanggotaan
    </div>
</body>
</html>

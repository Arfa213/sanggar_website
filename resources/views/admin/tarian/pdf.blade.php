<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Tarian</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #C65D2E; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #C65D2E; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #FDF0EA; color: #C65D2E; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #E8E0D8; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border: 1px solid #E8E0D8; vertical-align: top; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        .nama-tarian { font-weight: bold; color: #1a1a1a; font-size: 12px; }
        .kategori { font-style: italic; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SANGGAR MULYA BHAKTI</h2>
        <p>Laporan Data Koleksi Tarian Tradisional Indramayu</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Tarian</th>
                <th width="15%">Asal</th>
                <th width="15%">Kategori</th>
                <th width="30%">Fungsi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tarian as $index => $t)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="nama-tarian">{{ $t->nama }}</div>
                </td>
                <td>{{ $t->asal }}</td>
                <td class="kategori">{{ ucfirst($t->kategori) }}</td>
                <td>{{ $t->fungsi ?? '-' }}</td>
                <td style="text-align: center;">{{ $t->aktif ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halaman 1 dari 1 | Sanggar Mulya Bhakti — Sistem Manajemen Arsip Digital
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SANGGAR MULYA BHAKTI</div>
        <div>{{ $title }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>WAKTU</th>
                <th>NAMA TAMU</th>
                <th>NO. HP</th>
                <th>TUJUAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $p)
            <tr>
                <td>{{ $p->tanggal->format('d/m/Y') }} {{ date('H:i', strtotime($p->jam)) }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->no_hp ?? '-' }}</td>
                <td>{{ $p->tujuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

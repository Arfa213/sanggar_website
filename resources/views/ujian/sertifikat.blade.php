<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kelulusan Ujian — {{ $rapor->user->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #faf7f2;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .certificate-container {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 20mm;
            position: relative;
            background-color: #ffffff;
            border: 15px solid #1e1b4b;
            outline: 2px solid #c65d2e;
            outline-offset: -10px;
        }
        .gold-corner-tl {
            position: absolute; top: 15px; left: 15px; width: 80px; height: 80px;
            border-top: 5px solid #c65d2e; border-left: 5px solid #c65d2e;
        }
        .gold-corner-tr {
            position: absolute; top: 15px; right: 15px; width: 80px; height: 80px;
            border-top: 5px solid #c65d2e; border-right: 5px solid #c65d2e;
        }
        .gold-corner-bl {
            position: absolute; bottom: 15px; left: 15px; width: 80px; height: 80px;
            border-bottom: 5px solid #c65d2e; border-left: 5px solid #c65d2e;
        }
        .gold-corner-br {
            position: absolute; bottom: 15px; right: 15px; width: 80px; height: 80px;
            border-bottom: 5px solid #c65d2e; border-right: 5px solid #c65d2e;
        }
        .header {
            text-align: center;
            margin-top: 10mm;
        }
        .header h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.2rem;
            font-weight: 900;
            color: #1e1b4b;
            margin: 0;
            letter-spacing: 2px;
        }
        .header p {
            font-size: 0.85rem;
            color: #c65d2e;
            text-transform: uppercase;
            font-weight: 700;
            margin: 5px 0 0 0;
            letter-spacing: 3px;
        }
        .title {
            text-align: center;
            margin-top: 10mm;
        }
        .title h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 3rem;
            font-weight: 700;
            color: #c65d2e;
            margin: 0;
        }
        .title p {
            font-size: 1rem;
            font-style: italic;
            color: #475569;
            margin: 5px 0 0 0;
        }
        .recipient {
            text-align: center;
            margin-top: 8mm;
        }
        .recipient-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: #1e1b4b;
            border-bottom: 2px solid #1e1b4b;
            display: inline-block;
            padding-bottom: 5px;
            margin: 0;
        }
        .recipient-nip {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 5px;
            font-weight: 500;
        }
        .statement {
            text-align: center;
            margin-top: 8mm;
            padding: 0 15mm;
            font-size: 1rem;
            line-height: 1.6;
            color: #334155;
        }
        .statement strong {
            color: #1e1b4b;
        }
        .details-box {
            text-align: center;
            margin-top: 8mm;
        }
        .score-badge {
            display: inline-block;
            background-color: #fdf0ea;
            border: 1px solid #f9d5c5;
            color: #c65d2e;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .footer-section {
            margin-top: 15mm;
            width: 100%;
            position: relative;
        }
        .signature-left {
            position: absolute;
            left: 20mm;
            text-align: center;
            width: 60mm;
        }
        .signature-right {
            position: absolute;
            right: 20mm;
            text-align: center;
            width: 60mm;
        }
        .sig-line {
            width: 100%;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 5px;
        }
        .sig-title {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }
        .sig-name {
            font-weight: 700;
            color: #1e1b4b;
            font-size: 0.95rem;
        }
        .cert-number {
            position: absolute;
            bottom: 8mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Gold borders/corners -->
        <div class="gold-corner-tl"></div>
        <div class="gold-corner-tr"></div>
        <div class="gold-corner-bl"></div>
        <div class="gold-corner-br"></div>

        <!-- Header -->
        <div class="header">
            <h1>SANGGAR MULYA BHAKTI</h1>
            <p>Pusat Pelestarian Seni Tari Tradisional Cirebon</p>
        </div>

        <!-- Title -->
        <div class="title">
            <h2>SERTIFIKAT KELULUSAN</h2>
            <p>Sertifikat ini diberikan secara digital kepada:</p>
        </div>

        <!-- Recipient -->
        <div class="recipient">
            <h3 class="recipient-name">{{ $rapor->user->name }}</h3>
            <div class="recipient-nip">Nomor Induk Anggota: {{ $rapor->user->nomor_induk }}</div>
        </div>

        <!-- Statement -->
        <div class="statement">
            Dinyatakan <strong>LULUS</strong> dalam Ujian Tari Tradisional untuk kategori <strong>{{ $rapor->tarian->nama }}</strong><br>
            yang diselenggarakan pada event <strong>{{ $rapor->event->nama }}</strong> pada tanggal <strong>{{ $rapor->event->tanggal->isoFormat('D MMMM YYYY') }}</strong>.
        </div>

        <!-- Grade/Predikat -->
        <div class="details-box">
            <span class="score-badge">Nilai Akhir: {{ $rapor->nilai_akhir }} (Predikat: {{ $rapor->predikat }})</span>
        </div>

        <!-- Signatures -->
        <div class="footer-section">
            <div class="signature-left">
                <div style="height: 15mm;"></div> <!-- Spacer for sign image/written -->
                <div class="sig-line"></div>
                <div class="sig-name">{{ $rapor->pelatih->name }}</div>
                <div class="sig-title">Pelatih Penguji</div>
            </div>
            <div class="signature-right">
                <div style="height: 15mm;"></div> <!-- Spacer -->
                <div class="sig-line"></div>
                <div class="sig-name">Pimpinan Sanggar</div>
                <div class="sig-title">Sanggar Mulya Bhakti</div>
            </div>
        </div>

        <!-- Certificate Number -->
        <div class="cert-number">
            No. Sertifikat: SMB/UJIAN/{{ $rapor->id }}/{{ $rapor->event->tanggal->format('Y') }}
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Tiket Event - {{ $peserta->order_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .ticket-wrapper {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 2px dashed #cbd5e1;
            padding: 20px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #64748b;
        }
        .event-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .event-info h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #C65D2E;
        }
        .info-row {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .participant-info {
            margin-bottom: 20px;
        }
        .participant-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1e1b4b;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
        }
        .qr-placeholder {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background: #10b981;
            color: white;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="ticket-wrapper">
    <div class="header">
        <h1>SANGGAR MULYA BHAKTI</h1>
        <p>E-Tiket Resmi Event & Workshop</p>
    </div>

    <div class="event-info">
        <h2>{{ $peserta->event->nama }}</h2>
        <div class="info-row">
            <span class="info-label">Tanggal:</span> {{ $peserta->event->tanggal->format('d F Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">Lokasi:</span> {{ $peserta->event->lokasi }}
        </div>
        <div class="info-row">
            <span class="info-label">Kategori:</span> {{ ucfirst(str_replace('_', ' ', $peserta->event->kategori)) }}
        </div>
    </div>

    <div class="participant-info">
        <h3>Detail Pemegang Tiket</h3>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div class="info-row"><span class="info-label">Order ID:</span> <strong>{{ $peserta->order_id }}</strong></div>
                    <div class="info-row"><span class="info-label">Nama Lengkap:</span> {{ $peserta->nama_peserta }}</div>
                    <div class="info-row"><span class="info-label">No. WhatsApp:</span> {{ $peserta->no_hp }}</div>
                    <div class="info-row"><span class="info-label">Instansi:</span> {{ $peserta->asal_instansi ?? '-' }}</div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <div style="margin-bottom: 10px;">Status Pembayaran:</div>
                    <div class="status-badge">{{ $peserta->status_pembayaran }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="qr-placeholder">
        <p style="font-size: 12px; color: #475569; margin: 0 0 10px 0;">Harap tunjukkan E-Tiket ini (digital/cetak) kepada panitia di lokasi acara.</p>
        <!-- In a real app, generate a QR code for check-in here -->
        <div style="width: 120px; height: 120px; border: 2px solid #1e1b4b; display: inline-block; padding: 5px;">
            <div style="width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; text-align: center;">
                [QR CODE<br>{{ $peserta->order_id }}]
            </div>
        </div>
    </div>

    <div class="footer">
        Dicetak otomatis oleh sistem Sanggar Mulya Bhakti pada {{ date('d M Y H:i') }}.<br>
        Tiket ini adalah dokumen yang sah.
    </div>
</div>

</body>
</html>

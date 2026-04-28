<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($invoiceTitle) ?> - <?= esc($invoiceNumber) ?></title>
    <style>
        @page {
            margin: 16px;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        body {
            margin: 0;
            color: #25324d;
            background: #f4f8ff;
            font-size: 11px;
            line-height: 1.42;
        }

        .page {
            background: #ffffff;
            border: 1px solid #dfe9f7;
            border-radius: 18px;
            padding: 22px;
        }

        .topbar {
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(90deg, #5b56e8 0%, #7c88ff 45%, #4ade80 100%);
            margin-bottom: 18px;
        }

        .layout,
        .info-grid,
        .course-grid,
        .bottom-grid,
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef4ff;
            color: #5360e8;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .brand-row {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-mark {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #5a5cf0 0%, #7b8bff 60%, #4ade80 100%);
            color: #ffffff;
            text-align: center;
            font-size: 23px;
            font-weight: 800;
            line-height: 56px;
        }

        .brand-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.08;
            color: #31405e;
            font-weight: 800;
        }

        .brand-subtitle {
            margin: 6px 0 0;
            color: #6d7f9d;
            font-size: 11px;
            max-width: 310px;
        }

        .invoice-panel {
            text-align: right;
            padding-top: 4px;
        }

        .invoice-title {
            margin: 0;
            color: #273553;
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
        }

        .invoice-kicker {
            margin: 5px 0 10px;
            color: #7787a3;
            font-size: 11px;
        }

        .status-pill {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .status-pill.pending { background: #fff4d6; color: #9a6700; }
        .status-pill.paid { background: #e9fff0; color: #169246; }
        .status-pill.failed { background: #fee2e2; color: #b42318; }
        .status-pill.expired { background: #e8edf5; color: #475467; }
        .status-pill.cancelled { background: #eceff3; color: #344054; }

        .section-gap {
            height: 14px;
        }

        .panel {
            border: 1px solid #e2eaf7;
            border-radius: 16px;
            padding: 14px;
            background: #ffffff;
            vertical-align: top;
        }

        .panel.soft {
            background: #fbfdff;
            border-color: #dbe7f7;
        }

        .panel-title {
            margin: 0 0 10px;
            color: #6b7d9b;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .customer-name {
            margin: 0 0 4px;
            color: #283755;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.15;
        }

        .muted {
            color: #7588a6;
        }

        .summary-line {
            margin-bottom: 6px;
        }

        .summary-line:last-child {
            margin-bottom: 0;
        }

        .summary-line strong {
            color: #172033;
        }

        .course-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #4d63d8 0%, #6b8cff 100%);
            color: #ffffff;
            padding: 16px;
        }

        .course-badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            color: #eef4ff;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .course-title {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 800;
            color: #ffffff;
        }

        .course-copy {
            margin: 0;
            color: #f1f5ff;
            font-size: 11px;
            max-width: 360px;
        }

        .price-panel {
            border-radius: 18px;
            background: #fbfdff;
            border: 1px solid #dce8f8;
            padding: 16px;
            text-align: right;
            box-shadow: inset 0 0 0 1px #f2f7ff;
        }

        .price-label {
            color: #6f83a1;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .price-currency {
            color: #5967ee;
            font-size: 14px;
            font-weight: 800;
        }

        .price-value {
            color: #283755;
            font-size: 32px;
            font-weight: 800;
            line-height: 1.05;
            margin: 4px 0 10px;
        }

        .price-helper {
            color: #7a8ca9;
            font-size: 10px;
        }

        .totals-card {
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #e2eaf7;
            padding: 14px;
        }

        .totals-table td {
            padding: 5px 0;
        }

        .totals-label {
            color: #7286a4;
        }

        .totals-value {
            text-align: right;
            color: #2c3a59;
            font-weight: 700;
        }

        .grand-total td {
            padding-top: 9px;
            border-top: 1px solid #e4ebf5;
        }

        .grand-total .totals-label,
        .grand-total .totals-value {
            font-size: 16px;
            font-weight: 800;
            color: #2c3a59;
        }

        .note-card,
        .help-card {
            border-radius: 16px;
            padding: 14px;
            vertical-align: top;
        }

        .note-card {
            background: #fffef9;
            border: 1px solid #f4e8be;
        }

        .help-card {
            background: #f4fff7;
            border: 1px solid #d4f1dd;
        }

        .bottom-title {
            margin: 0 0 8px;
            color: #31405e;
            font-size: 13px;
            font-weight: 800;
        }

        .bottom-copy {
            margin: 0;
            color: #7385a2;
        }

        .bottom-copy strong {
            color: #31405e;
        }

        .footer-copy {
            margin-top: 12px;
            color: #91a0b5;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar"></div>

        <table class="layout">
            <tr>
                <td style="width: 54%; padding-right: 10px; vertical-align: top;">
                    <div class="brand-chip">Streaming Course Invoice</div>
                    <table class="brand-row">
                        <tr>
                            <td style="width: 68px; vertical-align: top;">
                                <div class="brand-mark">SC</div>
                            </td>
                            <td style="vertical-align: top;">
                                <h1 class="brand-title">Streaming Course</h1>
                                <p class="brand-subtitle">Ringkasan pembayaran premium yang rapi, mudah dibaca, dan siap disimpan sebagai bukti transaksi.</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="invoice-panel" style="width: 46%; padding-left: 10px; vertical-align: top;">
                    <h2 class="invoice-title">Invoice</h2>
                    <p class="invoice-kicker">Bukti transaksi resmi pembelian kursus</p>
                    <span class="status-pill <?= esc($statusClass) ?>"><?= esc($statusLabel) ?></span>
                </td>
            </tr>
        </table>

        <div class="section-gap"></div>

        <table class="info-grid">
            <tr>
                <td style="width: 48%; padding-right: 8px; vertical-align: top;">
                    <div class="panel">
                        <p class="panel-title">Ditagihkan Kepada</p>
                        <p class="customer-name"><?= esc($customerName) ?></p>
                        <div class="muted"><?= esc($customerEmail) ?></div>
                    </div>
                </td>
                <td style="width: 52%; padding-left: 8px; vertical-align: top;">
                    <div class="panel soft">
                        <p class="panel-title">Ringkasan Pembayaran</p>
                        <div class="summary-line"><strong>Provider:</strong> <?= esc($providerLabel) ?></div>
                        <div class="summary-line"><strong>Referensi:</strong> <?= esc($transaction['reference_code'] ?? '-') ?></div>
                        <div class="summary-line"><strong>ID Transaksi:</strong> #<?= esc($transaction['id']) ?></div>
                        <?php if ($expiresAt !== null): ?>
                            <div class="summary-line"><strong>Batas Bayar:</strong> <?= esc($expiresAt) ?></div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-gap"></div>

        <table class="course-grid">
            <tr>
                <td style="width: 60%; padding-right: 8px; vertical-align: top;">
                    <div class="course-hero">
                        <div class="course-badge">Kursus Premium</div>
                        <h3 class="course-title"><?= esc($courseTitle) ?></h3>
                        <p class="course-copy">Akses belajar untuk 1 akun pengguna, termasuk materi kursus dan histori pembayaran yang tersimpan aman di akun kamu.</p>
                    </div>
                </td>
                <td style="width: 40%; padding-left: 8px; vertical-align: top;">
                    <div class="price-panel">
                        <div class="price-label">Total Pembayaran</div>
                        <div class="price-currency"><?= esc(strtoupper((string) ($transaction['currency'] ?? 'IDR'))) ?></div>
                        <div class="price-value"><?= esc(number_format((int) ($transaction['amount'] ?? 0))) ?></div>
                        <div class="price-helper">Sudah termasuk seluruh biaya transaksi kursus ini</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-gap"></div>

        <div class="totals-card">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal</td>
                    <td class="totals-value"><?= esc($amountLabel) ?></td>
                </tr>
                <tr>
                    <td class="totals-label">Biaya Tambahan</td>
                    <td class="totals-value">IDR 0</td>
                </tr>
                <tr class="grand-total">
                    <td class="totals-label">Total Akhir</td>
                    <td class="totals-value"><?= esc($amountLabel) ?></td>
                </tr>
            </table>
        </div>

        <div class="section-gap"></div>

        <table class="bottom-grid">
            <tr>
                <td style="width: 62%; padding-right: 8px; vertical-align: top;">
                    <div class="note-card">
                        <p class="bottom-title">Catatan Invoice</p>
                        <p class="bottom-copy">
                            <?php if ($failureMessage !== ''): ?>
                                Status transaksi memiliki catatan: <strong><?= esc($failureMessage) ?></strong>
                            <?php elseif ($statusClass === 'paid'): ?>
                                Pembayaran telah <strong>berhasil diterima</strong>. Simpan invoice ini sebagai bukti transaksi resmi Streaming Course.
                            <?php elseif ($invoiceUrl !== ''): ?>
                                Pembayaran masih bisa dicek kembali melalui riwayat pembayaran dan link provider di dashboard akun kamu.
                            <?php else: ?>
                                Simpan invoice ini sebagai ringkasan transaksi digital pembelian kursus.
                            <?php endif; ?>
                        </p>
                    </div>
                </td>
                <td style="width: 38%; padding-left: 8px; vertical-align: top;">
                    <div class="help-card">
                        <p class="bottom-title">Butuh Bantuan?</p>
                        <p class="bottom-copy">Saat menghubungi admin, siapkan <strong>nomor invoice</strong> dan <strong>kode referensi</strong> agar proses pengecekan lebih cepat.</p>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-copy">Dokumen otomatis • Streaming Course • Ringkasan transaksi digital pengguna</div>
    </div>
</body>
</html>

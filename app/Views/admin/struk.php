<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
        }

        .logo {
            text-align: center;
            margin-bottom: 5px;
        }

        .logo img {
            width: 60px;
            height: auto;
        }

        h2,
        p {
            text-align: center;
            margin: 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        td {
            padding: 2px 0;
        }

        .total {
            border-top: 1px dashed #000;
            margin-top: 5px;
            padding-top: 5px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="logo">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo E-Commerce">
    </div>

    <h2>WARUNG KOPI KITA</h2>
    <p><?= date('d/m/Y H:i', strtotime($pesanan['created_at'])) ?></p>
    <p>Nama: <?= esc($pesanan['nama_pelanggan']) ?></p>
    <hr>

    <table>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= esc($item['nama_produk']) ?> (<?= $item['jumlah'] ?>)</td>
                <td style="text-align:right;">Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p class="total">Total: Rp<?= number_format($pesanan['total'], 0, ',', '.') ?></p>

    <div class="footer">
        <p>Terima kasih 🙏</p>
        <p>www.waroengkami.com</p>
    </div>

</body>

</html>
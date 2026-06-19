<?php
session_start();

require_once '../classes/user.php';

User::cekLogin();

require_once '../classes/Transaksi.php';

$id = $_GET['id'] ?? null;

if (!$id) {

    header('Location: index.php');

    exit;

}

$transaksi = new Transaksi();

$detail  = $transaksi->getDetail($id);
$riwayat = $transaksi->getRiwayat();

$dataTransaksi = null;

foreach ($riwayat as $t) {

    if ($t['id'] == $id) {

        $dataTransaksi = $t;

        break;

    }

}

if (!$dataTransaksi) {

    header('Location: riwayat.php');

    exit;

}

$namaRole = $_SESSION['user']['role'] === 'kasir'
    ? 'User'
    : 'Admin';

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Struk Transaksi - MyKasir</title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard_modern.css"
    >

    <style>

        body{
            background:#f1f5f9;
        }

        .receipt-wrapper{
            display:flex;
            justify-content:center;
            padding:40px 20px;
        }

        .receipt-card{
            width:100%;
            max-width:450px;
            background:white;
            border-radius:28px;
            overflow:hidden;
            box-shadow:0 12px 35px rgba(0,0,0,0.08);
        }

        .receipt-header{
            background:linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );

            color:white;
            padding:30px;
            text-align:center;
        }

        .receipt-header h1{
            font-size:30px;
            margin-bottom:8px;
        }

        .receipt-header p{
            font-size:14px;
            opacity:.9;
        }

        .receipt-body{
            padding:28px;
        }

        .receipt-info{
            background:#f8fafc;
            border-radius:18px;
            padding:18px;
            margin-bottom:24px;
        }

        .info-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:10px;
            font-size:14px;
            color:#334155;
        }

        .info-row:last-child{
            margin-bottom:0;
        }

        .section-title{
            font-size:16px;
            font-weight:700;
            margin-bottom:16px;
            color:#0f172a;
        }

        .product-item{
            padding:14px 0;
            border-bottom:1px dashed #dbeafe;
        }

        .product-name{
            font-weight:700;
            color:#1e293b;
            margin-bottom:8px;
        }

        .product-detail{
            display:flex;
            justify-content:space-between;
            font-size:14px;
            color:#64748b;
        }

        .total-box{
            margin-top:26px;
            background:#eff6ff;
            border-radius:18px;
            padding:20px;
        }

        .total-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .total-label{
            font-size:16px;
            font-weight:700;
            color:#1e293b;
        }

        .total-price{
            font-size:22px;
            font-weight:800;
            color:#2563eb;
        }

        .thankyou{
            text-align:center;
            margin-top:28px;
            color:#64748b;
            font-size:14px;
            line-height:1.7;
        }

        .receipt-footer{
            display:flex;
            gap:12px;
            padding:24px 28px 28px;
        }

        .btn-print,
        .btn-history,
        .btn-new{
            flex:1;
            text-decoration:none;
            border:none;
            padding:14px;
            border-radius:14px;
            font-weight:700;
            font-size:14px;
            cursor:pointer;
            transition:.3s;
            text-align:center;
        }

        .btn-print{
            background:#2563eb;
            color:white;
        }

        .btn-print:hover{
            background:#1d4ed8;
            transform:translateY(-2px);
        }

        .btn-history{
            background:#f59e0b;
            color:white;
        }

        .btn-history:hover{
            background:#d97706;
            transform:translateY(-2px);
        }

        .btn-new{
            background:#22c55e;
            color:white;
        }

        .btn-new:hover{
            background:#16a34a;
            transform:translateY(-2px);
        }

        @media print{

            .sidebar,
            .content-header,
            .receipt-footer{
                display:none !important;
            }

            body{
                background:white;
            }

            .receipt-card{
                box-shadow:none;
                max-width:100%;
            }

        }

    </style>

</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar no-print">

        <div class="sidebar-header">
            <div class="logo">🛒 MyKasir</div>
        </div>

        <nav class="sidebar-nav">

    <a href="../dashboard.php" class="nav-item">
        <span class="icon">📊</span>
        <span>Dashboard</span>
    </a>

    <a href="index.php" class="nav-item active">
        <span class="icon">📦</span>
        <span>Produk</span>
    </a>

    <?php if ($_SESSION['user']['role'] === 'kasir'): ?>

        <a href="../transaksi/index.php" class="nav-item">
            <span class="icon">💰</span>
            <span>Kasir</span>
        </a>

    <?php endif; ?>

    <a href="../transaksi/riwayat.php" class="nav-item">
        <span class="icon">📝</span>
        <span>Riwayat</span>
    </a>

    <a href="../logout.php" class="nav-item logout">
        <span class="icon">🚪</span>
        <span>Logout</span>
    </a>

</nav>

        <div class="sidebar-footer">

            <div class="user-info">

                <div class="avatar">
                    <?= strtoupper(
                        substr(
                            $_SESSION['user']['username'],
                            0,
                            1
                        )
                    ) ?>
                </div>

                <div class="user-details">

                    <div class="username">
                        <?= htmlspecialchars(
                            $_SESSION['user']['username']
                        ) ?>
                    </div>

                    <div class="role">
                        <?= $namaRole ?>
                    </div>

                </div>

            </div>

        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="content-header no-print">

            <div class="header-left">

                <h1>Struk Transaksi</h1>

                <p class="subtitle">
                    Detail pembayaran pelanggan 🧾
                </p>

            </div>

            <div class="header-right">
                <button class="btn-icon">🔔</button>
                <button class="btn-icon">⚙️</button>
            </div>

        </header>

        <!-- RECEIPT -->
        <div class="receipt-wrapper">

            <div class="receipt-card">

                <!-- HEADER -->
                <div class="receipt-header">

                    <h1>🛒 MyKasir</h1>

                    <p>
                        Bukti pembayaran transaksi
                    </p>

                </div>

                <!-- BODY -->
                <div class="receipt-body">

                    <!-- INFO -->
                    <div class="receipt-info">

                        <div class="info-row">
                            <span>ID Transaksi</span>

                            <strong>
                                #<?= str_pad(
                                    $dataTransaksi['id'],
                                    5,
                                    '0',
                                    STR_PAD_LEFT
                                ) ?>
                            </strong>
                        </div>

                        <div class="info-row">
                            <span>Tanggal</span>

                            <strong>
                                <?= date(
                                    'd M Y - H:i',
                                    strtotime(
                                        $dataTransaksi['tanggal']
                                    )
                                ) ?>
                            </strong>
                        </div>

                        <div class="info-row">
                            <span>Kasir</span>

                            <strong>
                                <?= htmlspecialchars(
                                    $dataTransaksi['username']
                                ) ?>
                            </strong>
                        </div>

                    </div>

                    <!-- PRODUK -->
                    <div class="section-title">
                        Detail Produk
                    </div>

                    <?php foreach ($detail as $d): ?>

                    <div class="product-item">

                        <div class="product-name">
                            <?= htmlspecialchars(
                                $d['nama_produk']
                            ) ?>
                        </div>

                        <div class="product-detail">

                            <span>
                                <?= $d['qty'] ?>
                                x
                                Rp <?= number_format(
                                    $d['harga_satuan'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </span>

                            <strong>
                                Rp <?= number_format(
                                    $d['subtotal'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>

                        </div>

                    </div>

                    <?php endforeach; ?>

                    <!-- TOTAL -->
                    <div class="total-box">

                        <div class="total-row">

                            <div class="total-label">
                                TOTAL
                            </div>

                            <div class="total-price">
                                Rp <?= number_format(
                                    $dataTransaksi['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </div>

                        </div>

                    </div>

                    <!-- THANKYOU -->
                    <div class="thankyou">

                        ✨ Terima kasih telah berbelanja
                        di MyKasir ✨

                        <br>

                        Semoga harimu secerah tombol
                        checkout yang berhasil ditekan 🚀

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="receipt-footer no-print">

                    <button
                        onclick="window.print()"
                        class="btn-print"
                    >
                        🖨️ Cetak
                    </button>

                    <a
                        href="riwayat.php"
                        class="btn-history"
                    >
                        📜 Riwayat
                    </a>

                    <?php if ($_SESSION['user']['role'] === 'kasir'): ?>

                    <a
                        href="index.php"
                        class="btn-new"
                    >
                        ➕ Baru
                    </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>
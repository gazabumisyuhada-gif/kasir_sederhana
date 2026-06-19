<?php
session_start();

require_once 'classes/user.php';
User::cekLogin();
$isAdmin = $_SESSION['user']['role'] === 'admin';

require_once 'classes/Produk.php';
require_once 'classes/Transaksi.php';

$produk    = new Produk();
$transaksi = new Transaksi();

$semuaProduk    = $produk->getAll();
$semuaTransaksi = $transaksi->getRiwayat();

$pendapatanHariIni = 0;
$today = date('Y-m-d');

foreach ($semuaTransaksi as $t) {

    if (date('Y-m-d', strtotime($t['tanggal'])) === $today) {
        $pendapatanHariIni += $t['total_harga'];
    }

}

$namaRole = $_SESSION['user']['role'] === 'admin'
    ? 'Admin'
    : 'Kasir';?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="logo">🛒 MyKasir</div>
        </div>

        <nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item active">
        <span class="icon">📊</span>
        <span>Dashboard</span>
    </a>

    <a href="Produk/index.php" class="nav-item">
        <span class="icon">📦</span>
        <span>Produk</span>
    </a>

    <?php if($_SESSION['user']['role'] === 'kasir'): ?>
    <a href="Transaksi/index.php" class="nav-item">
        <span class="icon">💰</span>
        <span>Kasir</span>
    </a>
    <?php endif; ?>

    <a href="Transaksi/riwayat.php" class="nav-item">
        <span class="icon">📝</span>
        <span>Riwayat</span>
    </a>

    <a href="logout.php" class="nav-item logout">
        <span class="icon">🚪</span>
        <span>Logout</span>
    </a>
</nav>

        <!-- USER INFO -->
        <div class="sidebar-footer">

            <div class="user-info">

                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['user']['username'], 0, 1)) ?>
                </div>

                <div class="user-details">

                    <div class="username">
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
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
        <header class="content-header">

            <div class="header-left">
                <h1>
                     <?= $isAdmin ? 'Dashboard Admin' : 'Dashboard Kasir' ?>
                </h1>

                <p class="subtitle">
                    Kelola produk dan transaksi toko ✨
                </p>
            </div>

            <div class="header-right">

                <button class="btn-icon">
                    🔔
                </button>

                <button class="btn-icon">
                    ⚙️
                </button>

            </div>

        </header>

        <!-- STATS -->
        <div class="stats-container">

            <!-- TOTAL PRODUK -->
            <div class="stat-card blue">

                <div class="stat-icon">📦</div>

                <div class="stat-content">

                    <div class="stat-value">
                        <?= count($semuaProduk) ?>
                    </div>

                    <div class="stat-label">
                        Total Produk
                    </div>

                </div>

            </div>

            <!-- TOTAL TRANSAKSI -->
            <div class="stat-card green">

                <div class="stat-icon">🛒</div>

                <div class="stat-content">

                    <div class="stat-value">
                        <?= count($semuaTransaksi) ?>
                    </div>

                    <div class="stat-label">
                        Total Transaksi
                    </div>

                </div>

            </div>

            <!-- PENDAPATAN -->
             <?php if ($isAdmin): ?>
            <div class="stat-card purple">

                <div class="stat-icon">💰</div>

                <div class="stat-content">

                    <div class="stat-value">
                        Rp <?= number_format($pendapatanHariIni, 0, ',', '.') ?>
                    </div>

                    <div class="stat-label">
                        Pendapatan Hari Ini
                    </div>

                </div>
                <?php endif; ?>

            </div>

        </div>

        <!-- PRODUK -->
        <div class="products-section">

            <div class="section-header">

                <div>

                    <h2>Daftar Produk</h2>

                    <p class="section-subtitle">
                        Semua produk yang tersedia di toko
                    </p>

                </div>

                <div class="section-actions">

                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                    <a href="Produk/tambah.php" class="btn btn-primary">
                        <span>+</span> Tambah Produk
                    </a>

                    <?php endif; ?>

                </div>

            </div>

            <!-- FILTER -->
            <div class="filter-tabs">

                <button class="tab-item active">
                    Semua
                    <span class="badge">
                        <?= count($semuaProduk) ?>
                    </span>
                </button>

                <button class="tab-item">

                    Tersedia

                    <span class="badge">
                        <?= count(array_filter($semuaProduk, fn($p) => $p['stok'] > 0)) ?>
                    </span>

                </button>

                <button class="tab-item">

                    Stok Habis

                    <span class="badge">
                        <?= count(array_filter($semuaProduk, fn($p) => $p['stok'] == 0)) ?>
                    </span>

                </button>

            </div>

            <!-- GRID PRODUK -->
            <div class="products-grid">

                <?php if (empty($semuaProduk)): ?>

                    <div class="empty-state">

                        <div class="empty-icon">📦</div>

                        <h3>Belum ada produk</h3>

                        <p>
                            Tambahkan produk terlebih dahulu
                        </p>

                    </div>

                <?php else: ?>

                    <?php foreach ($semuaProduk as $p): ?>

                    <div class="product-card">

                        <div class="product-badge <?= $p['stok'] > 0 ? 'available' : 'disabled' ?>">

                            <?= $p['stok'] > 0 ? 'Tersedia' : 'Habis' ?>

                        </div>

                        <div class="product-image">

                            <?php if ($p['foto']): ?>

                                <img
                                    src="assets/uploads/<?= htmlspecialchars($p['foto']) ?>"
                                    alt="<?= htmlspecialchars($p['nama_produk']) ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">📦</div>

                            <?php endif; ?>

                        </div>

                        <div class="product-info">

                            <h3 class="product-name">
                                <?= htmlspecialchars($p['nama_produk']) ?>
                            </h3>

                            <div class="product-meta">

                                <span class="product-date">
                                    <?= date('d.m.y', strtotime($p['created_at'] ?? 'now')) ?>
                                </span>

                                <span class="product-price">
                                    Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                </span>

                            </div>

                            <div class="product-stock">
                                Stok: <?= $p['stok'] ?>
                            </div>

                        </div>

                        <?php if ($p['stok'] > 0): ?>

                        <button
                            class="btn-add-cart"
                            onclick="tambahKeKeranjang(
                                <?= $p['id'] ?>,
                                '<?= htmlspecialchars($p['nama_produk'], ENT_QUOTES) ?>',
                                <?= $p['harga'] ?>,
                                <?= $p['stok'] ?>
                            )"
                        >
                            <span>🛒</span>
                            Tambah
                        </button>

                        <?php endif; ?>

                    </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

<script>

let keranjang =
    JSON.parse(localStorage.getItem('keranjang')) || [];

function tambahKeKeranjang(id, nama, harga, stok){

    const existing =
        keranjang.find(item => item.id === id);

    if(existing){

        if(existing.qty >= stok){

            alert('Stok tidak mencukupi!');
            return;

        }

        existing.qty++;
        existing.subtotal =
            existing.qty * existing.harga;

    }else{

        keranjang.push({
            id:id,
            nama:nama,
            harga:harga,
            qty:1,
            subtotal:harga,
            stok:stok
        });

    }

    localStorage.setItem(
        'keranjang',
        JSON.stringify(keranjang)
    );

    alert(nama + ' berhasil ditambahkan 🛒');

}

</script>

</body>
</html>
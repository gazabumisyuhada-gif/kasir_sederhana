<?php
session_start();

require_once '../classes/user.php';
User::cekLogin();

require_once '../classes/Transaksi.php';

$transaksi = new Transaksi();
$semuaTransaksi = $transaksi->getRiwayat();

// Logika dinamis untuk menentukan nama Role (Disesuaikan dengan file kasir)
$namaRole = (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin')
    ? 'Admin'
    : 'Kasir';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - MyKasir</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <style>
        .table-container{ background:white; border-radius:20px; padding:24px; margin-top:24px; box-shadow:0 4px 12px rgba(0,0,0,0.04); }
        .table-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
        .table-header h2{ font-size:28px; color:#0f172a; margin-bottom:5px; }
        .table-header p{ color:#64748b; font-size:14px; }
        .btn-primary{ background:#22c55e; color:white; text-decoration:none; padding:12px 18px; border-radius:12px; font-weight:600; transition:.3s; }
        .btn-primary:hover{ background:#16a34a; transform:translateY(-2px); }
        .custom-table{ width:100%; border-collapse:collapse; }
        .custom-table thead{ background:#f8fafc; }
        .custom-table th{ padding:16px; text-align:left; color:#64748b; font-size:14px; font-weight:600; }
        .custom-table td{ padding:18px 16px; border-top:1px solid #f1f5f9; color:#1e293b; }
        .transaction-id{ font-weight:700; color:#2563eb; }
        .cashier{ display:flex; align-items:center; gap:10px; }
        .cashier-avatar{ width:38px; height:38px; border-radius:50%; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700; }
        .total-price{ color:#16a34a; font-weight:700; }
        .btn-detail{ background:#2563eb; color:white; text-decoration:none; padding:10px 14px; border-radius:10px; font-size:14px; font-weight:600; transition:.3s; display:inline-block; }
        .btn-detail:hover{ background:#1d4ed8; }
        .empty-state{ text-align:center; padding:60px 20px; }
        .empty-icon{ font-size:60px; margin-bottom:12px; }
        .empty-state h3{ color:#1e293b; margin-bottom:6px; }
        .empty-state p{ color:#64748b; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">🛒 MyKasir</div>
        </div>

        <nav class="sidebar-nav">
            <a href="../dashboard.php" class="nav-item">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="../Produk/index.php" class="nav-item">
                <span class="icon">📦</span>
                <span>Produk</span>
            </a>
            <?php if ($_SESSION['user']['role'] === 'kasir'): ?>
                <a href="index.php" class="nav-item">
                    <span class="icon">💰</span>
                    <span>Kasir</span>
                </a>
            <?php endif; ?>
            <a href="riwayat.php" class="nav-item active">
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

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <h1>Riwayat Transaksi</h1>
                <p class="subtitle">Semua aktivitas transaksi toko Anda 💳</p>
            </div>
            <div class="header-right">
                <button class="btn-icon">🔔</button>
                <button class="btn-icon">⚙️</button>
            </div>
        </header>

        <div class="table-container">
            <div class="table-header">
                <div>
                    <h2>Daftar Transaksi</h2>
                    <p>Lihat seluruh transaksi yang telah dilakukan</p>
                </div>
                <?php if ($_SESSION['user']['role'] !== 'admin'): ?>
                    <a href="index.php" class="btn-primary">
                        + Transaksi Baru
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($semuaTransaksi)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🧾</div>
                    <h3>Belum ada transaksi</h3>
                    <p>Semua transaksi akan muncul di sini ✨</p>
                </div>
            <?php else: ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($semuaTransaksi as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="transaction-id">
                                #<?= str_pad($t['id'], 5, '0', STR_PAD_LEFT) ?>
                            </div>
                        </td>
                        <td>
                            <?= date('d M Y - H:i', strtotime($t['tanggal'])) ?>
                        </td>
                        <td>
                            <div class="cashier">
                                <div class="cashier-avatar">
                                    <?= strtoupper(substr($t['username'], 0, 1)) ?>
                                </div>
                                <span>
                                    <?= htmlspecialchars($t['username']) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="total-price">
                                Rp <?= number_format($t['total_harga'], 0, ',', '.') ?>
                            </div>
                        </td>
                        <td>
                            <a href="struk.php?id=<?= $t['id'] ?>" class="btn-detail">
                                🧾 Lihat Struk
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
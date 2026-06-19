<?php
session_start();

require_once '../classes/user.php';

User::cekLogin();

$namaRole = $_SESSION['user']['role'] === 'admin'
    ? 'Admin'
    : 'Kasir';

require_once '../classes/Produk.php';

$produk      = new Produk();
$semuaProduk = $produk->getAll();

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - MyKasir</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">

    <style>
        .table-container{
            background: white;
            border-radius: 18px;
            padding: 24px;
            margin-top: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .table-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .table-header h2{
            font-size:28px;
            color:#1e293b;
            margin-bottom:5px;
        }

        .table-header p{
            color:#64748b;
            font-size:14px;
        }

        .btn-primary{
            background:#22c55e;
            color:white;
            padding:12px 18px;
            border-radius:12px;
            text-decoration:none;
            font-weight:600;
            transition:.3s;
        }

        .btn-primary:hover{
            background:#16a34a;
            transform:translateY(-2px);
        }

        .custom-table{
            width:100%;
            border-collapse:collapse;
        }

        .custom-table thead{
            background:#f8fafc;
        }

        .custom-table th{
            padding:16px;
            text-align:left;
            color:#64748b;
            font-size:14px;
        }

        .custom-table td{
            padding:18px 16px;
            border-top:1px solid #f1f5f9;
            vertical-align:middle;
        }

        .product-img{
            width:60px;
            height:60px;
            border-radius:14px;
            object-fit:cover;
            border:1px solid #e2e8f0;
        }

        .no-image{
            width:60px;
            height:60px;
            border-radius:14px;
            background:#f1f5f9;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
        }

        .product-name{
            font-weight:600;
            color:#1e293b;
        }

        .price{
            font-weight:700;
            color:#22c55e;
        }

        .stock{
            padding:6px 12px;
            border-radius:999px;
            font-size:13px;
            font-weight:600;
            display:inline-block;
        }

        .stock.available{
            background:#dcfce7;
            color:#15803d;
        }

        .stock.low{
            background:#fee2e2;
            color:#dc2626;
        }

        .action-group{
            display:flex;
            gap:10px;
        }

        .btn-action{
            padding:10px 14px;
            border-radius:10px;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
            transition:.3s;
        }

        .btn-edit{
            background:#facc15;
            color:#1e293b;
        }

        .btn-edit:hover{
            background:#eab308;
        }

        .btn-delete{
            background:#ef4444;
            color:white;
        }

        .btn-delete:hover{
            background:#dc2626;
        }

        .alert{
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:20px;
            font-weight:500;
        }

        .alert-success{
            background:#dcfce7;
            color:#166534;
        }

        .alert-danger{
            background:#fee2e2;
            color:#991b1b;
        }

        .empty-state{
            text-align:center;
            padding:60px 20px;
        }

        .empty-state .icon{
            font-size:60px;
            margin-bottom:10px;
        }

        .empty-state h3{
            color:#1e293b;
            margin-bottom:6px;
        }

        .empty-state p{
            color:#64748b;
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="content-header">
            <div class="header-left">
                <h1>Produk</h1>
                <p class="subtitle">Kelola semua produk toko Anda 📦</p>
            </div>

            <div class="header-right">
                <button class="btn-icon">🔔</button>
                <button class="btn-icon">⚙️</button>
            </div>
        </header>

        <!-- TABLE -->
        <div class="table-container">

            <div class="table-header">
                <div>
                    <h2>Daftar Produk</h2>
                    <p>Semua produk yang tersedia di toko</p>
                </div>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="tambah.php" class="btn-primary">
                    + Tambah Produk
                </a>
                <?php endif; ?>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($semuaProduk)): ?>

                <div class="empty-state">
                    <div class="icon">📦</div>
                    <h3>Belum ada produk</h3>
                    <p>Tambahkan produk pertama untuk mulai berjualan 🚀</p>
                </div>

            <?php else: ?>

            <table class="custom-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($semuaProduk as $i => $p): ?>

                    <tr>

                        <td><?= $i + 1 ?></td>

                        <td>
                            <?php if ($p['foto']): ?>

                                <img
                                    src="../assets/uploads/<?= htmlspecialchars($p['foto']) ?>"
                                    class="product-img"
                                    alt="<?= htmlspecialchars($p['nama_produk']) ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">📦</div>

                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="product-name">
                                <?= htmlspecialchars($p['nama_produk']) ?>
                            </div>
                        </td>

                        <td>
                            <div class="price">
                                Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                            </div>
                        </td>

                        <td>

                            <?php if ($p['stok'] > 5): ?>

                                <span class="stock available">
                                    <?= $p['stok'] ?> pcs
                                </span>

                            <?php else: ?>

                                <span class="stock low">
                                    <?= $p['stok'] ?> pcs
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                                <div class="action-group">

                                    <a
                                        href="edit.php?id=<?= $p['id'] ?>"
                                        class="btn-action btn-edit"
                                    >
                                        ✏️ Edit
                                    </a>

                                    <a
                                        href="hapus.php?id=<?= $p['id'] ?>"
                                        class="btn-action btn-delete"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                    >
                                        🗑 Hapus
                                    </a>

                                </div>

                                <?php endif; ?>

                            </div>
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
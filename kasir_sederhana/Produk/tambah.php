<?php
session_start();

require_once '../classes/user.php';

User::cekLogin();
User::cekAdmin();

$namaRole = 'Admin';

require_once '../classes/Produk.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama  = trim($_POST['nama_produk']);
    $harga = trim($_POST['harga']);
    $stok  = trim($_POST['stok']);
    $foto  = null;

    if (empty($nama) || empty($harga) || empty($stok)) {

        $error = 'Semua field wajib diisi!';

    } elseif (!is_numeric($harga) || !is_numeric($stok))
     {

        $error = 'Harga dan stok harus berupa angka!';

    } else {

        // Upload Foto
        if (!empty($_FILES['foto']['name'])) {

            $ekstensi = strtolower(
                pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
            );

            $izin = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ekstensi, $izin)) {

                $error = 'Format foto harus JPG, JPEG, PNG, atau WEBP!';

            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {

                $error = 'Ukuran foto maksimal 2MB!';

            } else {

                $namaFile = time() . '_' . uniqid() . '.' . $ekstensi;

                $tujuan = '../assets/uploads/' . $namaFile;

                if (move_uploaded_file(
                    $_FILES['foto']['tmp_name'],
                    $tujuan
                )) {

                    $foto = $namaFile;

                } else {

                    $error = 'Gagal mengupload foto!';

                }

            }

        }

        if (!$error) {

            $produk = new Produk();

            if ($produk->tambah($nama, $harga, $stok, $foto)) {

                $_SESSION['success'] =
                    'Produk berhasil ditambahkan! 🎉';

                header('Location: index.php');

                exit;

            } else {

                $error = 'Gagal menambahkan produk!';

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Produk - MyKasir</title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard_modern.css"
    >

    <style>

        .form-wrapper{
            display:flex;
            justify-content:center;
            margin-top:24px;
        }

        .form-card{
            width:100%;
            max-width:620px;
            background:white;
            border-radius:24px;
            padding:32px;
            box-shadow:0 8px 30px rgba(0,0,0,0.05);
        }

        .form-header{
            margin-bottom:28px;
        }

        .form-header h2{
            font-size:32px;
            color:#0f172a;
            margin-bottom:8px;
        }

        .form-header p{
            color:#64748b;
            font-size:15px;
        }

        .alert-danger{
            background:#fee2e2;
            color:#991b1b;
            padding:14px 16px;
            border-radius:14px;
            margin-bottom:20px;
            font-size:14px;
            font-weight:600;
        }

        .form-group{
            margin-bottom:22px;
        }

        .form-group label{
            display:block;
            margin-bottom:10px;
            font-weight:700;
            color:#1e293b;
            font-size:15px;
        }

        .form-input{
            width:100%;
            padding:14px 16px;
            border:1px solid #dbeafe;
            border-radius:14px;
            font-size:15px;
            transition:.3s;
            outline:none;
            background:#f8fafc;
        }

        .form-input:focus{
            border-color:#2563eb;
            background:white;
            box-shadow:0 0 0 4px rgba(37,99,235,0.08);
        }

        .file-upload{
            padding:18px;
            border:2px dashed #cbd5e1;
            border-radius:18px;
            background:#f8fafc;
            text-align:center;
            transition:.3s;
        }

        .file-upload:hover{
            border-color:#2563eb;
            background:#eff6ff;
        }

        .file-upload input{
            margin-top:12px;
        }

        .upload-text{
            color:#64748b;
            font-size:14px;
        }

        .button-group{
            display:flex;
            gap:14px;
            margin-top:30px;
        }

        .btn-save{
            flex:1;
            border:none;
            background:#22c55e;
            color:white;
            padding:15px;
            border-radius:14px;
            font-size:15px;
            font-weight:700;
            cursor:pointer;
            transition:.3s;
        }

        .btn-save:hover{
            background:#16a34a;
            transform:translateY(-2px);
        }

        .btn-cancel{
            flex:1;
            text-decoration:none;
            background:#ef4444;
            color:white;
            padding:15px;
            border-radius:14px;
            text-align:center;
            font-size:15px;
            font-weight:700;
            transition:.3s;
        }

        .btn-cancel:hover{
            background:#dc2626;
            transform:translateY(-2px);
        }

        .preview-image{
            width:100%;
            max-height:260px;
            object-fit:cover;
            border-radius:18px;
            margin-top:18px;
            display:none;
            border:1px solid #e2e8f0;
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
                    <?= strtoupper(
                        substr($_SESSION['user']['username'], 0, 1)
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
        <header class="content-header">

            <div class="header-left">
                <h1>Tambah Produk</h1>
                <p class="subtitle">
                    Tambahkan produk baru ke toko 🚀
                </p>
            </div>

            <div class="header-right">
                <button class="btn-icon">🔔</button>
                <button class="btn-icon">⚙️</button>
            </div>

        </header>

        <!-- FORM -->
        <div class="form-wrapper">

            <div class="form-card">

                <div class="form-header">
                    <h2>📦 Produk Baru</h2>
                    <p>
                        Isi informasi produk dengan lengkap
                    </p>
                </div>

                <?php if ($error): ?>

                    <div class="alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <!-- Nama -->
                    <div class="form-group">

                        <label>Nama Produk</label>

                        <input
                            type="text"
                            name="nama_produk"
                            class="form-input"
                            placeholder="Contoh: Keyboard Gaming RGB"
                            value="<?= htmlspecialchars(
                                $_POST['nama_produk'] ?? ''
                            ) ?>"
                            required
                        >

                    </div>

                    <!-- Harga -->
                    <div class="form-group">

                        <label>Harga Produk</label>

                        <input
                            type="number"
                            name="harga"
                            class="form-input"
                            placeholder="Contoh: 150000"
                            min="0"
                            value="<?= htmlspecialchars(
                                $_POST['harga'] ?? ''
                            ) ?>"
                            required
                        >

                    </div>

                    <!-- Stok -->
                    <div class="form-group">

                        <label>Jumlah Stok</label>

                        <input
                            type="number"
                            name="stok"
                            class="form-input"
                            placeholder="Contoh: 20"
                            min="0"
                            value="<?= htmlspecialchars(
                                $_POST['stok'] ?? ''
                            ) ?>"
                            required
                        >

                    </div>

                    <!-- Foto -->
                    <div class="form-group">

                        <label>Foto Produk</label>

                        <div class="file-upload">

                            <div class="upload-text">
                                📸 Upload gambar produk
                                <br>
                                <small>
                                    JPG, PNG, WEBP • Maksimal 2MB
                                </small>
                            </div>

                            <input
                                type="file"
                                name="foto"
                                id="foto"
                                accept=".jpg,.jpeg,.png,.webp"
                                onchange="previewImage(event)"
                            >

                            <img
                                id="preview"
                                class="preview-image"
                            >

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="button-group">

                        <button
                            type="submit"
                            class="btn-save"
                        >
                            💾 Simpan Produk
                        </button>

                        <a
                            href="index.php"
                            class="btn-cancel"
                        >
                            ✖ Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<script>

function previewImage(event){

    const preview = document.getElementById('preview');

    preview.src = URL.createObjectURL(event.target.files[0]);

    preview.style.display = 'block';

}

</script>

</body>
</html>
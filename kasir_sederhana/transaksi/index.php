<?php
session_start();

require_once '../classes/user.php';
User::cekLogin();

// Logika dinamis untuk menentukan nama Role tampilan
$namaRole = (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin')
    ? 'Admin'
    : 'Kasir';

require_once '../classes/Produk.php';
require_once '../classes/Transaksi.php';

$produk = new Produk();
$transaksi = new Transaksi();

$semuaProduk = $produk->getAll();

// HAPUS baris penimpaan variabel statis yang sebelumnya ada di sini
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - MyKasir</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <style>
        .kasir-layout{
            display:grid;
            grid-template-columns: 1fr 380px;
            gap:24px;
            margin-top:24px;
        }
        .products-container, .cart-container{
            background:white;
            border-radius:20px;
            padding:24px;
            box-shadow:0 4px 12px rgba(0,0,0,0.04);
        }
        .section-title{ margin-bottom:20px; }
        .section-title h2{ font-size:28px; color:#0f172a; margin-bottom:5px; }
        .section-title p{ color:#64748b; font-size:14px; }
        .products-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:20px; }
        .product-card{ border:1px solid #e2e8f0; border-radius:18px; overflow:hidden; transition:.3s; background:white; }
        .product-card:hover{ transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.08); }
        .product-image{ height:180px; background:#f8fafc; display:flex; align-items:center; justify-content:center; }
        .product-image img{ width:100%; height:100%; object-fit:cover; }
        .no-image{ font-size:50px; color:#94a3b8; }
        .product-content{ padding:18px; }
        .product-name{ font-size:18px; font-weight:700; color:#1e293b; margin-bottom:8px; }
        .product-price{ color:#16a34a; font-size:20px; font-weight:700; margin-bottom:10px; }
        .product-stock{ display:inline-block; padding:6px 12px; border-radius:999px; font-size:13px; font-weight:600; margin-bottom:16px; }
        .stock-available{ background:#dcfce7; color:#15803d; }
        .stock-empty{ background:#fee2e2; color:#dc2626; }
        
        /* Tambahan style untuk input kuantitas massal di produk-card */
        .input-qty-massal {
            width: 100%;
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 10px;
            outline: none;
            font-size: 14px;
            text-align: center;
        }
        .input-qty-massal:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .btn-cart{ width:100%; border:none; padding:12px; border-radius:12px; background:#22c55e; color:white; font-weight:700; cursor:pointer; transition:.3s; }
        .btn-cart:hover{ background:#16a34a; }
        .btn-disabled{ width:100%; border:none; padding:12px; border-radius:12px; background:#cbd5e1; color:white; font-weight:700; cursor:not-allowed; }
        .cart-container{ position:sticky; top:20px; align-self:start; }
        .cart-empty{ text-align:center; padding:40px 0; color:#94a3b8; }
        .cart-table{ width:100%; border-collapse:collapse; }
        .cart-table th{ text-align:left; padding-bottom:12px; color:#64748b; font-size:13px; }
        .cart-table td{ padding:14px 0; border-top:1px solid #f1f5f9; font-size:14px; }
        .qty-box{ display:flex; align-items:center; gap:8px; }
        .qty-btn{ width:26px; height:26px; border:none; border-radius:8px; background:#e2e8f0; cursor:pointer; font-weight:700; }
        .delete-btn{ border:none; background:none; color:#ef4444; cursor:pointer; font-size:16px; }
        .total-section{ margin-top:20px; border-top:1px solid #e2e8f0; padding-top:20px; }
        .total-row{ display:flex; justify-content:space-between; font-size:18px; font-weight:700; margin-bottom:18px; }
        .btn-success{ width:100%; border:none; padding:14px; border-radius:12px; background:#16a34a; color:white; font-weight:700; cursor:pointer; transition:.3s; }
        .btn-success:hover{ background:#15803d; }
        .btn-danger{ width:100%; border:none; padding:14px; border-radius:12px; background:#ef4444; color:white; font-weight:700; cursor:pointer; transition:.3s; margin-top:10px; }
        .btn-danger:hover{ background:#dc2626; }
        @media(max-width:1100px){
            .kasir-layout{ grid-template-columns:1fr; }
            .cart-container{ position:relative; top:0; }
        }
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
            <a href="index.php" class="nav-item active">
                <span class="icon">💰</span>
                <span>Kasir</span>
            </a>
            <a href="riwayat.php" class="nav-item">
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
                <h1>Kasir</h1>
                <p class="subtitle">Pilih produk dan lakukan transaksi ✨</p>
            </div>
            <div class="header-right">
                <button class="btn-icon">🔔</button>
                <button class="btn-icon">⚙️</button>
            </div>
        </header>

        <div class="kasir-layout">
            <div class="products-container">
                <div class="section-title">
                    <h2>Pilih Produk</h2>
                    <p>Tambahkan produk ke keranjang belanja</p>
                </div>

                <div class="products-grid">
                    <?php foreach ($semuaProduk as $p): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($p['foto']): ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                            <?php else: ?>
                                <div class="no-image">📦</div>
                            <?php endif; ?>
                        </div>

                        <div class="product-content">
                            <div class="product-name"><?= htmlspecialchars($p['nama_produk']) ?></div>
                            <div class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>

                            <?php if ($p['stok'] > 0): ?>
                                <div class="product-stock stock-available">Stok <?= $p['stok'] ?></div>
                                
                                <input type="number" id="input_qty_<?= $p['id'] ?>" class="input-qty-massal" value="1" min="1" max="<?= $p['stok'] ?>" placeholder="Jumlah">
                                
                                <button class="btn-cart" onclick="tambahKeKeranjangMassal(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nama_produk'], ENT_QUOTES) ?>', <?= $p['harga'] ?>, <?= $p['stok'] ?>)">
                                    🛒 Tambah
                                </button>
                            <?php else: ?>
                                <div class="product-stock stock-empty">Stok Habis</div>
                                <button class="btn-disabled" disabled>Tidak Tersedia</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cart-container">
                <div class="section-title">
                    <h2>🧾 Keranjang</h2>
                    <p>Daftar produk yang dipilih</p>
                </div>

                <div id="keranjang-kosong" class="cart-empty">Keranjang masih kosong 🛒</div>

                <table id="tabel-keranjang" class="cart-table" style="display:none">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="isi-keranjang"></tbody>
                </table>

                <div id="total-section" class="total-section" style="display:none">
                    <div class="total-row">
                        <span>Total</span>
                        <span id="total-harga">Rp 0</span>
                    </div>

                    <form method="POST" action="proses.php" id="form-transaksi">
                        <input type="hidden" name="items" id="input-items">
                        <button type="submit" class="btn-success" onclick="return submitTransaksi()">
                            💳 Bayar Sekarang
                        </button>
                    </form>
                    <button class="btn-danger" onclick="resetKeranjang()">Reset Keranjang</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
let keranjang = JSON.parse(localStorage.getItem('get_items_frozen')) || JSON.parse(localStorage.getItem('keranjang')) || [];

// Fungsi baru untuk memasukkan produk dalam jumlah banyak sekaligus
function tambahKeKeranjangMassal(id, nama, harga, stok){
    const inputQty = document.getElementById('input_qty_' + id);
    const qtyBeli = parseInt(inputQty.value);

    // Validasi input kuantitas
    if (isNaN(qtyBeli) || qtyBeli <= 0) {
        alert('Masukkan jumlah pembelian yang valid!');
        return;
    }

    const existing = keranjang.find(item => item.id === id);
    
    if(existing){
        // Jika produk sudah ada di keranjang, jumlahkan qty lama dengan input qty baru
        if((existing.qty + qtyBeli) > stok){
            alert('Gagal menambah! Total di keranjang melebihi batas stok tersedia.');
            return;
        }
        existing.qty += qtyBeli;
        existing.subtotal = existing.qty * existing.harga;
    } else {
        // Jika produk baru pertama kali dimasukkan ke keranjang
        if(qtyBeli > stok){
            alert('Stok tidak mencukupi!');
            return;
        }
        keranjang.push({ id, nama, harga, qty: qtyBeli, subtotal: (harga * qtyBeli), stok });
    }

    // Reset nilai input field produk kembali menjadi 1
    inputQty.value = 1;

    localStorage.setItem('keranjang', JSON.stringify(keranjang));
    renderKeranjang();
}

// Fungsi internal bawaan (untuk tombol + di dalam list tabel keranjang belanja)
function tambahKeKeranjang(id, nama, harga, stok){
    const existing = keranjang.find(item => item.id === id);
    if(existing){
        if(existing.qty >= stok){
            alert('Stok tidak mencukupi!');
            return;
        }
        existing.qty++;
        existing.subtotal = existing.qty * existing.harga;
    } else {
        keranjang.push({ id, nama, harga, qty:1, subtotal:harga, stok });
    }
    localStorage.setItem('keranjang', JSON.stringify(keranjang));
    renderKeranjang();
}

function kurangiQty(id){
    const index = keranjang.findIndex(item => item.id === id);
    if(index === -1) return;

    keranjang[index].qty--;
    keranjang[index].subtotal = keranjang[index].qty * keranjang[index].harga;

    if(keranjang[index].qty <= 0){
        keranjang.splice(index,1);
    }
    localStorage.setItem('keranjang', JSON.stringify(keranjang));
    renderKeranjang();
}

function hapusDariKeranjang(id){
    keranjang = keranjang.filter(item => item.id !== id);
    localStorage.setItem('keranjang', JSON.stringify(keranjang));
    renderKeranjang();
}

function renderKeranjang(){
    const tbody = document.getElementById('isi-keranjang');
    const tabel = document.getElementById('tabel-keranjang');
    const kosong = document.getElementById('keranjang-kosong');
    const totalSec = document.getElementById('total-section');
    const totalHarga = document.getElementById('total-harga');

    tbody.innerHTML = '';

    if(keranjang.length === 0){
        tabel.style.display = 'none';
        kosong.style.display = 'block';
        totalSec.style.display = 'none';
        return;
    }

    tabel.style.display = 'table';
    kosong.style.display = 'none';
    totalSec.style.display = 'block';

    let total = 0;
    keranjang.forEach(item => {
        total += item.subtotal;
        tbody.innerHTML += `
            <tr>
                <td>${item.nama}</td>
                <td>
                    <div class="qty-box">
                        <button class="qty-btn" onclick="kurangiQty(${item.id})">−</button>
                        <span>${item.qty}</span>
                        <button class="qty-btn" onclick="tambahKeKeranjang(${item.id}, '${item.nama}', ${item.harga}, ${item.stok})">+</button>
                    </div>
                </td>
                <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td>
                    <button class="delete-btn" onclick="hapusDariKeranjang(${item.id})">✕</button>
                </td>
            </tr>
        `;
    });
    totalHarga.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function submitTransaksi(){
    if(keranjang.length === 0){
        alert('Keranjang masih kosong!');
        return false;
    }
    document.getElementById('input-items').value = JSON.stringify(keranjang);
    localStorage.removeItem('keranjang');
    return true;
}

function resetKeranjang(){
    if(confirm('Yakin ingin mereset keranjang?')){
        keranjang = [];
        localStorage.removeItem('keranjang');
        renderKeranjang();
    }
}

renderKeranjang();
</script>
</body>
</html>
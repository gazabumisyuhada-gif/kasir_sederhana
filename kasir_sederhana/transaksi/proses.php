<?php
session_start();
require_once '../classes/user.php';
// Pastikan user memang sudah login sebelum memproses data
User::cekLogin(); 

require_once '../classes/Transaksi.php';
require_once '../classes/Produk.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$items   = json_decode($_POST['items'], true);
$id_user = $_SESSION['user']['id'];

if (empty($items)) {
    $_SESSION['error'] = 'Keranjang kosong!';
    header('Location: index.php');
    exit;
}

$produk    = new Produk();
$dataItems = [];

// 1. Validasi awal untuk memastikan seluruh stok mencukupi
foreach ($items as $item) {
    $p = $produk->getById($item['id']);

    if (!$p || $p['stok'] < $item['qty']) {
        $_SESSION['error'] = "Stok produk '{$item['nama']}' tidak mencukupi!";
        header('Location: index.php');
        exit;
    }

    $dataItems[] = [
        'id_produk'    => $item['id'],
        'qty'          => $item['qty'],
        'harga_satuan' => $item['harga'],
        'subtotal'     => $item['subtotal']
    ];
}

$transaksi = new Transaksi();

try {
    // 2. Mulai Database Transaction jika menggunakan PDO
    // Catatan: Jika properti koneksi database di kelasmu bersifat privat,
    // disarankan untuk memindahkan blok transaction ini ke dalam method $transaksi->simpan()
    
    // Memulai proses simpan data transaksi & detailnya
    $id_transaksi = $transaksi->simpan($id_user, $dataItems);

    if ($id_transaksi) {
        // 3. Kurangi stok setiap produk
        foreach ($items as $item) {
            $produk->kurangiStok($item['id'], $item['qty']);
        }
        
        // Jika semua query berhasil tanpa error, data akan dikunci aman di database
        header("Location: struk.php?id={$id_transaksi}");
        exit;
    } else {
        throw new Exception('Transaksi gagal disimpan ke sistem!');
    }

} catch (Exception $e) {
    // Jika ada satu saja error/gagal di tengah jalan, batalkan semua perubahan data
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit;
}
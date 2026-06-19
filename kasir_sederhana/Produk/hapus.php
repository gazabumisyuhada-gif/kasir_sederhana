<?php
session_start();
require_once '../classes/user.php';
User::cekLogin();
User::cekAdmin();                   

require_once '../classes/Produk.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'ID produk tidak valid!';
    header('Location: index.php');
    exit;
}

$produk = new Produk();
$data   = $produk->getById($id);

if (!$data) {
    $_SESSION['error'] = 'Produk tidak ditemukan!';
    header('Location: index.php');
    exit;
}

$hasil = $produk->hapus($id);

if ($hasil === 'used') {
    $_SESSION['error'] = 'Produk tidak bisa dihapus karena sudah pernah digunakan dalam transaksi!';
} elseif ($hasil) {
    $_SESSION['success'] = 'Produk berhasil dihapus!';
} else {
    $_SESSION['error'] = 'Gagal menghapus produk!';
}

header('Location: index.php');
exit;
?>
<?php
require_once __DIR__ . '/../config/database.php';

class Transaksi {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function simpan($id_user, $items) {
        try {
            $this->conn->beginTransaction();

            $total = 0;
            foreach ($items as $item) {
                $total += $item['subtotal'];
            }

            $query = "INSERT INTO transaksi (id_user, total_harga)
                      VALUES (:id_user, :total)";
            $stmt  = $this->conn->prepare($query);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            $id_transaksi = $this->conn->lastInsertId();

            foreach ($items as $item) {
                $query2 = "INSERT INTO detail_transaksi
                           (id_transaksi, id_produk, qty, harga_satuan, subtotal)
                           VALUES (:id_trx, :id_produk, :qty, :harga, :subtotal)";
                $stmt2  = $this->conn->prepare($query2);
                $stmt2->bindParam(':id_trx', $id_transaksi);
                $stmt2->bindParam(':id_produk', $item['id_produk']);
                $stmt2->bindParam(':qty', $item['qty']);
                $stmt2->bindParam(':harga', $item['harga_satuan']);
                $stmt2->bindParam(':subtotal', $item['subtotal']);
                $stmt2->execute();
            }

            $this->conn->commit();
            return $id_transaksi;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getRiwayat() {
        $query = "SELECT t.*, u.username
                  FROM transaksi t
                  JOIN users u ON t.id_user = u.id
                  ORDER BY t.tanggal DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDetail($id_transaksi) {
        $query = "SELECT dt.*, p.nama_produk
                  FROM detail_transaksi dt
                  JOIN produk p ON dt.id_produk = p.id
                  WHERE dt.id_transaksi = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_transaksi);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
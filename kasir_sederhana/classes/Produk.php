<?php
require_once __DIR__ . '/../config/database.php';

class Produk {
    private $conn;
    private $table = 'produk';
    public $id;
    public $nama_produk;
    public $harga;
    public $stok;
    public $foto;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function tambah($nama, $harga, $stok, $foto) {
        $query = "INSERT INTO {$this->table} (nama_produk, harga, stok, foto)
                  VALUES (:nama, :harga, :stok, :foto)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':foto', $foto);
        return $stmt->execute();
    }

    public function edit($id, $nama, $harga, $stok, $foto = null) {
        if ($foto) {
             $query = "UPDATE {$this->table}
              SET nama_produk = :nama, harga = :harga, stok = :stok, foto = :foto
              WHERE id = :id";
        } else {
            $query = "UPDATE {$this->table}
              SET nama_produk = :nama, harga = :harga, stok = :stok
              WHERE id = :id";
        }
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':stok', $stok);
        if ($foto) {
            $stmt->bindParam(':foto',$foto);
        }
        return $stmt->execute();
    }

    public function hapus($id) {
        $cek = $this->conn->prepare("SELECT COUNT(*) as total FROM detail_transaksi WHERE id_produk = :id");
        $cek->bindParam(':id', $id);
        $cek->execute();
        $hasil = $cek->fetch();

        if ($hasil['total'] > 0) {
            return 'used';
        }

        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function kurangiStok($id, $qty) {
        $query = "UPDATE {$this->table}
                  SET stok = stok - :qty
                  WHERE id = :id AND stok >= :qty";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':qty', $qty);
        return $stmt->execute();
    }
}
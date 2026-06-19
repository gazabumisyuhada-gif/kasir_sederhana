<?php

require_once __DIR__ . '/../config/database.php';

class User {

    private $conn;
    private $table = 'users';

    public function __construct() {

        $db = new Database();
        $this->conn = $db->connect();

    }

    /*
    |-----------------------------------
    | LOGIN
    |-----------------------------------
    */
    public function login($username, $password) {

    $query = "SELECT * FROM {$this->table}
              WHERE username = :username
              AND password = MD5(:password)
              LIMIT 1";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    /*
    |-----------------------------------
    | CEK LOGIN
    |-----------------------------------
    */
    public static function cekLogin() {

        if (!isset($_SESSION['user'])) {

            $path =
                strpos($_SERVER['PHP_SELF'], '/transaksi/') !== false
                || strpos($_SERVER['PHP_SELF'], '/Produk/') !== false
                || strpos($_SERVER['PHP_SELF'], '/produk/') !== false
                ? '../login.php'
                : 'login.php';

            header('Location: ' . $path);
            exit;

        }

    }
    public static function cekAdmin() {

    if (
        !isset($_SESSION['user']) ||
        $_SESSION['user']['role'] !== 'admin'
    ) {

        header('Location: ../dashboard.php');
        exit;

    }
    

}

}

?>
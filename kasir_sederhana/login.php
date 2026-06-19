<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'classes/user.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $user = new User();
        $data = $user->login($username, $password);

        if ($data) {
            $_SESSION['user'] = [
                'id'       => $data['id'],
                'username' => $data['username'],
                'role'     => $data['role']
            ];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kasir</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <!-- Bagian Kiri -->
    <div class="login-left">
        <div class="brand-logo">Mykasir</div>
        <div class="illustration">
            <h2>Selamat Datang Kembali!</h2>
            <p>Kelola bisnis Anda dengan lebih mudah</p>
        </div>
    </div>

    <!-- Bagian Kanan -->
    <div class="login-right">
        <div class="login-form">
            <h1>Log In</h1>
            <p class="subtitle">Masuk ke akun Anda</p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text"
                           name="username"
                           placeholder="Masukkan username Anda"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           placeholder="Masukkan password Anda"
                           required>
                </div>

                <button type="submit" class="btn-login">Log In</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
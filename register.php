<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];

    if ($username === '' || $nama === '' || $password === '') {
        $error = "Semua field wajib diisi!";
    } else {
        // Cek username sudah ada atau belum
        $stmt = mysqli_prepare($conn, "SELECT id FROM login WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, "INSERT INTO login (username, password, nama_lengkap) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "sss", $username, $hash, $nama);
            if (mysqli_stmt_execute($stmt2)) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Gagal mendaftar, coba lagi.";
            }
            mysqli_stmt_close($stmt2);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Toko App</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-person-plus" style="font-size:2.5rem;color:#0d47a1;"></i>
            <h3 class="mt-2 mb-0">Register</h3>
            <p class="text-muted">Buat akun baru</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>
</body>
</html>

<?php
session_start();
require_once 'config/database.php';

// Jika sudah login, langsung arahkan ke halaman data
if (isset($_SESSION['user_id'])) {
    header("Location: data.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Ambil user berdasarkan username menggunakan prepared statement (anti SQL Injection)
    $stmt = mysqli_prepare($conn, "SELECT id, username, password, nama_lengkap FROM login WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verifikasi password dengan password_verify (password di-hash)
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id']      = $row['id'];
            $_SESSION['username']     = $row['username'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            header("Location: data.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko App</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-shop" style="font-size:2.5rem;color:#0d47a1;"></i>
            <h3 class="mt-2 mb-0">Login</h3>
            <p class="text-muted">Toko App - Data Barang</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Don't have an account? <a href="register.php">Register</a>
        </p>
        <p class="text-center text-muted small mt-2">
            Default: <b>admin</b> / <b>admin123</b>
        </p>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Toko App</title>
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color:#0d47a1;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="data.php">
            <i class="bi bi-shop"></i> Toko App
        </a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3"><i class="bi bi-person-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin'); ?></span>
            <a href="logout.php" class="btn btn-sm btn-light text-primary fw-semibold">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3" style="background-color:#1565c0; min-height: calc(100vh - 56px); width:220px;">
        <ul class="nav flex-column">
            <li class="nav-item mb-1">
                <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF'])=='data.php')?'active-link':''; ?>" href="data.php">
                    <i class="bi bi-box-seam"></i> Data Barang
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="report.php" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Report PDF
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-white" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="flex-fill p-4" style="background-color:#f4f8fd; min-height: calc(100vh - 56px);">

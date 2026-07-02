<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil nama file gambar sebelum data dihapus
$stmt = mysqli_prepare($conn, "SELECT gambar FROM barang WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($row) {
    // Hapus data dari database
    $stmtDel = mysqli_prepare($conn, "DELETE FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $id);
    mysqli_stmt_execute($stmtDel);
    mysqli_stmt_close($stmtDel);

    // Hapus file gambar terkait jika ada
    if (!empty($row['gambar']) && file_exists('assets/uploads/' . $row['gambar'])) {
        unlink('assets/uploads/' . $row['gambar']);
    }
}

header("Location: data.php?notif=deleted");
exit;

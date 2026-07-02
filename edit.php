<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$page_title = "Edit Data";
$error = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data barang yang akan diedit
$stmt = mysqli_prepare($conn, "SELECT * FROM barang WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header("Location: data.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori    = trim($_POST['kategori']);
    $harga       = (float) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambarName  = $data['gambar']; // default tetap gambar lama

    if ($kode_barang === '' || $nama_barang === '' || $kategori === '' || $harga < 0 || $stok < 0) {
        $error = "Mohon lengkapi semua field dengan benar!";
    }

    // ================== UPLOAD GAMBAR BARU (opsional) ==================
    if ($error === '' && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileTmp  = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['gambar']['size'];

        if (!in_array($fileExt, $allowedExt)) {
            $error = "Format gambar harus jpg, jpeg, png, atau gif.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 2MB.";
        } else {
            $newGambarName = 'barang_' . time() . '_' . uniqid() . '.' . $fileExt;
            $uploadPath = 'assets/uploads/' . $newGambarName;
            if (move_uploaded_file($fileTmp, $uploadPath)) {
                // Hapus gambar lama jika ada
                if (!empty($data['gambar']) && file_exists('assets/uploads/' . $data['gambar'])) {
                    unlink('assets/uploads/' . $data['gambar']);
                }
                $gambarName = $newGambarName;
            } else {
                $error = "Gagal mengupload gambar baru.";
            }
        }
    }

    if ($error === '') {
        $stmt2 = mysqli_prepare($conn, "UPDATE barang SET kode_barang=?, nama_barang=?, kategori=?, harga=?, stok=?, deskripsi=?, gambar=? WHERE id=?");
        mysqli_stmt_bind_param($stmt2, "sssdissi", $kode_barang, $nama_barang, $kategori, $harga, $stok, $deskripsi, $gambarName, $id);

        if (mysqli_stmt_execute($stmt2)) {
            header("Location: data.php?notif=updated");
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt2);
    }

    // supaya form tetap menampilkan input terbaru saat error
    $data = array_merge($data, [
        'kode_barang' => $kode_barang, 'nama_barang' => $nama_barang, 'kategori' => $kategori,
        'harga' => $harga, 'stok' => $stok, 'deskripsi' => $deskripsi
    ]);
}

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header"><i class="bi bi-pencil"></i> Edit Data Barang</div>
    <div class="card-body">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" value="<?php echo htmlspecialchars($data['kode_barang']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="<?php echo htmlspecialchars($data['nama_barang']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($data['kategori']); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" step="0.01" min="0" name="harga" class="form-control" value="<?php echo htmlspecialchars($data['harga']); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" min="0" name="stok" class="form-control" value="<?php echo htmlspecialchars($data['stok']); ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gambar Saat Ini</label><br>
                    <?php if (!empty($data['gambar']) && file_exists('assets/uploads/' . $data['gambar'])): ?>
                        <img src="assets/uploads/<?php echo htmlspecialchars($data['gambar']); ?>" class="thumb-barang mb-2" alt="gambar"><br>
                    <?php else: ?>
                        <span class="text-muted d-block mb-2">Belum ada gambar</span>
                    <?php endif; ?>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    <div class="form-text">Kosongkan jika tidak ingin mengganti gambar.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
            <a href="data.php" class="btn btn-outline-secondary">Batal</a>
        </form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

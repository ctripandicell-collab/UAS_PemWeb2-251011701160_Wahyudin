<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$page_title = "Tambah Data";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori    = trim($_POST['kategori']);
    $harga       = (float) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambarName  = null;

    // ================== VALIDASI ==================
    if ($kode_barang === '' || $nama_barang === '' || $kategori === '' || $harga < 0 || $stok < 0) {
        $error = "Mohon lengkapi semua field dengan benar!";
    }

    // ================== UPLOAD GAMBAR ==================
    if ($error === '' && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileTmp  = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['gambar']['size'];

        if (!in_array($fileExt, $allowedExt)) {
            $error = "Format gambar harus jpg, jpeg, png, atau gif.";
        } elseif ($fileSize > 2 * 1024 * 1024) { // max 2MB
            $error = "Ukuran gambar maksimal 2MB.";
        } else {
            $gambarName = 'barang_' . time() . '_' . uniqid() . '.' . $fileExt;
            $uploadPath = 'assets/uploads/' . $gambarName;
            if (!move_uploaded_file($fileTmp, $uploadPath)) {
                $error = "Gagal mengupload gambar.";
                $gambarName = null;
            }
        }
    }

    // ================== SIMPAN KE DATABASE ==================
    if ($error === '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO barang (kode_barang, nama_barang, kategori, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssdiss", $kode_barang, $nama_barang, $kategori, $harga, $stok, $deskripsi, $gambarName);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: data.php?notif=created");
            exit;
        } else {
            $error = mysqli_error($conn) === '' ? "Gagal menyimpan data." : ("Kode barang sudah digunakan atau terjadi error: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }
}

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header"><i class="bi bi-plus-circle"></i> Tambah Data Barang</div>
    <div class="card-body">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="create.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" value="<?php echo htmlspecialchars($_POST['kode_barang'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="<?php echo htmlspecialchars($_POST['nama_barang'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($_POST['kategori'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" step="0.01" min="0" name="harga" class="form-control" value="<?php echo htmlspecialchars($_POST['harga'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" min="0" name="stok" class="form-control" value="<?php echo htmlspecialchars($_POST['stok'] ?? ''); ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Upload Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maks 2MB.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="data.php" class="btn btn-outline-secondary">Batal</a>
        </form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

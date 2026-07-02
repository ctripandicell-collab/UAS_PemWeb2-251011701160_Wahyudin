<?php
require_once 'includes/auth_check.php'; // wajib login
require_once 'config/database.php';

$page_title = "Data Barang";

// ================== FITUR SEARCH ==================
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// ================== PAGINATION ==================
$limit = isset($_GET['show']) ? (int)$_GET['show'] : 10;
if ($limit <= 0) $limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;

// Query dengan pencarian (prepared statement)
$where = "WHERE nama_barang LIKE ? OR kode_barang LIKE ? OR kategori LIKE ?";
$searchParam = "%$keyword%";

// Hitung total data untuk pagination
$stmtCount = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM barang $where");
mysqli_stmt_bind_param($stmtCount, "sss", $searchParam, $searchParam, $searchParam);
mysqli_stmt_execute($stmtCount);
$totalRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount));
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data barang sesuai pencarian & pagination
$stmt = mysqli_prepare($conn, "SELECT * FROM barang $where ORDER BY id DESC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, "sssii", $searchParam, $searchParam, $searchParam, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Notifikasi dari proses create/update/delete
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-seam"></i> Data Barang Toko</span>
    </div>
    <div class="card-body">

        <?php if ($notif === 'created'): ?>
            <div class="alert alert-success">Data barang berhasil ditambahkan.</div>
        <?php elseif ($notif === 'updated'): ?>
            <div class="alert alert-success">Data barang berhasil diperbarui.</div>
        <?php elseif ($notif === 'deleted'): ?>
            <div class="alert alert-success">Data barang berhasil dihapus.</div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Data</a>
                <a href="report.php" target="_blank" class="btn btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i> Report Data (PDF)</a>
            </div>

            <!-- Form Search -->
            <form method="GET" action="data.php" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control" placeholder="Search kode/nama/kategori..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                <?php if ($keyword !== ''): ?>
                    <a href="data.php" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <form method="GET" action="data.php" class="d-flex align-items-center gap-2">
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                Show
                <select name="show" class="form-select form-select-sm" style="width:80px;" onchange="this.form.submit()">
                    <?php foreach ([10, 25, 50, 100] as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php echo $limit == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
                entries
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Deskripsi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): $no = $offset + 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if (!empty($row['gambar']) && file_exists('assets/uploads/' . $row['gambar'])): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($row['gambar']); ?>" class="thumb-barang" alt="gambar">
                                <?php else: ?>
                                    <span class="text-muted small">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['kode_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo (int)$row['stok']; ?></td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($row['deskripsi'] ?? '-', 0, 40, '...')); ?></td>
                            <td class="text-nowrap">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i> Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="bi bi-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Data tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?php echo $totalData > 0 ? $offset + 1 : 0; ?> to <?php echo min($offset + $limit, $totalData); ?> of <?php echo $totalData; ?> entries
            </small>

            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&show=<?php echo $limit; ?>&keyword=<?php echo urlencode($keyword); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

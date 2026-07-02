<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'libs/fpdf.php';

// Ambil semua data barang
$result = mysqli_query($conn, "SELECT * FROM barang ORDER BY id ASC");

class PDF extends FPDF
{
    // Header setiap halaman
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(13, 71, 161); // biru
        $this->Cell(0, 8, 'LAPORAN DATA BARANG TOKO', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 6, 'Dicetak pada: ' . date('d-m-Y H:i'), 0, 1, 'C');
        $this->Ln(2);

        // Table header
        $this->SetFillColor(13, 71, 161);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 8, 'No', 1, 0, 'C', true);
        $this->Cell(22, 8, 'Gambar', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Kode', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Nama Barang', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Kategori', 1, 0, 'C', true);
        $this->Cell(28, 8, 'Harga', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Stok', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Total', 1, 1, 'C', true);
    }

    // Footer setiap halaman
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);

$no = 1;
$rowH = 20; // tinggi baris agar cukup untuk gambar

while ($row = mysqli_fetch_assoc($result)) {

    // Jika baris berikutnya akan melewati batas halaman, buat halaman baru
    if ($pdf->GetY() + $rowH > 270) {
        $pdf->AddPage();
    }

    $y = $pdf->GetY();
    $x = $pdf->GetX();

    $pdf->SetFont('Arial', '', 9);

    // No
    $pdf->Cell(10, $rowH, $no, 1, 0, 'C');

    // Gambar
    $pdf->Cell(22, $rowH, '', 1, 0, 'C'); // cell kosong sebagai border, gambar ditumpuk di atasnya
    $imgPath = 'assets/uploads/' . $row['gambar'];
    if (!empty($row['gambar']) && file_exists($imgPath)) {
        $imgInfo = @getimagesize($imgPath);
        if ($imgInfo) {
            // Tempatkan gambar di tengah cell (lebar 22mm, tinggi rowH)
            $pdf->Image($imgPath, $x + 3, $y + 2, 16, $rowH - 4);
        }
    } else {
        $pdf->SetXY($x + 22, $y);
    }

    // Kode
    $pdf->SetXY($x + 32, $y);
    $pdf->Cell(25, $rowH, $row['kode_barang'], 1, 0, 'C');

    // Nama Barang
    $pdf->SetXY($x + 57, $y);
    $pdf->Cell(45, $rowH, $row['nama_barang'], 1, 0, 'L');

    // Kategori
    $pdf->SetXY($x + 102, $y);
    $pdf->Cell(30, $rowH, $row['kategori'], 1, 0, 'L');

    // Harga
    $pdf->SetXY($x + 132, $y);
    $pdf->Cell(28, $rowH, 'Rp ' . number_format($row['harga'], 0, ',', '.'), 1, 0, 'R');

    // Stok
    $pdf->SetXY($x + 160, $y);
    $pdf->Cell(15, $rowH, $row['stok'], 1, 0, 'C');

    // Total (harga x stok)
    $pdf->SetXY($x + 175, $y);
    $total = $row['harga'] * $row['stok'];
    $pdf->Cell(15, $rowH, number_format($total / 1000, 0) . 'K', 1, 1, 'C');

    $no++;
}

if ($no === 1) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Belum ada data barang.', 0, 1, 'C');
}

// Output langsung ke browser
$pdf->Output('I', 'Laporan_Data_Barang_' . date('Ymd_His') . '.pdf');

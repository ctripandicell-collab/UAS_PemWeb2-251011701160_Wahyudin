<?php
/**
 * Konfigurasi Koneksi Database
 * UAS Pemrograman Web 2 - Data Barang Toko
 */

// -- Ubah sesuai konfigurasi server lokal Anda --
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_toko');

// Membuat koneksi menggunakan MySQLi (procedural)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset agar aman dari masalah encoding
mysqli_set_charset($conn, "utf8mb4");

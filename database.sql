-- =========================================================
-- Database: db_toko
-- UAS Pemrograman Web 2 - Data Barang Toko (Kategori digit NIM: 0)
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_toko;
USE db_toko;

-- Table untuk login
CREATE TABLE IF NOT EXISTS login (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun default: username = admin, password = admin123
INSERT INTO login (username, password, nama_lengkap) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');
-- hash di atas adalah password_hash('admin123')

-- Table untuk data barang toko
CREATE TABLE IF NOT EXISTS barang (
    id INT(11) NOT NULL AUTO_INCREMENT,
    kode_barang VARCHAR(20) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    stok INT(11) NOT NULL DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh data
INSERT INTO barang (kode_barang, nama_barang, kategori, harga, stok, deskripsi, gambar) VALUES
('BRG001', 'Kemeja Flannel Pria', 'Fashion Pria', 125000.00, 25, 'Kemeja flannel bahan katun premium', NULL),
('BRG002', 'Sepatu Sneakers Wanita', 'Fashion Wanita', 250000.00, 15, 'Sepatu sneakers ringan dan nyaman', NULL),
('BRG003', 'Tas Ransel Laptop', 'Aksesoris', 175000.00, 30, 'Tas ransel anti air muat laptop 15 inch', NULL);

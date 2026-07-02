<?php
/**
 * Proteksi Halaman: hanya user yang sudah login yang boleh mengakses
 * Panggil file ini di paling atas setiap halaman yang butuh login
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

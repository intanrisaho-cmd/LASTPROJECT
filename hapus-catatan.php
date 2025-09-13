<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM catatan_pelanggaran WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        // Redirect kembali ke halaman notifikasi admin setelah berhasil hapus
        header("Location: notifikasi-admin.php?hapus=berhasil");
        exit;
    } else {
        // Jika gagal
        header("Location: notifikasi-admin.php?hapus=gagal");
        exit;
    }
} else {
    // Jika tidak ada ID
    header("Location: notifikasi-admin.php");
    exit;
}
?>

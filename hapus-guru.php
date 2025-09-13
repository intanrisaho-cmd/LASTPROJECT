<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Cek parameter id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Query hapus data guru berdasarkan ID
    $query = mysqli_query($conn, "DELETE FROM guru_bk WHERE id = $id");

    if ($query) {
        // Redirect dengan notifikasi sukses
        header("Location: data-guru.php?success=deleted");
        exit;
    } else {
        echo "❌ Gagal menghapus data guru!";
    }
} else {
    header("Location: data-guru.php");
    exit;
}
?>

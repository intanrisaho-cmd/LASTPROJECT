<?php
session_start();
include 'config.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// Cek apakah parameter ID tersedia
if (!isset($_GET['id'])) {
  echo "ID siswa tidak ditemukan.";
  exit;
}

$id = intval($_GET['id']);

// Eksekusi penghapusan
$delete = $conn->prepare("DELETE FROM siswa WHERE id = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
  // Redirect dengan notifikasi sukses
  header("Location: data-siswa.php?success=deleted");
  exit;
} else {
  echo "Gagal menghapus data: " . $conn->error;
}
?>

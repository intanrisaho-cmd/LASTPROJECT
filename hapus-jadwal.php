<?php
session_start();
include 'config.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Akses ditolak!');
        window.location.href = 'login.php';
    </script>";
    exit;
}

// Validasi ID jadwal
if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID tidak ditemukan!');
        window.location.href = 'jadwal-konseling.php';
    </script>";
    exit;
}

$id = intval($_GET['id']);

// Query hapus
$query = "DELETE FROM jadwal_konseling WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['hapus_success'] = true;
} else {
    $_SESSION['hapus_error'] = true;
}

$stmt->close();
$conn->close();

header("Location: jadwal-konseling.php");
exit;
?>

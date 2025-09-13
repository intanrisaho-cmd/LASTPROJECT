<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// ✅ Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    echo json_encode([
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Anda harus login terlebih dahulu.'
    ]);
    exit;
}

$username = $_SESSION['username'];

if (isset($_POST['jadwal_id'])) {
    $jadwal_id = $_POST['jadwal_id'];

    // ✅ Cek apakah jadwal ada
    $stmt = $conn->prepare("SELECT * FROM jadwal_konseling WHERE id = ?");
    $stmt->bind_param("i", $jadwal_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'icon' => 'error',
            'title' => 'Gagal!',
            'text' => 'Jadwal tidak ditemukan.'
        ]);
        exit;
    }

    $jadwal = $result->fetch_assoc();

    // ✅ Cek apakah user sudah pernah mengajukan jadwal ini
    $cek = $conn->prepare("SELECT * FROM jadwal_konseling WHERE id = ? AND username = ?");
    $cek->bind_param("is", $jadwal_id, $username);
    $cek->execute();
    $resCek = $cek->get_result();

    if ($resCek->num_rows > 0) {
        echo json_encode([
            'icon' => 'warning',
            'title' => 'Duplikat!',
            'text' => 'Anda sudah mengajukan jadwal ini sebelumnya.'
        ]);
        exit;
    }

    // ✅ Update baris jadwal dengan username & status
    $update = $conn->prepare("UPDATE jadwal_konseling 
                              SET username = ?, status = 'Menunggu Konfirmasi', notif_user = 0
                              WHERE id = ?");
    $update->bind_param("si", $username, $jadwal_id);

    if ($update->execute()) {
        echo json_encode([
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Pengajuan konseling berhasil dikirim.'
        ]);
    } else {
        echo json_encode([
            'icon' => 'error',
            'title' => 'Gagal!',
            'text' => 'Terjadi kesalahan saat menyimpan.'
        ]);
    }
}
?>

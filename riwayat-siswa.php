<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$nama_siswa = $_SESSION['nama'] ?? '';

// Ambil semua riwayat dari catatan_pelanggaran atau riwayat_konseling
$query = $conn->prepare("SELECT * FROM catatan_pelanggaran WHERE nama_siswa = ?");
$query->bind_param("s", $nama_siswa);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>📋 Riwayat Konseling</h2>
  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Pelanggaran</th>
        <th>Waktu</th>
        <th>Oleh</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['pelanggaran']) ?></td>
          <td><?= htmlspecialchars($row['waktu']) ?></td>
          <td><?= htmlspecialchars($row['dikirim_oleh']) ?></td>
        </tr>
      <?php endwhile; ?>
      <?php if ($no == 1): ?>
        <tr><td colspan="4" class="text-center">Tidak ada data riwayat.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <a href="data-siswa.php" class="btn btn-secondary">Kembali ke Profil</a>
</div>
</body>
</html>

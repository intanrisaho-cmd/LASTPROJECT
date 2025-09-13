<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$query = $conn->query("SELECT * FROM konseling WHERE status = 'Selesai' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Konseling (Admin)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f5f7fa; }
    .container { margin-top: 50px; }
    .card { border-radius: 15px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card shadow">
      <div class="card-body">
        <h3 class="mb-4 text-success">
          <i class="bi bi-journal-check me-2"></i>Riwayat Konseling Siswa
        </h3>

        <?php if ($query && $query->num_rows > 0): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
              <thead class="table-success">
                <tr>
                  <th>Username</th>
                  <th>Nama</th>
                  <th>Kelas</th>
                  <th>Topik</th>
                  <th>Deskripsi</th>
                  <th>Tanggal</th>
                  <th>Guru BK</th>
                  <th>Catatan Guru</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $query->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><?= htmlspecialchars($row['nama']) ?></td>
                  <td><?= htmlspecialchars($row['kelas']) ?></td>
                  <td><?= htmlspecialchars($row['topik']) ?></td>
                  <td><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
                  <td><?= htmlspecialchars($row['tanggal']) ?></td>
                  <td><?= htmlspecialchars($row['guru']) ?></td>
                  <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i> Belum ada riwayat konseling yang selesai.
          </div>
        <?php endif; ?>

        <a href="dashboard_admin.php" class="btn btn-outline-secondary mt-4">
          <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

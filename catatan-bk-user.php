<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username_siswa = $_SESSION['username'];

// Ambil nama siswa
$query_siswa = "SELECT nama FROM siswa WHERE username = ?";
$stmt_siswa = $conn->prepare($query_siswa);
$stmt_siswa->bind_param("s", $username_siswa);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();
$data_siswa = $result_siswa->fetch_assoc();
$nama_siswa = $data_siswa['nama'] ?? $username_siswa;

// Ambil data catatan konseling
$sql = "SELECT c.tanggal, c.jam, c.catatan, g.nama AS nama_guru
        FROM catatan c
        LEFT JOIN guru_bk g ON c.guru = g.username
        WHERE c.username_siswa = ?
        ORDER BY c.tanggal DESC, c.jam DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username_siswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
body { background: #f0f4f8; padding-top: 90px; font-family: 'Segoe UI', sans-serif; }
.card { border: none; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.table thead { background-color: #e3f2fd; }
.table th { color: #0d6efd; text-align: center; }
.table td { vertical-align: middle; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
<div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center text-success" href="user_dashboard.php">
      <img src="images/sma2.png" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
      <strong>Konseling Siswa</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarUser">
      <ul class="navbar-nav mb-2 mb-lg-0 gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="user_dashboard.php"><i class="bi bi-house"></i> Beranda</a></li>
        <li class="nav-item"><a class="nav-link active text-primary" href="jadwal_konseling.php"><i class="bi bi-calendar-week-fill"></i> Jadwal</a></li>
        <li class="nav-item"><a class="nav-link" href="status_pengajuan.php"><i class="bi bi-hourglass-split"></i> Status</a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_konseling.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="catatan-bk-user.php"><i class="bi bi-journals"></i>Catatan Guru BK</a></li>
        <li class="nav-item"><a class="nav-link" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
</div>
</nav>

<div class="container">
<div class="mb-4">
    <h3 class="text-dark">Selamat datang, <span class="text-primary"><?= htmlspecialchars($nama_siswa) ?></span>!</h3>
    <p class="text-muted">Berikut adalah riwayat konseling Anda bersama guru BK.</p>
</div>

<div class="card">
<div class="card-body">
  <div class="table-responsive">
    <table id="catatanTable" class="table table-hover table-bordered align-middle">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Guru BK</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal']))) ?></td>
              <td><?= htmlspecialchars($row['jam']) ?></td>
              <td><?= htmlspecialchars($row['nama_guru'] ?? '-') ?></td>
              <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Logout?',
      text: 'Apakah Anda yakin ingin keluar?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Logout',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) window.location.href = 'logout.php';
    });
});

$(document).ready(function() {
    $('#catatanTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [5,10,25,50],
        responsive: true
    });
});
</script>
</body>
</html>

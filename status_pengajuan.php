<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

// ✅ Hanya role "user" yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ✅ Gabungkan data jadwal & laporan konseling yang dikonfirmasi
$queryGabung = "
    SELECT guru, tanggal, jam, status FROM jadwal_konseling
    WHERE username = ? AND status = 'Dikonfirmasi'
    UNION ALL
    SELECT guru, tanggal, jam, status FROM laporan_konseling
    WHERE username = ? AND status = 'Dikonfirmasi'
    ORDER BY tanggal DESC
";
$stmt = $conn->prepare($queryGabung);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap & DataTables -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
body {
    background: linear-gradient(135deg, #f9f9f9, #eefaf4);
    font-family: 'Segoe UI', sans-serif;
    padding-top: 70px;
}
.navbar {
    border-bottom: 2px solid #198754;
}
.card {
    border-radius: 18px;
    border: none;
}
.card-body {
    padding: 2rem;
}
h3 {
    font-weight: bold;
    color: #198754;
}
.table thead {
    background: #198754;
    color: white;
}
.table-hover tbody tr:hover {
    background-color: #f0fff4 !important;
    transition: 0.3s;
}
.badge {
    font-size: 0.9rem;
}
</style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center text-success" href="user_dashboard.php">
      <img src="images/smandu.jpg" width="40" height="40" class="me-2 rounded-circle shadow-sm">
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
        <li class="nav-item"><a class="nav-link" href="catatan-bk-user.php"><i class="bi bi-journals"></i> Catatan Guru BK</a></li>
        <li class="nav-item"><a class="nav-link" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Konten -->
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-body">
      <h3 class="mb-4 text-center">
        <i class="bi bi-info-circle-fill me-2"></i> Status Konseling yang Sudah Dikonfirmasi
      </h3>

      <div class="table-responsive">
        <table id="statusTable" class="table table-striped table-hover align-middle text-center">
          <thead>
            <tr>
              <th>Guru BK</th>
              <th>Tanggal</th>
              <th>Jam</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['guru']) ?></td>
                  <td><?= $row['tanggal'] ? date('d M Y', strtotime($row['tanggal'])) : '-' ?></td>
                  <td><?= $row['jam'] ? date('H:i', strtotime($row['jam'])) . ' WIB' : '-' ?></td>
                  <td><span class="badge bg-success"><?= htmlspecialchars($row['status']) ?></span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td class="text-muted">-</td>
                <td class="text-muted">-</td>
                <td class="text-muted">-</td>
                <td class="text-muted">Belum ada jadwal yang dikonfirmasi</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ✅ JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ✅ SweetAlert Logout
document.getElementById('logoutBtn').addEventListener('click', function(e) {
  e.preventDefault();
  Swal.fire({
    title: 'Yakin ingin logout?',
    text: 'Sesi Anda akan diakhiri.',
    icon: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Batal',
    confirmButtonText: 'Ya, logout',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) window.location.href = 'logout.php';
  });
});

// ✅ DataTables
$(document).ready(function() {
  $('#statusTable').DataTable({
    order: [[1, "desc"]],
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    responsive: true,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
    }
  });
});
</script>
</body>
</html>

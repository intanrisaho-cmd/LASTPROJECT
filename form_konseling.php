<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username     = $_SESSION['username'];
  $nama         = trim($_POST['nama']);
  $kelas        = trim($_POST['kelas']);
  $no_hp        = trim($_POST['no_hp']);
  $email        = trim($_POST['email']);
  $no_ortu      = trim($_POST['no_ortu']);
  $topik        = trim($_POST['topik']);
  $deskripsi    = trim($_POST['deskripsi']);

  if ($nama && $kelas && $no_hp && $email && $no_ortu && $topik && $deskripsi) {
    $stmt = $conn->prepare("INSERT INTO pengajuan_konseling 
      (username, nama, kelas, no_hp, email, no_ortu, topik, deskripsi, status) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')");
    $stmt->bind_param("ssssssss", $username, $nama, $kelas, $no_hp, $email, $no_ortu, $topik, $deskripsi);
    $success = $stmt->execute();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Formulir Konseling</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-image: url('images/baru.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      padding-top: 70px;
      min-height: 100vh;
    }
    .card {
      border-radius: 15px;
    }
  </style>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="user_dashboard.php">
      <img src="images/sma.jpg" alt="Logo" width="45" height="45" class="me-2 rounded-circle shadow-sm">
      Konseling Siswa
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser" aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarUser">
      <ul class="navbar-nav mb-2 mb-lg-0 gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="user_dashboard.php"><i class="bi bi-house"></i> Beranda</a></li>
        <li class="nav-item"><a class="nav-link active text-primary" href="#"><i class="bi bi-pencil-square"></i> Ajukan Konseling</a></li>
        <li class="nav-item"><a class="nav-link" href="jadwal_konseling.php"><i class="bi bi-calendar-week-fill"></i> Jadwal</a></li>
        <li class="nav-item"><a class="nav-link" href="status_pengajuan.php"><i class="bi bi-hourglass-split"></i> Status</a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_konseling.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
        <li class="nav-item"><a class="nav-link" href="profil_user.php"><i class="bi bi-person-circle"></i> Profil</a></li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="#" onclick="logoutConfirm(event)">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ ISI FORM -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-4 text-primary">
            <i class="bi bi-journal-text me-2"></i>Formulir Permintaan Konseling
          </h3>

          <form method="post">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Kelas</label>
                <input type="text" name="kelas" class="form-control" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">No HP Siswa</label>
                <input type="text" name="no_hp" class="form-control" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">No HP Orang Tua</label>
                <input type="text" name="no_ortu" class="form-control" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Topik Konseling</label>
              <input type="text" name="topik" class="form-control" placeholder="Contoh: Masalah belajar" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi Permasalahan</label>
              <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send-check-fill"></i> Ajukan Konseling
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert untuk kirim berhasil -->
<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Permintaan konseling berhasil dikirim.',
    confirmButtonColor: '#3085d6',
    confirmButtonText: 'OK'
  });
</script>
<?php endif; ?>

<!-- SweetAlert Logout -->
<script>
  function logoutConfirm(event) {
    event.preventDefault();
    Swal.fire({
      title: 'Yakin ingin logout?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#5c5f63ff',
      confirmButtonText: 'Ya, logout',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  }
</script>

</body>
</html>

<?php
session_start();
include 'config.php';

// ✅ Pastikan hanya role "user" yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];

// ✅ Ambil data user
$query = $conn->prepare("SELECT username, email, role, foto FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$userData = $result->fetch_assoc();

$fotoProfil = !empty($userData['foto']) ? 'uploads/' . $userData['foto'] : 'images/default-profile.png';

// ✅ Ambil notifikasi dari session (jika ada)
$notif = '';
if (isset($_SESSION['notif'])) {
  $notif = $_SESSION['notif'];
  unset($_SESSION['notif']); // hapus setelah ditampilkan
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard User</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(to right, #e8f0ff, #f9f9ff);
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }
    .dashboard-container {
      width: 100vw;
      min-height: 100vh;
      padding: 40px 60px;
      background: #ffffff;
    }
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .welcome-box {
      background: #dfefff;
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .welcome-box img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #198754;
    }
    .welcome-text h4 {
      font-size: 28px;
      font-weight: bold;
      color: #198754;
      margin-bottom: 6px;
    }
    .summary-cards .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-cards .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    /* Carousel Styling Baru */
.carousel-img {
  width: 800px;
  height: 700px; /* bisa sesuaikan */
  object-fit: contain; /* menampilkan seluruh gambar tanpa terpotong */
  background-color: #f0f0f0; /* background netral untuk area kosong */
  border-radius: 12px;
}
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- HEADER -->
    <div class="dashboard-header">
      <div class="d-flex align-items-center gap-2">
        <h2 class="mb-0 d-flex align-items-center">
          <img src="images/sma2.png" alt="Logo" width="50" height="60" class="me-2">
          <span>Dashboard Konseling</span>  
        </h2>
      </div>

      <div class="d-flex align-items-center gap-3">
        <!-- Klik foto profil buka offcanvas -->
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#profileCanvas" 
           class="d-flex align-items-center text-decoration-none">
          <img src="<?= $fotoProfil ?>" alt="Foto Profil" 
               style="width:40px; height:40px; object-fit:cover; border-radius:50%; border:2px solid #198754;">
          <span class="ms-2 fw-semibold text-success"><?= htmlspecialchars($username); ?></span>
        </a>
        <button id="logoutBtn" class="btn btn-danger">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </div>
    </div>

    <!-- WELCOME CARD -->
    <div class="welcome-box">
      <img src="<?= $fotoProfil ?>" alt="Foto Profil">
      <div class="welcome-text">
        <h4>Hai, <?= htmlspecialchars($username); ?> 👋</h4>
        <p>Selamat datang di platform konseling siswa. Gunakan menu di bawah untuk mengakses layanan yang tersedia.</p>
      </div>
    </div>

    <!-- MENU CARDS -->
    <div class="row summary-cards g-4 mb-4">
      <div class="col-md-4">
        <div class="card p-4">
          <h5><i class="bi bi-calendar-check"></i> Jadwal Konseling</h5>
          <p>Lihat jadwal yang sudah dikonfirmasi oleh guru BK.</p>
          <a href="jadwal_konseling.php" class="btn btn-outline-primary">Lihat Jadwal</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5><i class="bi bi-hourglass-split"></i> Status Permintaan</h5>
          <p>Periksa status permintaan konseling kamu.</p>
          <a href="status_pengajuan.php" class="btn btn-outline-primary">Lihat Status</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5><i class="bi bi-star"></i> Rating & Feedback</h5>
          <p>Beri ulasan untuk sesi konseling yang sudah selesai.</p>
          <a href="rating_konseling.php" class="btn btn-outline-primary">Beri Feedback</a>
        </div>
      </div>
    </div>

    <div class="row summary-cards g-4">
      <div class="col-md-6">
        <div class="card p-4">
          <h5><i class="bi bi-clock-history"></i> Riwayat Konseling</h5>
          <p>Lihat daftar riwayat dan detail konseling sebelumnya.</p>
          <a href="riwayat_konseling.php" class="btn btn-primary">Lihat Riwayat</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4">
          <h5><i class="bi bi-journal-text"></i> Catatan Guru BK</h5>
          <p>Lihat catatan dari guru BK.</p>
          <a href="catatan-bk-user.php" class="btn btn-primary">Lihat Catatan</a>
        </div>
      </div>
    </div>

<!-- GALERI SLIDER -->
<div id="galeriCarousel" class="carousel slide mt-5" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/a.jpg" class="d-block w-100 carousel-img" alt="Galeri 1">
    </div>
    <div class="carousel-item">
      <img src="images/b.jpg" class="d-block w-100 carousel-img" alt="Galeri 2">
    </div>
    <div class="carousel-item">
      <img src="images/h.jpg" class="d-block w-100 carousel-img" alt="Galeri 3">
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#galeriCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#galeriCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

  <div class="carousel-indicators mt-3">
    <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="2"></button>
  </div>
</div>


      <!-- Indicators -->
      <div class="carousel-indicators mt-3">
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="3"></button>
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="4"></button>
        <button type="button" data-bs-target="#galeriCarousel" data-bs-slide-to="5"></button>
      </div>
    </div>

  </div> <!-- penutup dashboard-container -->

  <!-- OFFCANVAS PROFIL -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="profileCanvas" aria-labelledby="profileCanvasLabel" style="width:350px;">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="profileCanvasLabel"><i class="bi bi-person-circle me-2"></i> Edit Profil</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
      <?php if ($userData): ?>
        <form id="formUpdate" action="update_profile.php" method="post" enctype="multipart/form-data">
          <div class="text-center mb-3">
            <img src="<?= $fotoProfil ?>" class="rounded-circle border border-success" width="90" height="90" style="object-fit:cover;">
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" 
                   value="<?= htmlspecialchars($userData['username']); ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($userData['email']); ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Kosongkan jika tidak ingin mengubah">
          </div>

          <div class="mb-3">
            <label class="form-label">Ganti Foto Profil</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Batal</button>
          </div>
        </form>
      <?php else: ?>
        <div class="alert alert-warning mt-3">Data pengguna tidak ditemukan.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Script SweetAlert Logout -->
  <script>
    document.getElementById('logoutBtn').addEventListener('click', function () {
      Swal.fire({
        title: 'Yakin ingin logout?',
        text: "Kamu akan keluar dari sesi ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, logout',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'logout.php';
        }
      })
    });

    <?php if (!empty($notif)): ?>
      Swal.fire({
        icon: '<?= $notif['type']; ?>',
        title: '<?= $notif['title']; ?>',
        text: '<?= $notif['message']; ?>',
        timer: 2500,
        showConfirmButton: false
      });
    <?php endif; ?>
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

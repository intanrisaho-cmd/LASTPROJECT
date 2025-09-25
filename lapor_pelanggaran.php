<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['nis']) || trim($_GET['nis']) === '') {
    echo "NIS tidak ditemukan di URL.";
    exit;
}

$nis = $_GET['nis'];
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis_pelanggaran'];
    $wali = $_SESSION['username'];

    $query = "INSERT INTO laporan_pelanggaran (nis, nama_siswa, jenis_pelanggaran, tanggal, wali_kelas)
              VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nis, $siswa['nama'], $jenis, $wali);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lapor Pelanggaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
        background-color: #dedbf8ff;
        font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
        background-color: #4a59f9ff;
    }
    .navbar-brand,
    .nav-link {
        color: #fff !important;
        font-size: 0.95rem;
    }
    .nav-link:hover {
        color: #ffc107 !important;
    }
    .form-wrapper {
        margin-top: 100px;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        max-width: 700px;
    }
    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4 no-print">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">📘 Dashboard Wali Kelas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWali">
            <span class="navbar-toggler-icon text-light"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarWali">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="wali_dashboard.php">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pilih_siswa_lapor.php">📑 Laporan Konseling</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">🔓 Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- FORM -->
<div class="container d-flex justify-content-center align-items-start" style="min-height: 100vh; padding-top: 100px;">
  <div class="form-wrapper w-100">
    <h3 class="mb-4 text-center text-primary">Laporan konseling</h3>
    <form method="post">
      <div class="mb-4">
        <label class="form-label"><strong>Nama Siswa:</strong></label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($siswa['nama']); ?>" disabled>
      </div>
      <div class="mb-4">
        <label for="jenis" class="form-label">Topik:</label>
        <textarea name="Topik" class="form-control" placeholder="Tuliskan pelanggaran secara lengkap..." required></textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="bi bi-send-fill me-1"></i> Kirim
        </button>
      </div>
    </form>
  </div>
</div>

<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Laporan pelanggaran berhasil dikirim.',
    confirmButtonText: 'OK'
  }).then(() => {
    window.location.href = 'wali_dashboard.php';
  });
</script>
<?php endif; ?>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

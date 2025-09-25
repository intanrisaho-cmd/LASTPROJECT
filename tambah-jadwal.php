<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// Ambil daftar siswa dari database
$siswaQuery = $conn->query("SELECT username, nama FROM users WHERE role = 'user'");
$siswaList = [];
while ($siswa = $siswaQuery->fetch_assoc()) {
  $siswaList[] = $siswa;
}

// Proses simpan jadwal
if (isset($_POST['simpan'])) {
  // $username  = $_POST['username'];
  $hari      = $_POST['hari'];
  $jamInput  = $_POST['jam']; // format dari input: HH:MM
  $guru      = $_POST['guru'];
  $ruangan   = $_POST['ruangan'];
  $tanggal   = $_POST['tanggal'];
  $topik     = $_POST['topik'];
  // $deskripsi = $_POST['deskripsi'];

  // Pastikan format jam valid dan lengkapi ke HH:MM:SS untuk tipe TIME
  if (preg_match('/^\d{2}:\d{2}$/', $jamInput)) {
    $jam = $jamInput . ":00";
  } else {
    $jam = "00:00:00"; // fallback (tidak valid)
  }

  $sql = "INSERT INTO jadwal_konseling 
            (username, hari, jam, guru, ruangan, tanggal, topik)
          VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param("sssssss", $username, $hari, $jam, $guru, $ruangan, $tanggal, $topik);

  if ($stmt->execute()) {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Jadwal berhasil ditambahkan!',
          showConfirmButton: false,
          timer: 2000
        }).then(() => { window.location = 'jadwal-konseling.php'; });
      });
    </script>";
  } else {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan: " . $stmt->error . "'
        });
      });
    </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Jadwal Konseling</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family:'Segoe UI'; margin:0; display:flex; background:#f4f6f8; }
    .sidebar {
      width:240px; background:#2c3e50; height:100vh; color:#fff;
      position:fixed; display:flex; flex-direction:column; padding:20px 0;
    }
    .sidebar h2 { text-align:center; margin-bottom:1rem; }
    .sidebar a {
      padding:12px 20px; color:#fff; text-decoration:none; display:flex; align-items:center;
    }
    .sidebar a:hover { background:#34495e; }
    .logout-btn { margin-top:auto; margin:20px; background:#e74c3c;
                  text-align:center; padding:10px; border-radius:5px; color:#fff; text-decoration:none; }
    .main { margin-left:240px; padding:30px; width:100%; }
    form {
      background:#fff; padding:20px; border-radius:5px; max-width:500px;
      box-shadow:0 2px 6px rgba(0,0,0,0.1);
    }
    label { display:block; margin-top:15px; font-weight:500; }
    select, input[type="date"], input[type="text"], input[type="time"] {
      width:100%; padding:8px; margin-top:5px;
      border:1px solid #ccc; border-radius:4px;
    }
    button {
      margin-top:20px; padding:10px 20px; border:none; background:#3498db;
      color:#fff; border-radius:4px; cursor:pointer;
    }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>

  <!-- Sidebar Admin -->
  <div class="sidebar">
    <h2>Admin Konseling</h2>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data-siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a>
    <a href="data-guru.php"><i class="fas fa-user-tie"></i> Data Guru BK</a>
     <!-- <a href="data-pelanggaran.php"><i class="fas fa-exclamation-triangle"></i> Data Pelanggaran</a> -->
    <a href="jadwal-konseling.php"><i class="fas fa-calendar-alt"></i> Jadwal Konseling</a>
    <a href="catatan-admin.php"><i class="fas fa-book"></i> Catatan Guru BK</a>
    <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
    <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Catatan Konseling</a>
    <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <h1>Tambah Jadwal Konseling</h1>
    <form method="post">

      <!-- <label>Nama Siswa</label>
      <select name="username" class="form-select" required>
        <option value="">-- Pilih Siswa --</option>
        <?php foreach ($siswaList as $siswa): ?>
          <option value="<?= htmlspecialchars($siswa['username']) ?>">
            <?= htmlspecialchars($siswa['nama']) ?> (<?= $siswa['username'] ?>)
          </option>
        <?php endforeach; ?>
      </select> -->

      <label>Hari</label>
      <select name="hari" class="form-select" required>
        <option value="">-- Pilih Hari --</option>
        <option>Senin</option>
        <option>Selasa</option>
        <option>Rabu</option>
        <option>Kamis</option>
        <option>Jumat</option>
      </select>

      <label>Jam Konseling</label>
      <input type="time" name="jam" class="form-control" required>

      <label>Tanggal Konseling</label>
      <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>

      <label>Nama Guru BK</label>
      <input type="text" name="guru" class="form-control" placeholder="Masukkan nama guru" required>

      <label>Ruangan</label>
      <input type="text" name="ruangan" class="form-control" placeholder="Masukkan ruangan" required>

      <label>Topik Konseling</label>
      <input type="text" name="topik" class="form-control" placeholder="Masukkan topik" required>

      <!-- <label>Deskripsi Permasalahan</label>
      <input type="text" name="deskripsi" class="form-control" placeholder="Masukkan deskripsi" required> -->

      <button type="submit" name="simpan" class="btn btn-primary">Simpan Jadwal</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

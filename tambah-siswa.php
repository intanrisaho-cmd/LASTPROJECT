<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}

$success = '';
$error = '';
$showSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $tempat_lahir = $_POST['tempat_lahir'];
  $tanggal_lahir = $_POST['tanggal_lahir'];
  $nis = $_POST['nis'];
  $nisn = $_POST['nisn'];
  $kelas = $_POST['kelas'];
  $wali_kelas = $_POST['wali_kelas'];
  $no_hp_ortu = $_POST['no_hp_ortu'];
  $kelamin = $_POST['kelamin'];

  if ($nama && $tempat_lahir && $tanggal_lahir && $nis && $nisn && $no_hp_ortu && $kelamin) {
    $stmt = $conn->prepare("INSERT INTO siswa (nama, tempat_lahir, tanggal_lahir, nis, nisn, kelas, wali_kelas,no_hp_ortu, kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("sssssssss", $nama, $tempat_lahir, $tanggal_lahir, $nis, $nisn, $kelas, $wali_kelas, $no_hp_ortu, $kelamin);


    if ($stmt->execute()) {
      $showSuccess = true;
    } else {
      $error = "Gagal menyimpan data siswa.";
    }
  } else {
    $error = "Semua field harus diisi.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      background-color: #f4f6f8;
    }
    .sidebar {
      width: 240px;
      background-color: #2c3e50;
      height: 100vh;
      color: white;
      position: fixed;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 22px;
      margin-bottom: 20px;
    }
    .sidebar a {
      padding: 12px 20px;
      color: white;
      display: flex;
      align-items: center;
      text-decoration: none;
    }
    .sidebar a i {
      margin-right: 10px;
    }
    .sidebar a:hover {
      background-color: #34495e;
    }
    .logout-btn {
      margin-top: auto;
      margin: 20px;
      background-color: #e74c3c;
      text-align: center;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      color: white;
    }
    .logout-btn:hover {
      background-color: #c0392b;
    }
    .main {
      margin-left: 240px;
      padding: 30px;
      flex-grow: 1;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h2>Admin Konseling</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="data-siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a>
  <a href="data-guru.php"><i class="fas fa-user-tie"></i> Data Guru BK</a>
   <!-- <a href="data-pelanggaran.php"><i class="fas fa-exclamation-triangle"></i> Data Pelanggaran</a> -->
  <a href="jadwal-konseling.php"><i class="fas fa-calendar-alt"></i> Jadwal Konseling</a>
   <a href="catatan-admin.php"><i class="fas fa-book"></i> Catatan Guru BK</a>
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
  <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Catatan Pelanggaran</a>
  <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="container">
    <div class="card shadow">
      <div class="card-header bg-success text-white">
        <h4 class="mb-0">Tambah Data Siswa</h4>
      </div>
      <div class="card-body">
        <form method="post" action="">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Siswa</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input type="text" name="nis" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="nisn" class="form-label">No. NISN</label>
            <input type="text" name="nisn" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" name="kelas" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="wali_kelas" class="form-label">Wali_Kelas</label>
            <input type="text" name="wali_kelas" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="no_hp_ortu" class="form-label">No HP Orang Tua</label>
            <input type="text" name="no_hp_ortu" id="no_hp_ortu" class="form-control" 
            value="<?= isset($siswa['no_hp_ortu']) ? htmlspecialchars($siswa['no_hp_ortu']) : '' ?>" required>
          </div>

          <div class="mb-3">
            <label for="kelamin" class="form-label">Jenis Kelamin</label>
            <select name="kelamin" class="form-select" required>
              <option value="">-- Pilih --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if ($showSuccess): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Data siswa berhasil disimpan.',
    timer: 2000,
    showConfirmButton: false
  }).then(function() {
    window.location.href = 'data-siswa.php';
  });
</script>
<?php elseif ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?= $error ?>'
  });
</script>
<?php endif; ?>

</body>
</html>

<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan!";
    exit;
}

$id = intval($_GET['id']);

// Ambil data siswa
$query = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
if (!$query) {
    die("Query prepare gagal: " . $conn->error);
}
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows !== 1) {
    echo "Data tidak ditemukan!";
    exit;
}

$siswa = $result->fetch_assoc();
$updated = false;

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama          = $_POST['nama'];
    $tempat_lahir  = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $nis           = $_POST['nis'];
    $nisn          = $_POST['nisn'];
    $kelas         = $_POST['kelas'];
    $wali_kelas    = $_POST['wali_kelas'];
    $no_hp_ortu    = $_POST['no_hp_ortu'];
    $kelamin       = $_POST['kelamin'];

    $update = $conn->prepare("UPDATE siswa 
        SET nama=?, tempat_lahir=?, tanggal_lahir=?, nis=?, nisn=?, kelas=?, wali_kelas=?, no_hp_ortu=?, kelamin=? 
        WHERE id=?");
    if (!$update) {
        die("Query update gagal: " . $conn->error);
    }

    $update->bind_param("sssssssssi", $nama, $tempat_lahir, $tanggal_lahir, $nis, $nisn, $kelas, $wali_kelas, $no_hp_ortu, $kelamin, $id);

    if ($update->execute()) {
        $updated = true;
    } else {
        $error = "Gagal memperbarui data: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f8; display: flex; }
    .sidebar { width: 240px; background-color: #2c3e50; padding: 20px 0; color: white; height: 100vh; position: fixed; display: flex; flex-direction: column; }
    .sidebar h2 { text-align: center; margin-bottom: 20px; font-size: 22px; font-weight: 500; }
    .sidebar a { display: flex; align-items: center; padding: 12px 20px; color: white; text-decoration: none; transition: 0.3s; }
    .sidebar a i { margin-right: 12px; }
    .sidebar a:hover { background-color: #34495e; }
    .logout-btn { margin-top: auto; margin: 20px; padding: 10px; background-color: #e74c3c; color: white; text-align: center; border-radius: 5px; text-decoration: none; }
    .logout-btn:hover { background-color: #c0392b; }
    .main-content { margin-left: 240px; padding: 30px; flex: 1; }
    .header { background-color: #2980b9; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
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
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
  <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Notifikasi</a>
  <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="header">
    <h3 class="mb-0">Edit Data Siswa</h3>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Nama Siswa</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Tempat Lahir</label>
      <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($siswa['tempat_lahir']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" class="form-control" value="<?= $siswa['tanggal_lahir'] ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">NIS</label>
      <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">NISN</label>
      <input type="text" name="nisn" class="form-control" value="<?= htmlspecialchars($siswa['nisn']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Kelas</label>
      <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Wali Kelas</label>
      <input type="text" name="wali_kelas" class="form-control" value="<?= htmlspecialchars($siswa['wali_kelas']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis Kelamin</label>
      <select name="kelamin" class="form-select" required>
        <option value="L" <?= $siswa['kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
        <option value="P" <?= $siswa['kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="no_hp_ortu" class="form-label">No HP Orang Tua</label>
      <input type="text" name="no_hp_ortu" id="no_hp_ortu" class="form-control" 
        value="<?= htmlspecialchars($siswa['no_hp_ortu'] ?? '') ?>" required>
    </div>

    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    <a href="data-siswa.php" class="btn btn-secondary">Batal</a>
  </form>
</div>

<!-- SweetAlert -->
<?php if ($updated): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Data berhasil diperbarui!',
  showConfirmButton: false,
  timer: 2000
}).then(() => {
  window.location.href = 'data-siswa.php';
});
</script>
<?php endif; ?>

</body>
</html>

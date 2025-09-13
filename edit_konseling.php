<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
include 'config.php';

if (!isset($_GET['id'])) {
  header("Location: jadwal-konseling.php");
  exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM jadwal_konseling WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (isset($_POST['update'])) {
  $hari = $_POST['hari'];
  $tanggal = $_POST['tanggal'];
  $waktu = $_POST['waktu'];
  $tempat = $_POST['tempat'];
  $pembimbing = $_POST['pembimbing'];

  $stmt = $conn->prepare("UPDATE jadwal_konseling SET hari=?, tanggal=?, waktu=?, tempat=?, pembimbing=? WHERE id=?");
  $stmt->bind_param("sssssi", $hari, $tanggal, $waktu, $tempat, $pembimbing, $id);

  if ($stmt->execute()) {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data berhasil diperbarui',
          showConfirmButton: false,
          timer: 2000
        }).then(function() {
          window.location = 'jadwal-konseling.php';
        });
      });
    </script>";
  } else {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Gagal memperbarui data'
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
  <title>Edit Jadwal Konseling</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Segoe UI'; margin:0; display:flex; background:#f4f6f8; }
    .sidebar { width:240px; background:#2c3e50; height:100vh; color:#fff; position:fixed; display:flex; flex-direction:column; padding:20px 0; }
    .sidebar h2 { text-align:center; font-size:22px; margin-bottom:20px; }
    .sidebar a { padding:12px 20px; color:white; text-decoration:none; display:flex; align-items:center; }
    .sidebar a i { margin-right:10px; }
    .sidebar a:hover { background:#34495e; }
    .logout-btn { margin-top:auto; background:#e74c3c; text-align:center; padding:10px; margin:20px; border-radius:5px; color:white; text-decoration:none; }
    .main { margin-left:240px; padding:30px; width:100%; }
    h1 { font-size:22px; margin-bottom:20px; color:#2c3e50; }
    form { background:white; padding:20px; border-radius:5px; width:400px; }
    label { display:block; margin-top:10px; }
    input, select {
      width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px;
    }
    button {
      margin-top:15px; padding:10px 15px; border:none; background:#3498db; color:white; border-radius:5px;
    }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>
<div class="sidebar">
  <h2>Admin Konseling</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="data-siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a>
  <a href="data-guru.php"><i class="fas fa-user-tie"></i> Data Guru BK</a>
  <a href="jadwal-konseling.php"><i class="fas fa-calendar-alt"></i> Jadwal Konseling</a>
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
  <h1>Edit Jadwal Konseling</h1>
  <form method="post">
    <label>Hari</label>
    <select name="hari" required>
      <option value="Senin" <?= $data['hari'] == 'Senin' ? 'selected' : '' ?>>Senin</option>
      <option value="Selasa" <?= $data['hari'] == 'Selasa' ? 'selected' : '' ?>>Selasa</option>
      <option value="Rabu" <?= $data['hari'] == 'Rabu' ? 'selected' : '' ?>>Rabu</option>
      <option value="Kamis" <?= $data['hari'] == 'Kamis' ? 'selected' : '' ?>>Kamis</option>
      <option value="Jumat" <?= $data['hari'] == 'Jumat' ? 'selected' : '' ?>>Jumat</option>
    </select>
    <label>Tanggal</label>
    <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" required>
    <label>Waktu</label>
    <input type="time" name="waktu" value="<?= $data['waktu']; ?>" required>
    <label>Tempat</label>
    <input type="text" name="tempat" value="<?= $data['tempat']; ?>" required>
    <label>Pembimbing</label>
    <input type="text" name="pembimbing" value="<?= $data['pembimbing']; ?>" required>
    <button type="submit" name="update">Simpan Perubahan</button>
  </form>
</div>
</body>
</html>

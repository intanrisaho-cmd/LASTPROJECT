<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$nis = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];
    $no_hp_ortu = $_POST['no_hp_ortu'];

    $stmt = $conn->prepare("UPDATE siswa SET nama=?, kelas=?, alamat=?, no_hp_ortu=? WHERE nis=?");
    $stmt->bind_param("sssss", $nama, $kelas, $alamat, $no_hp_ortu, $nis);
    $stmt->execute();

    header("Location: data-siswa.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>✏️ Edit Profil Saya</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Kelas</label>
      <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($data['kelas']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Alamat</label>
      <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat']) ?>" required>
    </div>
    <div class="mb-3">
      <label>No HP Orang Tua</label>
      <input type="text" name="no_hp_ortu" class="form-control" value="<?= htmlspecialchars($data['no_hp_ortu']) ?>" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    <a href="data-siswa.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>
</body>
</html>

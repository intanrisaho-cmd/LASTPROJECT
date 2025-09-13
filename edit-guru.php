<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
include 'config.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $stmt = $conn->prepare("SELECT * FROM guru_bk WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = $result->fetch_assoc();
} else {
  header("Location: data-guru.php");
  exit;
}

if (isset($_POST['update'])) {
  $nama = $_POST['nama'];
  $nip = $_POST['nip'];
  $email = $_POST['email'];
  $telepon = $_POST['telepon'];

  $stmt = $conn->prepare("UPDATE guru_bk SET nama=?, nip=?, email=?, telepon=? WHERE id=?");
  $stmt->bind_param("ssssi", $nama, $nip, $email, $telepon, $id);

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
          window.location = 'data-guru.php';
        });
      });
    </script>";
  } else {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Terjadi kesalahan saat memperbarui data'
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
  <title>Edit Guru BK</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      display: flex;
      background: #f0f2f5;
    }
    .sidebar {
      width: 240px;
      background-color: #2c3e50;
      height: 100vh;
      color: white;
      position: fixed;
      display: flex;
      flex-direction: column;
      padding: 20px 0;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 22px;
      margin-bottom: 20px;
    }
    .sidebar a {
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: background 0.3s;
    }
    .sidebar a i {
      margin-right: 10px;
    }
    .sidebar a:hover {
      background-color: #34495e;
    }
    .logout-btn {
      margin-top: auto;
      background-color: #e74c3c;
      text-align: center;
      padding: 10px;
      margin: 20px;
      border-radius: 5px;
      color: white;
      text-decoration: none;
    }
    .logout-btn:hover {
      background-color: #c0392b;
    }
    .main {
      margin-left: 240px;
      padding: 40px;
      width: 100%;
    }
    .card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      padding: 30px;
      max-width: 500px;
      margin: auto;
    }
    .card h1 {
      font-size: 24px;
      margin-bottom: 25px;
      text-align: center;
      color: #2c3e50;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #333;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
      font-size: 14px;
    }
    button {
      background: #3498db;
      color: white;
      padding: 12px 18px;
      border: none;
      border-radius: 6px;
      width: 100%;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #2980b9;
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
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
  <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Notifikasi</a>
  <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="card">
    <h1>Edit Guru BK</h1>
    <form method="post">
      <label>Nama</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

      <label>NIP</label>
      <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>

      <label>Telepon</label>
      <input type="text" name="telepon" value="<?= htmlspecialchars($data['telepon']) ?>" required>

      <button type="submit" name="update">Simpan Perubahan</button>
    </form>
  </div>
</div>

</body>
</html>

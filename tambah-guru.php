<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
include 'config.php';

$showSuccess = false;

if (isset($_POST['simpan'])) {
  $nama = $_POST['nama'];
  $nip = $_POST['nip'];
  $email = $_POST['email'];
  $telepon = $_POST['telepon'];
  
  if ($conn->query("INSERT INTO guru_bk (nama, nip, email, telepon) VALUES ('$nama', '$nip', '$email', '$telepon')")) {
    $showSuccess = true;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Guru BK</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f4f6f8;
      display: flex;
    }

    .sidebar {
      width: 240px;
      background: #2c3e50;
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
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a:hover {
      background: #34495e;
    }

    .logout-btn {
      margin-top: auto;
      background: #e74c3c;
      text-align: center;
      padding: 10px;
      margin: 20px;
      border-radius: 5px;
      color: white;
      text-decoration: none;
    }

    .main {
      margin-left: 240px;
      padding: 40px;
      width: 100%;
    }

    .card {
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 12px;
      border: none;
    }

    .card-header {
      background-color: #2980b9;
      color: white;
      font-size: 20px;
      padding: 20px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .btn-success {
      background-color: #27ae60;
      border: none;
    }

    .btn-success:hover {
      background-color: #219150;
    }

    label {
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .main {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<?php if ($showSuccess): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Data guru berhasil disimpan.',
      showConfirmButton: false,
      timer: 2000
    }).then(() => {
      window.location.href = 'data-guru.php';
    });
  });
</script>
<?php endif; ?>

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
>

<div class="main">
  <div class="card">
    <div class="card-header">
      <i class="fas fa-user-plus"></i> Tambah Data Guru BK
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="mb-3 col-md-6">
            <label>Nama Guru</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3 col-md-6">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" required>
          </div>
          <div class="mb-3 col-md-6">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3 col-md-6">
            <label>Telepon</label>
            <input type="text" name="telepon" class="form-control" required>
          </div>
        </div>
        <div class="text-end">
          <button type="submit" name="simpan" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan Data
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>

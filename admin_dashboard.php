<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
include 'config.php';

// Handle update (inline edit)
if (isset($_POST['edit_id'])) {
  $id = $_POST['edit_id'];
  $nama = $_POST['nama'];
  $nohp = $_POST['no_hp'];
  $kelas = $_POST['kelas'];
  $email = $_POST['email'];
  $ortu = $_POST['no_ortu'];
  $topik = $_POST['topik'];
  $deskripsi = $_POST['deskripsi'];

  $update = mysqli_query($conn, "UPDATE pengajuan_konseling SET 
    nama_siswa='$nama', 
    no_hp_siswa='$nohp', 
    kelas='$kelas',
    email='$email',
    no_hp_ortu='$ortu',
    topik='$topik',
    deskripsi='$deskripsi'
    WHERE id='$id'");

  if ($update) {
    echo "<script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Data berhasil diubah!',
        timer: 2000,
        showConfirmButton: false
      });
    </script>";
  }
}

// Handle hapus
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  mysqli_query($conn, "DELETE FROM pengajuan_konseling WHERE id='$id'");
  echo "<script>
    Swal.fire({
      icon: 'success',
      title: 'Dihapus',
      text: 'Data berhasil dihapus!',
      timer: 2000,
      showConfirmButton: false
    }).then(() => {
      window.location = 'admin_dashboard.php';
    });
  </script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin Konseling</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Segoe UI';
      margin: 0;
      display: flex;
      background: url('images/baru.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .sidebar {
      width: 240px;
      background: rgba(44, 62, 80, 0.95);
      height: 100vh;
      color: #fff;
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
      padding: 30px;
      width: 100%;
    }

    h1 {
      font-size: 22px;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .card-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .card {
      flex: 1;
      color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      min-width: 200px;
    }

    .card.blue { background-color: #3498db; }
    .card.red { background-color: #e74c3c; }
    .card.yellow { background-color: #f1c40f; color: #2c3e50; }
    .card.green { background-color: #2ecc71; }

    .table-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-top: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    input[type="text"], textarea {
      width: 100%;
      border: none;
      background: transparent;
    }

    .btn {
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 13px;
    }

    .btn-edit { background: #3498db; color: #fff; }
    .btn-save { background: #2ecc71; color: #fff; }
    .btn-delete { background: #e74c3c; color: #fff; }
  </style>
</head>
<body>

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

<div class="main">
  <h1>Dashboard Admin</h1>

  <div class="card-container">
    <?php
      $total_siswa = $conn->query("SELECT COUNT(*) FROM siswa")->fetch_row()[0];
      $total_jadwal = $conn->query("SELECT COUNT(*) FROM jadwal_konseling")->fetch_row()[0];
      $total_guru = $conn->query("SELECT COUNT(*) FROM guru_bk")->fetch_row()[0];
    ?>
    <div class="card blue"><h3>Total Siswa</h3><p><?= $total_siswa ?></p></div>
    <div class="card red"><h3>Jadwal Konseling</h3><p><?= $total_jadwal ?></p></div>
    <div class="card yellow"><h3>Guru BK</h3><p><?= $total_guru ?></p></div>
  </div>
</script>
</body>
</html>

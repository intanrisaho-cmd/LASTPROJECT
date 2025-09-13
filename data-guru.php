<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Guru BK</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; display: flex; }
    .sidebar {
      width: 240px; background: #2c3e50; height: 100vh; color: white; position: fixed;
      display: flex; flex-direction: column; padding: 20px 0;
    }
    .sidebar h2 { text-align: center; font-size: 22px; margin-bottom: 20px; }
    .sidebar a {
      padding: 12px 20px; color: white; text-decoration: none; display: flex; align-items: center;
    }
    .sidebar a i { margin-right: 10px; }
    .sidebar a:hover { background: #34495e; }
    .logout-btn {
      margin-top: auto; background: #e74c3c; text-align: center; padding: 10px;
      margin: 20px; border-radius: 5px; color: white; text-decoration: none;
    }
    .logout-btn:hover { background: #c0392b; }
    .main { margin-left: 240px; padding: 30px; width: 100%; }
    .header {
      background-color: #2980b9; color: white; padding: 15px;
      border-radius: 10px; margin-bottom: 20px;
      display: flex; justify-content: space-between; align-items: center;
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

<!-- Main -->
<div class="main">
  <div class="header">
    <h4 class="mb-0">Data Guru BK</h4>
    <a href="tambah-guru.php" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Guru</a>
  </div>

  <table id="guru" class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>NIP</th>
        <th>Email</th>
        <th>Telepon</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $q = $conn->query("SELECT * FROM guru_bk");
      while ($row = $q->fetch_assoc()) {
        echo "<tr>
          <td>{$no}</td>
          <td>{$row['nama']}</td>
          <td>{$row['nip']}</td>
          <td>{$row['email']}</td>
          <td>{$row['telepon']}</td>
          <td>
            <a href='edit-guru.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-pen'></i></a>
            <button class='btn btn-danger btn-sm' onclick='confirmDelete({$row['id']})'><i class='fas fa-trash'></i></button>
          </td>
        </tr>";
        $no++;
      }
      ?>
    </tbody>
  </table>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Button export -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function () {
    $('#guru').DataTable({
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'copy',
          className: 'btn btn-success btn-sm text-white me-1',
          text: '<i class="fas fa-copy"></i> Salin'
        },
        {
          extend: 'excel',
          className: 'btn btn-warning btn-sm text-dark me-1',
          text: '<i class="fas fa-file-excel"></i> Excel'
        },
        {
          extend: 'pdf',
          className: 'btn btn-danger btn-sm text-white me-1',
          text: '<i class="fas fa-file-pdf"></i> PDF'
        },
        {
          extend: 'print',
          className: 'btn btn-primary btn-sm text-white me-1',
          text: '<i class="fas fa-print"></i> Cetak'
        }
      ],
      responsive: true
    });
  });

  function confirmDelete(id) {
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: "Data guru akan dihapus permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'hapus-guru.php?id=' + id;
      }
    });
  }
</script>

</body>
</html>

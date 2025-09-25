<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa Aktif</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      display: flex;
    }
    .sidebar {
      width: 240px;
      background-color: #2c3e50;
      padding: 20px 0;
      color: white;
      height: 100vh;
      position: fixed;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
      font-weight: 500;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a i {
      margin-right: 12px;
    }
    .sidebar a:hover {
      background-color: #34495e;
    }
    .logout-btn {
      margin-top: auto;
      margin: 20px;
      padding: 10px;
      background-color: #e74c3c;
      color: white;
      text-align: center;
      border-radius: 5px;
      text-decoration: none;
    }
    .logout-btn:hover {
      background-color: #c0392b;
    }
    .main-content {
      margin-left: 240px;
      padding: 30px;
      flex: 1;
      width: 100%;
    }
    .header {
      background-color: #2980b9;
      color: white;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
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
  <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Catatan Konseling</a>
  <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="header d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">📋 Data Siswa Aktif</h4>
    <a href="tambah-siswa.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Siswa</a>
  </div>

  <div class="table-responsive">
    <table id="siswaTable" class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Tempat Lahir</th>
          <th>Tanggal Lahir</th>
          <th>NIS</th>
          <th>NISN</th>
          <th>Kelas</th>
          <th>Wali Kelas</th>
          <th>Jenis Kelamin</th>
          <th>No HP Orang Tua</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
        while ($data = mysqli_fetch_assoc($query)) :
        ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($data['nama']); ?></td>
          <td><?= htmlspecialchars($data['tempat_lahir']); ?></td>
          <td><?= htmlspecialchars($data['tanggal_lahir']); ?></td>
          <td><?= htmlspecialchars($data['nis']); ?></td>
          <td><?= htmlspecialchars($data['nisn']); ?></td>
          <td><?= htmlspecialchars($data['kelas']); ?></td>
          <td><?= htmlspecialchars($data['wali_kelas']); ?></td>
          <td><?= htmlspecialchars($data['kelamin']); ?></td>
          <td><?= htmlspecialchars($data['no_hp_ortu']); ?></td>
          <td class="text-center">
            <a href="edit-siswa.php?id=<?= $data['id']; ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
            <a href="hapus-siswa.php?id=<?= $data['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event, <?= $data['id']; ?>)"><i class="fa fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons for export -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  $(document).ready(function () {
    $('#siswaTable').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', className: 'btn btn-success text-white btn-sm me-1', text: '<i class="fa fa-copy"></i> Salin' },
        { extend: 'excel', className: 'btn btn-warning text-dark btn-sm me-1', text: '<i class="fa fa-file-excel"></i> Excel' },
        { extend: 'pdf', className: 'btn btn-danger text-white btn-sm me-1', text: '<i class="fa fa-file-pdf"></i> PDF' },
        { extend: 'print', className: 'btn btn-primary text-white btn-sm me-1', text: '<i class="fa fa-print"></i> Cetak' }
      ],
      responsive: true
    });
  });

  // Konfirmasi hapus SweetAlert
  function confirmHapus(event, id) {
    event.preventDefault();
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: "Data siswa akan dihapus permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'hapus-siswa.php?id=' + id;
      }
    });
    return false;
  }
</script>

<!-- Notifikasi jika berhasil hapus -->
<?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Berhasil!',
  text: 'Data siswa berhasil dihapus.',
  timer: 2000,
  showConfirmButton: false
});
</script>
<?php endif; ?>

</body>
</html>

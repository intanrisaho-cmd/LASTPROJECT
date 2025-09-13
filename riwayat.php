<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

$wali = $_SESSION['username'];
$query = $conn->prepare("SELECT * FROM catatan_pelanggaran WHERE dikirim_oleh = ? ORDER BY waktu DESC");
$query->bind_param("s", $wali);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pengiriman Notifikasi</title>
    <!-- Bootstrap & Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(to bottom, #0d6efd, #0a58ca);
            color: white;
            padding-top: 20px;
        }
        .sidebar h4 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: #ffd700;
        }
        .navbar-custom {
            margin-left: 250px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .profile-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-icon {
            font-size: 2rem;
            color: #0d6efd;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>


<!-- Sidebar -->
<div class="sidebar">
  <h4>📘 Wali Kelas</h4>
  <a href="wali_dashboard.php">📝 Catatan Pelanggaran</a>
  <a href="riwayat.php">📂 Riwayat</a>
</div>

<!-- Navbar -->
<div class="navbar-custom">
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" 
       id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
      <span class="fw-bold me-2"><?= htmlspecialchars($wali) ?></span>
      <i class="bi bi-person-circle profile-icon"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
      <li><h6 class="dropdown-header">👤 <?= htmlspecialchars($wali) ?></h6></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
    </ul>
  </div>
</div>


<!-- Konten -->
<div class="content">
    <h4 class="mb-4">📜 Riwayat Pengiriman Notifikasi</h4>
    <div class="card">
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table id="riwayatTable" class="table table-bordered table-striped">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Pelanggaran</th>
                                <th>Waktu</th>
                                <th>No HP Ortu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($row['pelanggaran']) ?></td>
                                <td><?= htmlspecialchars($row['waktu']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Belum ada catatan pengiriman notifikasi.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#riwayatTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm' },
            { extend: 'print', className: 'btn btn-info btn-sm' }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        }
    });
});
</script>

</body>
</html>

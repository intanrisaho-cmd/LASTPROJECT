<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'config.php';

// Notifikasi SweetAlert setelah penghapusan
if (isset($_SESSION['hapus_success'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil dihapus!',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>";
    unset($_SESSION['hapus_success']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Konseling</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            background: #f4f6f8;
        }
        .sidebar {
            width: 240px;
            background: #2c3e50;
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
        .header {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dt-buttons .btn {
            margin-right: 5px;
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
<div class="main">
    <div class="header">
        <h4 class="mb-0">Jadwal Konseling</h4>
        <a href="tambah-jadwal.php" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Jadwal</a>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table id="jadwal" class="table table-striped table-bordered" style="width:100%">
            <thead class="table-primary text-center">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Guru</th>
                    <th>Tanggal</th>
                    <th>Ruangan</th>
                    <th>Topik</th>
                    <!-- <th>Deskripsi</th> -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
             <?php
                    $no = 1;
                    $q = $conn->query("SELECT * FROM jadwal_konseling");
                    while ($row = $q->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='text-center'>{$no}</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
                        date_default_timezone_set('Asia/Jakarta'); // Pastikan timezone diatur ke WIB

                        // Cek apakah kolom 'jam' tidak kosong dan valid
                        if (!empty($row['jam']) && strtotime($row['jam']) !== false) {
                            echo "<td>" . htmlspecialchars(date('H:i', strtotime($row['jam']))) . " WIB</td>";
                        } else {
                            echo "<td>-</td>"; // Jika data kosong atau tidak valid
                        }
                        echo "<td>" . htmlspecialchars($row['guru']) . "</td>";
                        echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ruangan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['topik']) . "</td>";
                        // echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                        echo "<td class='text-center'>
                                <a href='edit-jadwal.php?id={$row['id']}' class='btn btn-warning btn-sm me-1'>
                                    <i class='fas fa-edit'></i>
                                </a>
                                <button class='btn btn-danger btn-sm' onclick='hapusData({$row['id']})'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </td>";
                        echo "</tr>";
                        $no++;
                    }
                    ?>

            </tbody>
        </table>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#jadwal').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-success btn-sm text-white', text: '<i class="fas fa-copy"></i> Salin' },
            { extend: 'excel', className: 'btn btn-warning btn-sm text-dark', text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm text-white', text: '<i class="fas fa-file-pdf"></i> PDF' },
            { extend: 'print', className: 'btn btn-primary btn-sm text-white', text: '<i class="fas fa-print"></i> Cetak' }
        ],
        responsive: true
    });
});

function hapusData(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data tidak bisa dikembalikan setelah dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus-jadwal.php?id=' + id;
        }
    });
}
</script>

</body>
</html>

<?php
session_start();
include 'config.php';

// Cek role login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepsek') {
    header("Location: login.php");
    exit;
}

$kepsek = $_SESSION['username'];

// Ambil data siswa
$siswaQuery = "SELECT * FROM siswa ORDER BY kelas, nama";
$siswaResult = $conn->query($siswaQuery);
if (!$siswaResult) {
    die("Query siswa gagal: " . $conn->error);
}

// Ambil data laporan pelanggaran
$laporanQuery = "SELECT * FROM catatan_pelanggaran ORDER BY waktu DESC";
$laporanResult = $conn->query($laporanQuery);
if (!$laporanResult) {
    die("Query laporan gagal: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kepala Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background: #eef3f9;
            font-family: 'Segoe UI', sans-serif;
        }
        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(to bottom, #2b5876, #4e4376);
            padding-top: 20px;
            color: white;
        }
        .sidebar .brand {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar .nav-link {
            color: white;
            font-weight: 500;
            padding: 10px 20px;
            display: block;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #ffd700 !important;
        }
        /* Navbar atas */
        .navbar-custom {
            margin-left: 250px;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .profile-box {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #333;
        }
        .profile-box i {
            font-size: 28px;
            color: #4e4376;
        }
        /* Konten utama */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .table-wrapper {
            background: #ffffff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
            margin-bottom: 40px;
        }
        h2, h4 {
            color: #333;
            font-weight: 600;
        }
        th {
            background-color: #e9ecef !important;
            color: #000 !important;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">📘 Kepsek Dashboard</div>
    <a class="nav-link" href="kepsek_dashboard.php">🏠 Dashboard</a>
    <a class="nav-link text-warning" href="logout.php">🔓 Logout</a>
</div>

<!-- Navbar Atas -->
<div class="navbar-custom">
    <div class="profile-box">
        <span><?= htmlspecialchars($kepsek) ?></span>
        <i class="bi bi-person-circle"></i>
    </div>
</div>

<!-- Konten utama -->
<div class="main-content">
    <h2 class="mb-4">Selamat Datang, 
        <span class="text-primary"><?= htmlspecialchars($kepsek); ?></span>
    </h2>

    <!-- Data Semua Siswa -->
    <div class="table-wrapper">
        <h4>Data Semua Siswa 📋</h4>
        <div class="table-responsive">
            <table id="tabelSiswa" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($siswa = $siswaResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($siswa['nama']); ?></td>
                        <td><?= htmlspecialchars($siswa['nis']); ?></td>
                        <td><?= htmlspecialchars($siswa['kelas']); ?></td>
                        <td><?= htmlspecialchars($siswa['wali_kelas']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Laporan Pelanggaran -->
    <div class="table-wrapper">
        <h4> Catatan Konseling 🚨</h4>
        <div class="table-responsive">
            <table id="tabelLaporan" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No HP</th>
                        <th>Nama Siswa</th>
                        <th>Catatan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($lapor = $laporanResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($lapor['no_hp']); ?></td>
                        <td><?= htmlspecialchars($lapor['nama_siswa']); ?></td>
                        <td><?= htmlspecialchars($lapor['catatan']); ?></td>
                        <td><?= htmlspecialchars($lapor['waktu']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS Bootstrap & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 20, 50],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "→",
                previous: "←"
            }
        }
    });

    $('#tabelLaporan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 20, 50],
        order: [[4, "desc"]], // urut berdasarkan tanggal
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "→",
                previous: "←"
            }
        }
    });
});
</script>
</body>
</html>

<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

// Ambil data siswa
$siswa = $conn->query("SELECT * FROM siswa ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Siswa - Laporan Konseling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ccdbfcff, #dde4f5ff);
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #6a53c7ff;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: #ffdd57 !important;
        }
        .container {
            max-width: 900px;
            margin-top: 40px;
        }
        .card {
            border-radius: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            background-color: #fff;
        }
        .card-title {
            font-weight: bold;
            color: #5a4c94;
            text-align: center;
            margin-bottom: 25px;
        }
        table th {
            background-color: #d0e0ff;
            color: #333;
            text-align: center;
        }
        table td {
            vertical-align: middle;
        }
        table tbody tr:hover {
            background-color: #f4f9ff;
        }
        .btn-warning {
            background-color: #f7b731;
            border: none;
            transition: 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #e09e12;
        }
        .btn-info {
            background-color: #6dd5ed;
            border: none;
            transition: 0.3s ease;
        }
        .btn-info:hover {
            background-color: #3cbec9;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<style>
  .navbar-gradient {
    background: linear-gradient(to right, #6a11cb, #2575fc); /* ungu ke biru */
  }
</style>

<nav class="navbar navbar-expand-lg mb-4 no-print">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">📘 Dashboard Wali Kelas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWali">
            <span class="navbar-toggler-icon text-light"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarWali">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="wali_dashboard.php">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pilih_siswa_lapor.php">📑 Laporan Konseling</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">🔓 Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<!-- CONTENT -->
<div class="container">
    <div class="card mt-4">
        <h3 class="card-title">Pilih Siswa untuk Laporan Konseling</h3>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $siswa->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($row['nis']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td class="text-center">
                                <a href="lapor_pelanggaran.php?nis=<?= urlencode($row['nis']); ?>" class="btn btn-warning btn-sm mb-1">Lapor</a>
                                <a href="kirim_notifikasi.php?nis=<?= urlencode($row['nis']); ?>" class="btn btn-info btn-sm">Notifikasi</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

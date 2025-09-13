<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data catatan dari tabel catatan dan siswa
$query = "SELECT c.id, s.nama AS nama_siswa, c.catatan, c.tanggal, c.guru 
          FROM catatan c 
          JOIN siswa s ON c.username = s.username 
          ORDER BY c.tanggal DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan BK - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="d-flex">
    <!-- SIDEBAR -->
    <div class="bg-dark text-white p-3 vh-100" style="width: 250px;">
        <h4 class="text-center mb-4">Admin Konseling</h4>
        <a href="admin_dashboard.php" class="text-white d-block mb-2"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="data-siswa.php" class="text-white d-block mb-2"><i class="fas fa-user-graduate me-2"></i> Data Siswa</a>
        <a href="data-guru.php" class="text-white d-block mb-2"><i class="fas fa-chalkboard-teacher me-2"></i> Data Guru BK</a>
         <!-- <a href="data-pelanggaran.php"><i class="fas fa-exclamation-triangle"></i> Data Pelanggaran</a> -->
        <a href="jadwal-konseling.php" class="text-white d-block mb-2"><i class="fas fa-calendar-alt me-2"></i> Jadwal Konseling</a>
        <a href="catatan-bk-admin.php" class="text-white d-block mb-2"><i class="fas fa-book me-2"></i> Catatan Konseling</a>
        <a href="logout.php" class="text-white d-block mt-5"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container mt-4">
        <h3>Catatan Konseling</h3>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Guru BK</th>
                    <th>Tanggal</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_siswa']}</td>
                        <td>{$row['guru']}</td>
                        <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                        <td>{$row['catatan']}</td>
                        <td>
                            <a href='hapus-catatan.php?id={$row['id']}' onclick=\"return confirm('Yakin ingin menghapus catatan ini?')\" class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Hapus</a>
                        </td>
                    </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

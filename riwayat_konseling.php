<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
}
.navbar { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

/* jarak atas dan bawah konten */
.page-container {
    padding-top: 100px;
    padding-bottom: 40px;
}

/* Navbar brand hijau */
.navbar-brand strong {
    color: #198754 !important; /* hijau bootstrap */
}

/* Card header hijau */
.card-header {
    background: #ffffff;
    color: #198754; /* hijau */
    font-weight: 700;
    text-align: center;
    border-bottom: 2px solid #198754;
}
.card {
    border-radius: 14px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Tabel putih dengan teks hitam */
.table thead th {
    background: #ffffff;
    color: #000000; /* header hitam */
    text-align: center;
    border-bottom: 2px solid #198754; /* garis hijau */
}
.table td, .table th {
    background: #ffffff;
    color: #000000; /* isi tabel hitam */
    text-align: center;
    vertical-align: middle;
}
.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="user_dashboard.php">
            <img src="images/smandu.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
            <strong>Konseling Siswa</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarUser">
            <ul class="navbar-nav mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php"><i class="bi bi-house"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="jadwal_konseling.php"><i class="bi bi-calendar-week-fill"></i> Jadwal</a></li>
                <li class="nav-item"><a class="nav-link" href="status_pengajuan.php"><i class="bi bi-hourglass-split"></i> Status</a></li>
                <li class="nav-item"><a class="nav-link active font-weight-bold text-success" href="riwayat_konseling.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link" href="catatan-bk-user.php"><i class="bi bi-journals"></i> Catatan Guru BK</a></li>
                <li class="nav-item"><a class="nav-link" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Card Riwayat di Tengah -->
<div class="container page-container d-flex justify-content-center">
    <div class="card w-100" style="max-width: 1000px;">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Konseling</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Topik</th>
                            <th>Deskripsi Permasalahan</th>
                            <th>Status</th>
                            <th>Guru BK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->prepare("
                            SELECT tanggal, topik, deskripsi, status, guru 
                            FROM jadwal_konseling 
                            WHERE username = ? 
                              AND status IN ('Dikonfirmasi', 'Disetujui', 'Ditolak') 
                            ORDER BY tanggal DESC
                        ");
                        $query->bind_param("s", $username);
                        $query->execute();
                        $result = $query->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $badgeClass = "badge-warning";
                                if ($row['status'] == 'Disetujui') $badgeClass = "badge-success";
                                elseif ($row['status'] == 'Ditolak') $badgeClass = "badge-danger";
                                elseif ($row['status'] == 'Dikonfirmasi') $badgeClass = "badge-info";

                                echo "<tr>
                                        <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
                                        <td>".htmlspecialchars($row['topik'])."</td>
                                        <td>".htmlspecialchars($row['deskripsi'])."</td>
                                        <td><span class='badge $badgeClass'>".htmlspecialchars($row['status'])."</span></td>
                                        <td>".htmlspecialchars($row['guru'])."</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Belum ada riwayat konseling.</td></tr>";
                        }

                        $query->close();
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Logout SweetAlert
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Sesi Anda akan diakhiri.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d21c0f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, logout',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) window.location.href = 'logout.php';
    });
});
</script>
</body>
</html>

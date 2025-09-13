<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

// Cek akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Notifikasi konfirmasi
$notifQuery = $conn->query("SELECT * FROM jadwal_konseling 
    WHERE username = '$username' AND status = 'Dikonfirmasi' AND notif_user = 1 LIMIT 1");
$showNotif = false;
if ($notifQuery && $notifQuery->num_rows > 0) {
    $showNotif = true;
    $conn->query("UPDATE jadwal_konseling 
        SET notif_user = 0 
        WHERE username = '$username' AND status = 'Dikonfirmasi'");
}

// Ambil jadwal umum
$jadwalUmum = $conn->query("SELECT * FROM jadwal_konseling ORDER BY hari, jam");

// Ambil pengajuan user
$pengajuanSiswa = [];
$resPengajuan = $conn->query("SELECT DISTINCT guru, jam, status FROM jadwal_konseling WHERE username = '$username'");
if ($resPengajuan && $resPengajuan->num_rows > 0) {
    while ($row = $resPengajuan->fetch_assoc()) {
        $pengajuanSiswa[$row['guru']][$row['jam']] = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Jadwal Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- SweetAlert & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<style>
body {
    padding-top: 90px;
    background-color: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}
.navbar {
    background-color: #fff;
    border-bottom: 2px solid #28a745;
}
.card-full {
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    padding: 30px;
}
.table thead {
    background: #28a745;
    color: #fff;
}
.table-hover tbody tr:hover {
    background-color: #e9f7ef;
    transition: 0.3s;
}
.badge-status {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 10px;
}
@media print {
    .navbar, .form-control, .btn, form { display: none !important; }
}
</style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
<div class="container-fluid px-4">
    <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="user_dashboard.php">
        <img src="images/smandu.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
        Konseling Siswa
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarUser">
        <ul class="navbar-nav gap-2">
            <li class="nav-item"><a class="nav-link" href="user_dashboard.php"><i class="bi bi-house"></i> Beranda</a></li>
            <li class="nav-item"><a class="nav-link active text-primary" href="jadwal_konseling.php"><i class="bi bi-calendar-week-fill"></i> Jadwal</a></li>
            <li class="nav-item"><a class="nav-link" href="status_pengajuan.php"><i class="bi bi-hourglass-split"></i> Status</a></li>
            <li class="nav-item"><a class="nav-link" href="riwayat_konseling.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
            <li class="nav-item"><a class="nav-link" href="catatan-bk-user.php"><i class="bi bi-journals"></i> Catatan Guru BK</a></li>
            <li class="nav-item"><a class="nav-link" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="#" onclick="logoutConfirm()"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
        </ul>
    </div>
</div>
</nav>

<!-- ✅ Konten -->
<div class="container-fluid px-4">
<div class="card card-full mx-auto">
    <h3 class="mb-4 text-center text-success">
        <i class="bi bi-calendar3 me-2"></i> Jadwal Konseling Umum
    </h3>

    <?php if ($showNotif): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Dikonfirmasi!',
        text: 'Pengajuan konseling kamu telah dikonfirmasi.',
        confirmButtonColor: '#28a745'
    });
    </script>
    <?php endif; ?>

    <div class="table-responsive">
        <table id="jadwalTable" class="table table-bordered table-hover align-middle text-center">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Guru BK</th>
                    <th>Ruangan</th>
                    <th>Topik</th>
                    <th>Deskripsi</th>
                    <th>Status / Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($jadwalUmum && $jadwalUmum->num_rows > 0): ?>
                <?php while ($row = $jadwalUmum->fetch_assoc()): ?>
                    <?php
                    $guru = $row['guru'];
                    $jam = $row['jam'];
                    $status = $pengajuanSiswa[$guru][$jam] ?? '';
                    ?>
                    <tr class="<?= $status === 'Dikonfirmasi' ? 'table-success fw-bold' : '' ?>">
                        <td><?= htmlspecialchars($row['hari']) ?></td>
                        <td><?= htmlspecialchars(date('H:i', strtotime($row['jam']))) ?> WIB</td>
                        <td><?= htmlspecialchars($row['guru']) ?></td>
                        <td><?= htmlspecialchars($row['ruangan']) ?></td>
                        <td><?= htmlspecialchars($row['topik']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td>
                        <?php if ($status === 'Dikonfirmasi'): ?>
                            <span class="badge bg-success badge-status"><i class="bi bi-check-circle-fill me-1"></i> Dikonfirmasi</span>
                        <?php elseif ($status === 'Menunggu Konfirmasi'): ?>
                            <span class="badge bg-warning text-dark badge-status"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                        <?php else: ?>
                            <form method="post" action="jadwal_konseling_process.php" class="d-inline ajukan-form">
                                <input type="hidden" name="jadwal_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-send-check"></i> Ajukan
                                </button>
                            </form>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-muted">Belum ada jadwal tersedia.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
// ✅ Logout dengan SweetAlert
function logoutConfirm() {
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: "Sesi Anda akan diakhiri.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, logout!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = 'logout.php';
    });
}

// ✅ AJAX Ajukan Jadwal
$(document).ready(function(){
    $('.ajukan-form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var jadwal_id = form.find('input[name="jadwal_id"]').val();

        $.post('jadwal_konseling_process.php', {jadwal_id: jadwal_id}, function(response){
            Swal.fire({
                icon: response.icon,
                title: response.title,
                text: response.text,
                confirmButtonColor: '#3085d6'
            }).then(() => { location.reload(); });
        }, 'json');
    });

    // ✅ DataTables
    $('#jadwalTable').DataTable({
        "order": [[0, "asc"], [1, "asc"]],
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "paginate": { "next": "➡", "previous": "⬅" }
        }
    });
});
</script>

<!-- Bootstrap & DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>

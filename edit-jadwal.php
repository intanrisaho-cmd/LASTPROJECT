<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
include 'config.php';

$data = ['hari' => '', 'jam' => '', 'guru' => '', 'ruangan' => '', 'topik' => '', 'deskripsi' => ''];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM jadwal_konseling WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = $_POST['hari'];
    $jamInput = $_POST['jam'];
    $jam = preg_match('/^\d{2}:\d{2}$/', $jamInput) ? $jamInput . ":00" : "00:00:00";
    $guru = $_POST['guru'];
    $ruangan = $_POST['ruangan'];
    $topik = $_POST['topik'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("UPDATE jadwal_konseling SET hari=?, jam=?, guru=?, ruangan=?, topik=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("ssssssi", $hari, $jam, $guru, $ruangan, $topik, $deskripsi, $id);

    if ($stmt->execute()) {
        $success = "Jadwal berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui data!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Jadwal Konseling</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #f8f9fa;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            padding: 12px 15px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .sidebar a.logout-btn {
            background-color: #e74c3c;
            color: white;
        }

        .sidebar a.logout-btn:hover {
            background-color: #c0392b;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            border-radius: 30px;
        }
    </style>
</head>
<body>

<!-- Sidebar Admin -->
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

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Jadwal Konseling</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="hari">Hari</label>
                        <input type="text" name="hari" id="hari" class="form-control" value="<?= htmlspecialchars($data['hari']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="jam">Jam</label>
                        <input type="time" name="jam" id="jam" class="form-control" value="<?= htmlspecialchars(substr($data['jam'], 0, 5)) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="guru">Guru BK</label>
                        <input type="text" name="guru" id="guru" class="form-control" value="<?= htmlspecialchars($data['guru']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ruangan">Ruangan</label>
                        <input type="text" name="ruangan" id="ruangan" class="form-control" value="<?= htmlspecialchars($data['ruangan']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="topik">Topik</label>
                        <input type="text" name="topik" id="topik" class="form-control" value="<?= htmlspecialchars($data['topik']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($success)) : ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= $success ?>',
    timer: 2000,
    showConfirmButton: false
}).then(() => {
    window.location.href = 'jadwal-konseling.php';
});
</script>
<?php elseif (!empty($error)) : ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?= $error ?>',
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

</body>
</html>

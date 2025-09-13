<?php
session_start();
include 'config.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil ID feedback dari URL
if (!isset($_GET['id'])) {
    header("Location: feedback.php");
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM feedback WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_siswa = htmlspecialchars($_POST['username_siswa']);
    $guru = htmlspecialchars($_POST['guru']);
    $tanggal = $_POST['tanggal'];
    $isi_feedback = htmlspecialchars($_POST['isi_feedback']);

    $update = mysqli_query($conn, "UPDATE feedback SET 
        username_siswa = '$username_siswa', 
        guru = '$guru',
        tanggal = '$tanggal',
        isi_feedback = '$isi_feedback'
        WHERE id = $id");

    if ($update) {
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Feedback berhasil diperbarui',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'feedback.php';
        });
        </script>";
    } else {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Feedback gagal diperbarui',
            timer: 2000,
            showConfirmButton: false
        });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Feedback</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3>Edit Feedback Siswa</h3>
    <form method="POST" class="card p-4 shadow-sm bg-white">
        <div class="mb-3">
            <label class="form-label">Username Siswa</label>
            <input type="text" name="username_siswa" class="form-control" value="<?= htmlspecialchars($data['username_siswa']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Guru BK</label>
            <input type="text" name="guru" class="form-control" value="<?= htmlspecialchars($data['guru']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Isi Feedback</label>
            <textarea name="isi_feedback" class="form-control" rows="5" required><?= htmlspecialchars($data['isi_feedback']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="feedback.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>

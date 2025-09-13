<?php
session_start();
include 'config.php';

// Cek apakah role wali
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

// Ambil data siswa
$dataSiswa = $conn->query("SELECT * FROM siswa");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="#">Sistem Konseling</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="wali_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="form_pelanggaran.php">Input Pelanggaran</a></li>
            <li class="nav-item"><a class="nav-link" href="riwayat_pelanggaran.php">Riwayat</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Form -->
<div class="container mt-5">
    <h4>Laporkan Pelanggaran Siswa</h4>
    <form method="post" action="">
        <div class="mb-3">
            <label for="nis" class="form-label">Pilih Siswa</label>
            <select class="form-select" name="nis" id="nis" required>
                <option value="">-- Pilih Siswa --</option>
                <?php while ($s = $dataSiswa->fetch_assoc()): ?>
                    <option value="<?= $s['nis'] ?>"><?= $s['nama'] ?> - <?= $s['kelas'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="kirim" class="btn btn-primary">Kirim Notifikasi</button>
    </form>
</div>

<?php
// Proses kirim notifikasi otomatis
if (isset($_POST['kirim'])) {
    $nis = $_POST['nis'];
    $ambil = $conn->query("SELECT * FROM siswa WHERE nis = '$nis'");
    $siswa = $ambil->fetch_assoc();

    $no_hp = $siswa['no_wa_ortu'];
    $nama  = $siswa['nama'];
    $kelas = $siswa['kelas'];

    $pesan = "Assalamualaikum, kami informasikan bahwa ananda *$nama* dari kelas *$kelas* telah melakukan pelanggaran. Dimohon kerja samanya untuk menindaklanjuti.";
    $pesan_encoded = urlencode($pesan);
    $link = "https://wa.me/$no_hp?text=$pesan_encoded";

    // Simpan riwayat
    $log = date("Y-m-d H:i:s") . " - $nama ($kelas) ke $no_hp\n";
    file_put_contents("riwayat_pengiriman.txt", $log, FILE_APPEND);

    // Redirect ke link WA
    echo "<script>window.open('$link', '_blank');</script>";
}
?>

</body>
</html>

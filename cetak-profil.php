    <?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    die("Akses ditolak.");
}

$nis = $_GET['nis'] ?? '';
if ($nis !== $_SESSION['username']) {
    die("Anda tidak diizinkan mengakses data ini.");
}

$stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cetak Profil</title>
  <style>
    body { font-family: Arial; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 10px; }
    h3 { text-align: center; }
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body>
  <h3>Profil Siswa</h3>
  <table>
    <tr><th>NIS</th><td><?= $siswa['nis'] ?></td></tr>
    <tr><th>Nama</th><td><?= $siswa['nama'] ?></td></tr>
    <tr><th>Kelas</th><td><?= $siswa['kelas'] ?></td></tr>
    <tr><th>Jenis Kelamin</th><td><?= $siswa['jk'] ?></td></tr>
    <tr><th>No HP Ortu</th><td><?= $siswa['no_ortu'] ?></td></tr>
  </table>
  <br>
  <div class="no-print">
    <button onclick="window.print()">Cetak</button>
  </div>
</body>
</html>

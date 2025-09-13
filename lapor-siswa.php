<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] !== 'wali_kelas') {
  header("Location: dashboard.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_wali = $_SESSION['user_id'];
  $id_siswa = $_POST['id_siswa'];
  $id_guru_bk = $_POST['id_guru_bk'];
  $isi_laporan = mysqli_real_escape_string($conn, $_POST['isi_laporan']);
  $tanggal = date('Y-m-d');

  // Simpan laporan
  $query = "INSERT INTO laporan_wali (id_wali_kelas, id_siswa, id_guru_bk, tanggal_lapor, isi_laporan)
            VALUES ('$id_wali', '$id_siswa', '$id_guru_bk', '$tanggal', '$isi_laporan')";
  mysqli_query($conn, $query);

  // Kirim notifikasi ke orang tua
  $pesan = "Laporan dibuat oleh wali kelas pada tanggal $tanggal untuk siswa ID $id_siswa.";
  mysqli_query($conn, "INSERT INTO notifikasi_orang_tua (id_siswa, pesan) VALUES ('$id_siswa', '$pesan')");

  echo "<script>alert('Laporan berhasil dikirim'); window.location='lapor-siswa.php';</script>";
}
?>

<h2>Form Laporan Siswa ke Guru BK</h2>
<form method="POST">
  <label>Nama Siswa:</label><br>
  <select name="id_siswa" required>
    <?php
    $kelas_id = $_SESSION['kelas_id'];
    $result = mysqli_query($conn, "SELECT * FROM siswa WHERE kelas_id = '$kelas_id'");
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<option value='{$row['id']}'>{$row['nama']}</option>";
    }
    ?>
  </select><br><br>

  <label>Guru BK:</label><br>
  <select name="id_guru_bk" required>
    <?php
    $guru_bk = mysqli_query($conn, "SELECT * FROM users WHERE role = 'guru_bk'");
    while ($row = mysqli_fetch_assoc($guru_bk)) {
      echo "<option value='{$row['id']}'>{$row['nama']}</option>";
    }
    ?>
  </select><br><br>

  <label>Isi Laporan:</label><br>
  <textarea name="isi_laporan" rows="4" cols="50" required></textarea><br><br>

  <button type="submit">Kirim Laporan</button>
</form>

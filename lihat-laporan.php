<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] !== 'kepala_sekolah') {
  header("Location: dashboard.php");
  exit;
}

$query = mysqli_query($conn, "
  SELECT l.*, s.nama AS nama_siswa, u.nama AS guru_bk 
  FROM laporan_wali l
  JOIN siswa s ON l.id_siswa = s.id
  JOIN users u ON l.id_guru_bk = u.id
  ORDER BY l.tanggal_lapor DESC
");
?>

<h2>Daftar Laporan dari Wali Kelas</h2>
<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>Nama Siswa</th>
    <th>Guru BK</th>
    <th>Tanggal Lapor</th>
    <th>Isi Laporan</th>
    <th>Status</th>
  </tr>
  <?php while ($data = mysqli_fetch_assoc($query)) : ?>
  <tr>
    <td><?= $data['nama_siswa']; ?></td>
    <td><?= $data['guru_bk']; ?></td>
    <td><?= $data['tanggal_lapor']; ?></td>
    <td><?= $data['isi_laporan']; ?></td>
    <td><?= ucfirst($data['status_tindaklanjut']); ?></td>
  </tr>
  <?php endwhile; ?>
</table>

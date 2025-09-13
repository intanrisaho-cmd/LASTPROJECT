<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] !== 'guru_bk') {
  header("Location: dashboard.php");
  exit;
}

$id_guru_bk = $_SESSION['user_id'];

if (isset($_POST['proses_id'])) {
  $laporan_id = $_POST['proses_id'];
  $status = $_POST['status'];
  mysqli_query($conn, "UPDATE laporan_wali SET status_tindaklanjut = '$status' WHERE id = '$laporan_id'");
  echo "<script>alert('Status laporan diperbarui'); window.location='proses-laporan.php';</script>";
}

$query = mysqli_query($conn, "
  SELECT l.*, s.nama AS nama_siswa, u.nama AS wali_kelas
  FROM laporan_wali l
  JOIN siswa s ON l.id_siswa = s.id
  JOIN users u ON l.id_wali_kelas = u.id
  WHERE l.id_guru_bk = '$id_guru_bk'
  ORDER BY l.tanggal_lapor DESC
");
?>

<h2>Daftar Laporan untuk Anda</h2>
<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>Siswa</th>
    <th>Wali Kelas</th>
    <th>Tanggal</th>
    <th>Isi Laporan</th>
    <th>Status</th>
    <th>Ubah Status</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($query)) : ?>
  <tr>
    <td><?= $row['nama_siswa']; ?></td>
    <td><?= $row['wali_kelas']; ?></td>
    <td><?= $row['tanggal_lapor']; ?></td>
    <td><?= $row['isi_laporan']; ?></td>
    <td><?= ucfirst($row['status_tindaklanjut']); ?></td>
    <td>
      <form method="POST" style="display:inline-block">
        <input type="hidden" name="proses_id" value="<?= $row['id']; ?>">
        <select name="status" onchange="this.form.submit()">
          <option value="">--Pilih--</option>
          <option value="proses">Diproses</option>
          <option value="selesai">Selesai</option>
        </select>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

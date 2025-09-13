<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] !== 'orang_tua') {
  header("Location: dashboard.php");
  exit;
}

$id_siswa = $_SESSION['id_siswa'];

$result = mysqli_query($conn, "
  SELECT * FROM notifikasi_orang_tua
  WHERE id_siswa = '$id_siswa'
  ORDER BY waktu_kirim DESC
");
?>

<h2>Notifikasi untuk Orang Tua</h2>
<ul>
  <?php while ($notif = mysqli_fetch_assoc($result)) : ?>
    <li>
      <?= $notif['pesan']; ?> <br>
      <small><?= date("d M Y, H:i", strtotime($notif['waktu_kirim'])); ?></small>
    </li>
    <?php
    // Tandai sebagai dibaca
    mysqli_query($conn, "UPDATE notifikasi_orang_tua SET status_baca = 'sudah' WHERE id = {$notif['id']}");
    ?>
  <?php endwhile; ?>
</ul>

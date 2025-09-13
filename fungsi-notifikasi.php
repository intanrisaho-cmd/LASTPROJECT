<?php
function kirimNotifikasi($conn, $penerima, $pesan) {
  $stmt = $conn->prepare("INSERT INTO notifikasi (penerima, isi) VALUES (?, ?)");
  $stmt->bind_param("ss", $penerima, $pesan);
  $stmt->execute();
}
?>

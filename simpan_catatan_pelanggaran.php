<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $pelanggaran = $_POST['pelanggaran'];
    $tanggal = $_POST['tanggal'];
    $wali = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO catatan_pelanggaran (nama_siswa, no_hp_ortu, pelanggaran, tanggal, wali_kelas) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $no_hp, $pelanggaran, $tanggal, $wali);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: 'Catatan pelanggaran berhasil dikirim!',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              window.location.href = 'dashboard-wali.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: 'Catatan gagal dikirim. Coba lagi.',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              window.history.back();
            });
        </script>";
    }
}
?>

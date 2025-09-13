<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

$wali = $_SESSION['username'];

// Ambil data siswa yang diajar oleh wali ini
$siswa_result = $conn->query("SELECT * FROM siswa WHERE wali_kelas = '$wali'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buat Catatan Pelanggaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container mt-4">
    <h3 class="mb-4">Form Catatan Pelanggaran</h3>

    <!-- SweetAlert saat halaman dibuka -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'info',
          title: 'Catatan Pelanggaran',
          text: 'Silakan isi catatan pelanggaran siswa dengan lengkap.',
          timer: 3000,
          showConfirmButton: false
        });
      });
    </script>

    <form action="simpan_catatan_pelanggaran.php" method="POST">
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Siswa</label>
        <select name="nama" id="nama" class="form-select" required>
          <option value="">-- Pilih Siswa --</option>
          <?php while ($siswa = $siswa_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($siswa['nama']) ?>" data-hp="<?= htmlspecialchars($siswa['no_hp_ortu']) ?>">
              <?= htmlspecialchars($siswa['nama']) ?> (<?= htmlspecialchars($siswa['nis']) ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="no_hp" class="form-label">No HP Orang Tua</label>
        <input type="text" class="form-control" name="no_hp" id="no_hp" readonly required>
      </div>

      <div class="mb-3">
        <label for="pelanggaran" class="form-label">Jenis Pelanggaran</label>
        <textarea name="pelanggaran" class="form-control" id="pelanggaran" rows="3" required></textarea>
      </div>

      <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal Kejadian</label>
        <input type="date" name="tanggal" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary">Kirim Catatan</button>
    </form>
  </div>

  <script>
    // Otomatis isi no HP saat pilih siswa
    document.getElementById('nama').addEventListener('change', function () {
      const selected = this.options[this.selectedIndex];
      const hp = selected.getAttribute('data-hp') || '';
      document.getElementById('no_hp').value = hp;
    });
  </script>
</body>
</html>

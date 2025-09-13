<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data catatan pelanggaran
$query = "
    SELECT c.id, s.nama AS nama_siswa, s.nis, g.nama AS nama_guru, c.tanggal, c.jam, c.keterangan
    FROM catatan c
    JOIN siswa s ON c.username = s.username
    JOIN guru_bk g ON c.guru = g.id
    ORDER BY c.tanggal DESC, c.jam DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Catatan Pelanggaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <h3 class="mb-4">Data Catatan Pelanggaran</h3>
  <a href="tambah-catatan.php" class="btn btn-primary mb-3">+ Tambah Catatan</a>
  
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Siswa</th>
          <th>NIS</th>
          <th>Guru BK</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Keterangan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): 
            $no = 1;
            while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
            <td><?= htmlspecialchars($row['nis']) ?></td>
            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['jam']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
            <td>
              <a href="edit-catatan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="hapus-catatan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus catatan ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr>
            <td colspan="8" class="text-center">Belum ada catatan pelanggaran.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

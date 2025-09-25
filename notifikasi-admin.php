<?php
session_start();
include 'config.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data catatan pelanggaran
$dataCatatan = mysqli_query($conn, "SELECT * FROM catatan_pelanggaran ORDER BY waktu DESC");
if (!$dataCatatan) {
    die("Query error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Konseling - Catatan Pelanggaran</title>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { font-family: 'Segoe UI', sans-serif; display: flex; background-color: #f4f6f8; }
    .sidebar { width: 240px; background-color: #2c3e50; padding: 20px 0; color: white; height: 100vh; position: fixed; display: flex; flex-direction: column; }
    .sidebar h2 { text-align: center; margin-bottom: 20px; font-size: 22px; font-weight: 500; }
    .sidebar a { display: flex; align-items: center; padding: 12px 20px; color: white; text-decoration: none; transition: 0.3s; }
    .sidebar a i { margin-right: 12px; }
    .sidebar a:hover { background-color: #34495e; }
    .logout-btn { margin-top: auto; margin: 20px; padding: 10px; background-color: #e74c3c; color: white; text-align: center; border-radius: 5px; text-decoration: none; }
    .logout-btn:hover { background-color: #c0392b; }
    .main-content { margin-left: 240px; padding: 30px; flex: 1; width: 100%; }
    .card-header { background-color: #2980b9; color: white; font-weight: 600; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Konseling</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="data-siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a>
  <a href="data-guru.php"><i class="fas fa-user-tie"></i> Data Guru BK</a>
  <!-- <a href="data-pelanggaran.php"><i class="fas fa-exclamation-triangle"></i> Data Pelanggaran</a> -->
  <a href="jadwal-konseling.php"><i class="fas fa-calendar-alt"></i> Jadwal Konseling</a>
  <a href="catatan-admin.php"><i class="fas fa-book"></i> Catatan Guru BK</a>
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
  <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Catatan Konseling</a>
  <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
  <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
  <div class="card shadow border-0">
    <div class="card-header">
      <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> Laporan Konseling Siswa</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="catatanTable" class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Siswa</th>
              <th>Catatan</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($dataCatatan)): ?>
              <tr>
                <td></td> <!-- Kolom No akan di-generate oleh DataTables -->
                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                <td><?= htmlspecialchars($row['catatan']) ?></td>
                <td><?= date('d M Y, H:i', strtotime($row['waktu'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" 
                          onclick="cetakCatatan('<?= htmlspecialchars($row['nama_siswa']) ?>',
                                                 '<?= htmlspecialchars($row['catatan']) ?>',
                                                 '<?= date('d-m-Y H:i:s', strtotime($row['waktu'])) ?>')">
                      <i class="bi bi-printer"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="hapusCatatan(<?= $row['id'] ?>)">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($dataCatatan) === 0): ?>
              <tr><td colspan="5" class="text-center">Belum ada catatan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var t = $('#catatanTable').DataTable({
        order: [[3, 'desc']],
        lengthMenu: [5, 10, 25, 50, 100],
        pageLength: 10,
        columnDefs: [
          { orderable: false, targets: 4 }, // Kolom aksi tidak bisa disort
          { orderable: false, targets: 0 }  // Kolom No tidak bisa disort
        ]
    });

    // Generate nomor urut sesuai halaman
    t.on('order.dt search.dt draw.dt', function () {
        t.column(0, { search:'applied', order:'applied' }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});

// Cetak catatan
function cetakCatatan(siswa, catatan, tanggal) {
  const w = window.open('', '_blank');
  w.document.write(`
    <html>
      <head><title>Cetak Catatan Pelanggaran</title></head>
      <body style="font-family: Arial; padding:20px;">
        <h2 style="text-align:center;">Catatan Pelanggaran Siswa</h2>
        <table style="width:100%; margin-top:20px;" border="0">
          <tr><td><strong>Siswa:</strong></td><td>${siswa}</td></tr>
          <tr><td><strong>Catatan:</strong></td><td>${catatan}</td></tr>
          <tr><td><strong>Tanggal:</strong></td><td>${tanggal}</td></tr>
        </table>
        <br><br><p style="text-align:right;">Tanda Tangan</p>
      </body>
    </html>
  `);
  w.document.close();
  w.print();
}

// Hapus catatan
function hapusCatatan(id) {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: "Catatan ini tidak bisa dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if(result.isConfirmed){
      window.location.href = 'hapus-catatan.php?id=' + id;
    }
  });
}
</script>

</body>
</html>

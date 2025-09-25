<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Proses balasan feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['balasan_id'])) {
    $id = intval($_POST['balasan_id']);
    $balasan = trim($_POST['balasan']);
    $stmt = $conn->prepare("UPDATE feedback SET balasan = ? WHERE id = ?");
    $stmt->bind_param("si", $balasan, $id);
    if ($stmt->execute()) {
        header("Location: admin_feedback.php?balas=berhasil");
        exit;
    } else {
        die("Query Error: " . $conn->error);
    }
}

$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Feedback Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: #f4f6f8; }
    .sidebar { width: 240px; background-color: #2c3e50; height: 100vh; color: white; position: fixed; padding-top: 20px; display: flex; flex-direction: column; }
    .sidebar h2 { text-align: center; font-size: 22px; margin-bottom: 20px; }
    .sidebar a { padding: 12px 20px; color: white; display: flex; align-items: center; text-decoration: none; }
    .sidebar a i { margin-right: 10px; }
    .sidebar a:hover { background-color: #34495e; }
    .logout-btn { margin-top: auto; margin: 20px; background-color: #e74c3c; text-align: center; padding: 10px; border-radius: 5px; text-decoration: none; color: white; }
    .logout-btn:hover { background-color: #c0392b; }
    .main { margin-left: 240px; padding: 30px; flex-grow: 1; }
    .table-container { background-color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
    h3 { color: #2c3e50; margin-bottom: 25px; }
    .badge-rating[data-rating="1"] { background-color: #e74c3c; color: white; }
    .badge-rating[data-rating="2"] { background-color: #e67e22; color: white; }
    .badge-rating[data-rating="3"] { background-color: #f1c40f; color: black; }
    .badge-rating[data-rating="4"] { background-color: #27ae60; color: white; }
    .badge-rating[data-rating="5"] { background-color: #2ecc71; color: white; }
  </style>
</head>
<body>

<script>
  // SweetAlert Notifikasi
  <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'berhasil'): ?>
    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Feedback berhasil dihapus!', timer: 2000, showConfirmButton: false });
  <?php endif; ?>
  <?php if (isset($_GET['balas']) && $_GET['balas'] === 'berhasil'): ?>
    Swal.fire({ icon: 'success', title: 'Balasan terkirim!', text: 'Feedback berhasil dibalas.', timer: 2000, showConfirmButton: false });
  <?php endif; ?>
</script>

<!-- Sidebar -->
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

<!-- Main Content -->
<div class="main">
  <div class="table-container">
    <h3><i class="fas fa-comment-dots me-2"></i> Feedback dari Siswa</h3>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Username</th>
            <th>Rating</th>
            <th>Tanggal</th>
            <th>Komentar</th>
            <th>Balasan Admin</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><span class="badge-rating" data-rating="<?= (int)$row['rating'] ?>"><?= $row['rating'] ?> / 5</span></td>
              <td class="text-muted"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
              <td><?= !empty($row['komentar']) ? nl2br(htmlspecialchars($row['komentar'])) : '<em class="text-muted">Tidak ada komentar</em>' ?></td>
              <td>
                <?= !empty($row['balasan']) ? nl2br(htmlspecialchars($row['balasan'])) : '<em class="text-muted">Belum dibalas</em>' ?>
              </td>
              <td>
                <button class="btn btn-sm btn-success" onclick="openBalasModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['komentar'])) ?>')">
                  <i class="fas fa-reply"></i> Balas
                </button>
                <button onclick="hapusFeedback(<?= $row['id'] ?>)" class="btn btn-sm btn-danger">
                  <i class="fas fa-trash-alt"></i> Hapus
                </button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Balasan -->
<div class="modal fade" id="balasModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-reply me-2"></i> Balas Feedback</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="balasan_id" id="balasan_id">
        <p><strong>Komentar:</strong></p>
        <p id="komentarText" class="border rounded p-2 bg-light"></p>
        <div class="mb-3">
          <label class="form-label">Balasan Admin</label>
          <textarea name="balasan" class="form-control" rows="4" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kirim Balasan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function hapusFeedback(id) {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: "Data feedback akan dihapus permanen!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'hapus-feedback.php?id=' + id;
    }
  });
}

function openBalasModal(id, komentar) {
  document.getElementById('balasan_id').value = id;
  document.getElementById('komentarText').innerText = komentar;
  new bootstrap.Modal(document.getElementById('balasModal')).show();
}
</script>

</body>
</html>

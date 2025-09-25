<?php 
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: login.php");
  exit;
}

// Proses update status via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['aksi'])) {
  $id = $_POST['id'];
  $username = $_POST['username'];
  $statusBaru = $_POST['aksi'] === 'konfirmasi' ? 'Dikonfirmasi' : 'Menunggu Konfirmasi';
  $notif = $_POST['aksi'] === 'konfirmasi' 
            ? 'Pengajuan Anda telah dikonfirmasi oleh Admin.' 
            : 'Pengajuan Anda sedang dalam proses dan menunggu konfirmasi dari Admin.';

  $stmt = $conn->prepare("UPDATE jadwal_konseling SET status = ?, notif_user = ? WHERE id = ?");
  $stmt->bind_param("ssi", $statusBaru, $notif, $id);
  echo json_encode(['success' => $stmt->execute(), 'error'=>$stmt->error]);
  exit;
}

$result = $conn->query("SELECT * FROM jadwal_konseling ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Konseling</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body { background: #f4f6f8; font-family: 'Segoe UI', sans-serif; margin:0; display:flex; }
    .sidebar { width: 240px; background:#2c3e50; height:100vh; color:#fff; position:fixed; display:flex; flex-direction:column; padding-top:20px;}
    .sidebar h2 { text-align:center; margin-bottom:20px; font-size:22px;}
    .sidebar a { padding:12px 20px; color:#fff; text-decoration:none; display:flex; align-items:center; }
    .sidebar a i { margin-right:10px; }
    .sidebar a:hover { background:#34495e; }
    .logout-btn { margin-top:auto; margin:20px; background:#e74c3c; text-align:center; padding:10px; border-radius:5px; color:#fff; text-decoration:none; }
    .main { margin-left:240px; padding:30px; flex-grow:1; }
    .header-box { background: linear-gradient(to right, #2c3e50, #2980b9); color:white; padding:20px; border-radius:10px; margin-bottom:20px;}
    .table-status { font-weight:bold; padding:5px 10px; border-radius:6px; color:white; display:inline-block;}
    .status-dikonfirmasi { background-color:#27ae60; }
    .status-menunggu { background-color:#f39c12; }
    .btn-action { border-radius:50%; width:35px; height:35px; padding:0; }
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

<div class="main">
  <div class="header-box">
    <h3 class="mb-0">📋 Laporan Konseling</h3>
    <p class="mb-0">Menampilkan semua data konseling dan ubah status sesuai kebutuhan</p>
  </div>

  <div class="table-responsive bg-white p-3 rounded shadow-sm">
    <table id="laporanTable" class="table table-bordered table-striped table-hover">
      <thead class="table-success">
        <tr>
          <th>No</th>
          <th>Username</th>
          <th>Guru BK</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Topik</th>
          <!-- <th>Deskripsi</th> -->
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if($result->num_rows>0): $no=1; ?>
          <?php while($row=$result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['guru']) ?></td>
              <td><?= htmlspecialchars($row['tanggal']) ?></td>
              <td><?= htmlspecialchars($row['jam']) ?></td>
              <td><?= htmlspecialchars($row['topik']) ?></td>
              <td><span class="table-status <?= $row['status']=='Dikonfirmasi'?'status-dikonfirmasi':'status-menunggu' ?>">
                <?= htmlspecialchars($row['status']) ?>
              </span></td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-success btn-sm btn-action btn-aksi" 
                    data-id="<?= $row['id'] ?>" 
                    data-aksi="konfirmasi" 
                    data-username="<?= $row['username'] ?>" title="Konfirmasi">
                    <i class="fas fa-check"></i>
                  </button>
                  <button class="btn btn-warning btn-sm btn-action btn-aksi" 
                    data-id="<?= $row['id'] ?>" 
                    data-aksi="menunggu" 
                    data-username="<?= $row['username'] ?>" title="Menunggu">
                    <i class="fas fa-clock"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center text-muted">Belum ada data konseling.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function(){
  $('#laporanTable').DataTable({
    "order": [[3, "desc"]],
    "pageLength": 10,
    "lengthMenu":[5,10,25,50]
  });

  $('.btn-aksi').click(function(e){
    e.preventDefault();
    const id = $(this).data('id');
    const aksi = $(this).data('aksi');
    const username = $(this).data('username');
    const text = aksi==='konfirmasi' ? 'konfirmasi pengajuan ini?' : 'menunda pengajuan ini?';
    
    Swal.fire({
      title: 'Yakin ingin ' + text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, lanjutkan',
      cancelButtonText: 'Batal',
      confirmButtonColor: aksi==='konfirmasi'?'#27ae60':'#f39c12'
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('',{id:id,aksi:aksi,username:username},function(res){
          try{
            const json = JSON.parse(res);
            if(json.success){
              Swal.fire({icon:'success',title:'Status berhasil diperbarui',timer:1500,showConfirmButton:false})
              .then(()=> location.reload());
            } else Swal.fire('Gagal', json.error||'Terjadi kesalahan','error');
          }catch(e){ Swal.fire('Gagal','Respon tidak valid dari server.','error'); }
        });
      }
    });
  });
});
</script>

</body>
</html>

<?php
session_start();
include 'config.php';

// ✅ Cek apakah sudah login dan rolenya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// ✅ CREATE
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $jenis = $_POST['jenis_pelanggaran'];

    $stmt = $conn->prepare("INSERT INTO pelanggaran (nama, kelas, jenis_pelanggaran) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $kelas, $jenis);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pesan'] = "tambah";
    header("Location: data-pelanggaran.php");
    exit;
}

// ✅ UPDATE
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $jenis = $_POST['jenis_pelanggaran'];

    $stmt = $conn->prepare("UPDATE pelanggaran SET nama=?, kelas=?, jenis_pelanggaran=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $kelas, $jenis, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pesan'] = "edit";
    header("Location: data-pelanggaran.php");
    exit;
}

// ✅ DELETE
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM pelanggaran WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pesan'] = "hapus";
    header("Location: data-pelanggaran.php");
    exit;
}

// ✅ READ
$query = "SELECT * FROM pelanggaran ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa Pelanggaran</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      display: flex;
    }
    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #2c3e50;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 20px;
      font-weight: 500;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 20px;
      color: #ffc107;
      font-weight: 600;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
      font-size: 15px;
    }
    .sidebar a:hover {
      background: #495057;
      color: #ffc107;
    }
    .sidebar a i {
      margin-right: 10px;
    }
    .logout-btn {
      position: absolute;
      bottom: 20px;
      width: 100%;
      background: #dc3545;
      text-align: center;
    }
    /* Konten */
    .content {
      margin-left: 250px;
      padding: 20px;
      width: 100%;
    }
    .card-header h4 {
      font-weight: 600;
    }
    table thead {
      font-weight: 600;
    }
    .modal-title {
      font-weight: 600;
    }
  </style>
</head>
<body>

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

<!-- Konten -->
<div class="content">
  <div class="container-fluid mt-3">
    <div class="card shadow">
      <div class="card-header bg-danger text-white d-flex justify-content-between">
        <h4 class="mb-0">📋 Data Siswa Yang Melakukan Pelanggaran</h4>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah Data</button>
      </div>
      <div class="card-body">
        <table id="tabelPelanggaran" class="table table-bordered table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Nama Siswa</th>
              <th>Kelas</th>
              <th>Jenis Pelanggaran</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$no++."</td>
                        <td>".$row['nama']."</td>
                        <td>".$row['kelas']."</td>
                        <td>".$row['jenis_pelanggaran']."</td>
                        <td>
                          <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#modalEdit".$row['id']."'><i class='fas fa-edit'></i></button>
                          <a href='?hapus=".$row['id']."' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i></a>
                        </td>
                      </tr>";

                // Modal Edit
                echo "
                <div class='modal fade' id='modalEdit".$row['id']."' tabindex='-1'>
                  <div class='modal-dialog'>
                    <div class='modal-content'>
                      <div class='modal-header bg-primary text-white'>
                        <h5 class='modal-title'>Edit Data Pelanggaran</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                      </div>
                      <form method='post'>
                        <div class='modal-body'>
                          <input type='hidden' name='id' value='".$row['id']."'>
                          <div class='mb-3'>
                            <label>Nama Siswa</label>
                            <input type='text' name='nama' class='form-control' value='".$row['nama']."' required>
                          </div>
                          <div class='mb-3'>
                            <label>Kelas</label>
                            <input type='text' name='kelas' class='form-control' value='".$row['kelas']."' required>
                          </div>
                          <div class='mb-3'>
                            <label>Jenis Pelanggaran</label>
                            <textarea name='jenis_pelanggaran' class='form-control' required>".$row['jenis_pelanggaran']."</textarea>
                          </div>
                        </div>
                        <div class='modal-footer'>
                          <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                          <button type='submit' name='edit' class='btn btn-primary'>Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Tambah Data Pelanggaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label>Nama Siswa</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Kelas</label>
            <input type="text" name="kelas" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Jenis Pelanggaran</label>
            <textarea name="jenis_pelanggaran" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
      $('#tabelPelanggaran').DataTable();
  });
</script>

<?php if (isset($_SESSION['pesan'])): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php 
      if ($_SESSION['pesan'] == "tambah") echo "Data berhasil ditambahkan!";
      if ($_SESSION['pesan'] == "edit") echo "Data berhasil diedit!";
      if ($_SESSION['pesan'] == "hapus") echo "Data berhasil dihapus!";
    ?>',
    showConfirmButton: false,
    timer: 2000
  });
</script>
<?php unset($_SESSION['pesan']); endif; ?>

</body>
</html>

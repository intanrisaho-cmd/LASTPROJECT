<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role  = $_SESSION['role'];
$wali  = $_SESSION['username'];
$redirect_to_wa = false;
$link_wa = "";

// Ambil data siswa
if ($role === 'wali') {
    $siswa_query = $conn->prepare("SELECT * FROM siswa WHERE wali_kelas = ?");
    $siswa_query->bind_param("s", $wali);
} else { // admin
    $siswa_query = $conn->prepare("SELECT * FROM siswa");
}
$siswa_query->execute();
$result     = $siswa_query->get_result();
$siswa_list = $result->fetch_all(MYSQLI_ASSOC);

// Proses form wali
if ($role === 'wali' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim'])) {
    $nama        = htmlspecialchars($_POST['nama']);
    $catatan = htmlspecialchars($_POST['catatan']);
    $no_hp       = htmlspecialchars($_POST['no_hp']);
    $waktu       = date("Y-m-d H:i:s");

    // Simpan ke tabel catatan_pelanggaran
    $stmt = $conn->prepare("
        INSERT INTO catatan_pelanggaran 
        (nama_siswa, catatan, no_hp, waktu, dikirim_oleh) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $nama, $catatan, $no_hp, $waktu, $wali);
    $stmt->execute();

    // Simpan ke notifikasi_admin
    $stmtNotif = $conn->prepare("
        INSERT INTO notifikasi_admin 
        (nama_siswa, catatan, waktu, dikirim_oleh) 
        VALUES (?, ?, ?, ?)
    ");
    $stmtNotif->bind_param("ssss", $nama, $catatan, $waktu, $wali);
    $stmtNotif->execute();

    // Link WA
    $pesan   = "Assalamualaikum, kami dari sekolah ingin menginformasikan bahwa ananda *$nama* telah melakukan pelanggaran. Mohon kehadiran Bapak/Ibu ke sekolah. Pelanggaran: *$pelanggaran* pada $waktu.";
    $link_wa = "https://wa.me/$no_hp?text=" . urlencode($pesan);
    $redirect_to_wa = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Wali Kelas</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <style>
    body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
    .sidebar { width: 250px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(to bottom, #0d6efd, #0a58ca); color: white; padding-top: 20px; }
    .sidebar h4 { text-align: center; font-weight: 600; margin-bottom: 30px; }
    .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; font-weight: 500; }
    .sidebar a:hover { background: rgba(255,255,255,0.1); color: #ffd700; }
    .navbar-custom { margin-left: 250px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 10px 20px; display: flex; justify-content: flex-end; align-items: center; position: sticky; top: 0; z-index: 1000; }
    .profile-icon { font-size: 2rem; color: #0d6efd; }
    .content { margin-left: 260px; padding: 20px; }
    .container-box { background: #fff; padding: 2rem; border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
    #daftarSiswa { max-height: 200px; overflow-y: auto; display: none; }
    table.dataTable td, table.dataTable th { vertical-align: middle; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4>📘 Wali Kelas</h4>
  <a href="#">📝 Catatan Konseling</a>
  <a href="riwayat.php">📂 Riwayat</a>
</div>

<!-- Navbar -->
<div class="navbar-custom">
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" 
       id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
      <span class="fw-bold me-2"><?= htmlspecialchars($wali) ?></span>
      <i class="bi bi-person-circle profile-icon"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
      <li><h6 class="dropdown-header">👤 <?= htmlspecialchars($wali) ?></h6></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
    </ul>
  </div>
</div>


<!-- Konten Utama -->
<div class="content">
  <h2 class="mb-4">Selamat Datang, <span class="text-primary"><?= htmlspecialchars($wali); ?></span> 👋</h2>

  <!-- Form Catatan -->
  <div class="container-box">
    <h4 class="mb-4">📝 Form Catatan Konseling Siswa</h4>

    <div class="mb-3">
      <label for="cariSiswa" class="form-label">🔍 Cari Nama Siswa</label>
      <input type="text" id="cariSiswa" class="form-control" placeholder="Ketik nama siswa...">
      <small class="text-muted">Klik nama siswa untuk mengisi otomatis</small>
    </div>

    <ul class="list-group mt-2" id="daftarSiswa">
      <?php foreach ($siswa_list as $siswa): ?>
        <li class="list-group-item list-group-item-action"
            data-nama="<?= htmlspecialchars($siswa['nama']) ?>"
            data-hp="<?= htmlspecialchars($siswa['no_hp_ortu']) ?>"
            style="cursor: pointer;">
          <?= htmlspecialchars($siswa['nama']) ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <form method="POST" class="mt-3">
      <div class="mb-3">
        <label class="form-label">Nama Siswa</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Topik Konseling </label>
        <textarea name="pelanggaran" class="form-control" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Nomor HP Orang Tua</label>
        <input type="text" name="no_hp" class="form-control" required>
      </div>
      <button type="submit" name="kirim" class="btn btn-primary">Kirim Notifikasi</button>
    </form>
  </div>

  <!-- Data Siswa -->
  <div class="container-box">
    <h5>📋 <?= ($role === 'admin') ? "Semua Data Siswa" : "Data Siswa Wali Kelas"; ?></h5>
    <div class="table-responsive mt-3">
      <table id="tabelSiswa" class="table table-bordered table-striped align-middle w-100">
        <thead class="table-primary text-center">
          <tr>
            <th style="width:60px">No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>No HP Orang Tua</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($siswa_list)): ?>
            <?php $no = 1; foreach ($siswa_list as $siswa): ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($siswa['nis']) ?></td>
                <td><?= htmlspecialchars($siswa['nama']) ?></td>
                <td><?= htmlspecialchars($siswa['kelas']) ?></td>
                <td><?= htmlspecialchars($siswa['no_hp_ortu']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          <!-- ⚠️ Tidak ada baris dengan colspan di sini -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if ($redirect_to_wa): ?>
<script>
  window.onload = function() {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Catatan disimpan dan akan dialihkan ke WhatsApp...',
      timer: 2000,
      showConfirmButton: false
    });
    setTimeout(function() {
      window.location.href = <?= json_encode($link_wa); ?>;
    }, 2000);
  }
</script>
<?php endif; ?>

<!-- JS Bootstrap & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
  // Inisialisasi DataTables
  $('#tabelSiswa').DataTable({
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    language: {
      emptyTable: "Tidak ada data siswa.",       // ← Pakai ini untuk tabel kosong
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data per halaman",
      zeroRecords: "Tidak ada data yang cocok",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data tersedia",
      infoFiltered: "(disaring dari _MAX_ total data)",
      paginate: {
        first: "Pertama",
        last: "Terakhir",
        next: "➡",
        previous: "⬅"
      }
    }
  });

  // Autocomplete siswa
  const searchInput = document.getElementById("cariSiswa");
  const daftarSiswa = document.getElementById("daftarSiswa");
  const listItems   = daftarSiswa.getElementsByTagName("li");

  searchInput.addEventListener("input", function () {
    const keyword = this.value.toLowerCase().trim();
    let matchFound = false;
    if (keyword === "") {
      daftarSiswa.style.display = "none";
      return;
    }
    daftarSiswa.style.display = "block";
    Array.from(listItems).forEach(item => {
      const nama = item.textContent.toLowerCase();
      const cocok = nama.includes(keyword);
      item.style.display = cocok ? "block" : "none";
      if (cocok) matchFound = true;
    });
    if (!matchFound) daftarSiswa.style.display = "none";
  });

  Array.from(listItems).forEach(item => {
    item.addEventListener("click", function () {
      const nama = this.getAttribute("data-nama");
      const hp   = this.getAttribute("data-hp");
      document.querySelector('input[name="nama"]').value = nama;
      document.querySelector('input[name="no_hp"]').value = hp;
      Swal.fire({
        icon: 'info',
        title: 'Data Terisi Otomatis',
        text: `Nama dan nomor HP orang tua ${nama} telah diisi.`,
        timer: 1500,
        showConfirmButton: false
      });
      searchInput.value = '';
      daftarSiswa.style.display = "none";
    });
  });
});
</script>
</body>
</html>

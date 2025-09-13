<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: login.php");
    exit;
}

// Cek NIS
if (!isset($_GET['nis']) || empty($_GET['nis'])) {
    header("Location: pilih_siswa_notifikasi.php");
    exit;
}

$nis = $_GET['nis'];
$wali = $_SESSION['username'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    header("Location: pilih_siswa_notifikasi.php");
    exit;
}

$success = false;

// Proses kirim notifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan = trim($_POST['pesan']);
    $tanggal = date("Y-m-d H:i:s");
    $nama_siswa = $siswa['nama'];

    $insert = $conn->prepare("INSERT INTO notifikasi (nis, nama_siswa, pesan, tanggal, wali_kelas) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssss", $nis, $nama_siswa, $pesan, $tanggal, $wali);
    if ($insert->execute()) {
        $success = true;

    }
}

// Kirim pesan ke WhatsApp orang tua
$noWaOrtu = $siswa['no_wa_ortu'];
$pesanWa = "*Notifikasi Wali Kelas*\n\nUntuk: *" . $siswa['nama'] . "*\n\nPesan:\n" . $pesan . "\n\n- Wali Kelas";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://kirim.pesanWA.id/v2/send-message', // Contoh endpoint Wablas
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => http_build_query([
      'api_key' => 'ISI_API_KEY_KAMU',
      'phone'   => $noWaOrtu,
      'message' => $pesanWa
  ]),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kirim Notifikasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: #d7daf3ff;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      background-color: #8c48d0ff;
    }

    .navbar-brand, .nav-link {
      color: white !important;
    }

    .nav-link:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 800px;
      margin-top: 100px;
    }

    .card {
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      padding: 30px;
      background-color: white;
    }

    .btn-kirim {
      background-color: #2e6959ff;
      color: white;
      border: none;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 8px;
    }

    .btn-kirim i {
      margin-right: 6px;
    }

    .btn-kirim:hover {
      background-color: #0b5ed7;
    }

    footer {
      text-align: center;
      margin-top: 60px;
      font-size: 14px;
      color: #888;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4 no-print">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">📘 Dashboard Wali Kelas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWali">
            <span class="navbar-toggler-icon text-light"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarWali">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="wali_dashboard.php">🏠 Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pilih_siswa_lapor.php">📑 Lapor Pelanggaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">🔓 Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Form -->
<div class="container">
  <div class="card">
    <h4 class="mb-4 text-center text-primary">Kirim Notifikasi ke Orang Tua</h4>
    <p class="text-muted"><strong>Nama Siswa:</strong> <?= htmlspecialchars($siswa['nama']) ?><br>
       <strong>NIS:</strong> <?= htmlspecialchars($siswa['nis']) ?></p>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Isi Pesan</label>
        <textarea name="pesan" class="form-control rounded-3" rows="5" placeholder="Tuliskan isi pesan notifikasi..." required></textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-kirim">
          <i class="bi bi-send-fill"></i> Kirim
        </button>
      </div>
    </form>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Sistem Monitoring Wali Kelas
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Notifikasi Terkirim!',
    text: 'Pesan berhasil dikirim ke orang tua.',
    confirmButtonText: 'OK'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "wali_dashboard.php";
    }
  });
</script>
<?php endif; ?>

</body>
</html>

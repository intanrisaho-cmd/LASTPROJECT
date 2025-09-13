<?php
session_start();
include 'config.php';

// Pastikan hanya user yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$success = false;
$error = '';

// Proses kirim feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating   = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $komentar = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

    if ($rating < 1 || $rating > 5 || empty($komentar)) {
        $error = "Silakan isi rating (1–5) dan komentar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (username, rating, komentar, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("sis", $username, $rating, $komentar);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Gagal menyimpan feedback. " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Query error: " . $conn->error;
        }
    }
}

// Ambil daftar feedback milik user ini
$stmt = $conn->prepare("SELECT * FROM feedback WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$feedbacks = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rating & Feedback Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { 
    background: linear-gradient(135deg, #e0f7fa, #f1f8e9); 
    padding-top: 90px; 
    font-family: 'Segoe UI', sans-serif; 
    min-height: 100vh;
}
.navbar-brand { font-weight: bold; }
.feedback-card {
    padding: 30px 25px;
    background: #fff; border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    height: 100%;
}
.feedback-title { 
    color: #2c3e50; 
    text-align: center; 
    font-weight: 700; 
    margin-bottom: 25px; 
}
.rating-select { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
.rating-select input { display: none; }
.rating-select label { font-size: 2rem; color: #ddd; cursor: pointer; transition: 0.3s; }
.rating-select input:checked ~ label,
.rating-select label:hover,
.rating-select label:hover ~ label { color: #ffc107; transform: scale(1.2); }
textarea.form-control { border-radius: 12px; padding: 12px; }
.btn-submit { display: block; width: 100%; padding: 14px; border-radius: 12px; font-weight: 600; color: #fff;
    background: linear-gradient(90deg,#27ae60,#2ecc71); border: none; transition: all 0.3s; }
.btn-submit:hover { background: linear-gradient(90deg,#2ecc71,#27ae60); }
.feedback-item { background: #fafafa; padding: 15px 20px; border-radius: 12px; margin-bottom: 15px; border: 1px solid #eee; }
.feedback-meta { font-size: 0.9rem; color: #666; }
.feedback-balasan { margin-top: 8px; padding: 10px; border-left: 4px solid #27ae60; background: #f1fdf3; border-radius: 6px; font-size: 0.95rem; }

/* Atur posisi card lebih ke tengah */
.section-center {
    margin-top: 50px;
    margin-bottom: 50px;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
<div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center text-success" href="user_dashboard.php">
      <img src="images/sma.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
      <strong>Konseling Siswa</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarUser">
      <ul class="navbar-nav mb-2 mb-lg-0 gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="user_dashboard.php"><i class="bi bi-house"></i> Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="jadwal_konseling.php"><i class="bi bi-calendar-week-fill"></i> Jadwal</a></li>
        <li class="nav-item"><a class="nav-link" href="status_pengajuan.php"><i class="bi bi-hourglass-split"></i> Status</a></li>
        <li class="nav-item"><a class="nav-link" href="riwayat_konseling.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="catatan-bk-user.php"><i class="bi bi-journals"></i> Catatan Guru BK</a></li>
        <li class="nav-item"><a class="nav-link active text-primary" href="rating_konseling.php"><i class="bi bi-star-fill"></i> Feedback</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
</div>
</nav>

<!-- Konten -->
<div class="container section-center">
  <div class="row justify-content-center g-4">
    
    <!-- Riwayat Feedback (Kiri) -->
    <div class="col-lg-6 col-md-12">
      <div class="feedback-card h-100">
        <h5 class="mb-3"><i class="bi bi-chat-left-text me-2"></i> Riwayat Feedback Anda</h5>
        <div class="feedback-list">
          <?php if ($feedbacks->num_rows > 0): ?>
              <?php while ($row = $feedbacks->fetch_assoc()): ?>
                  <div class="feedback-item">
                      <div><strong>Rating:</strong> <?= $row['rating'] ?> / 5</div>
                      <div class="mt-1"><?= nl2br(htmlspecialchars($row['komentar'])) ?></div>
                      <div class="feedback-meta mt-1">Dikirim: <?= date('d M Y H:i', strtotime($row['created_at'])) ?></div>
                      <?php if (!empty($row['balasan'])): ?>
                          <div class="feedback-balasan">
                              <strong>Balasan Admin:</strong><br><?= nl2br(htmlspecialchars($row['balasan'])) ?>
                          </div>
                      <?php endif; ?>
                  </div>
              <?php endwhile; ?>
          <?php else: ?>
              <p class="text-muted">Belum ada feedback yang Anda kirim.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Form Feedback (Kanan) -->
    <div class="col-lg-6 col-md-12">
      <div class="feedback-card h-100">
        <h3 class="feedback-title"><i class="bi bi-star-fill me-2"></i>Rating & Feedback</h3>
        <form method="post">
          <div class="rating-select">
            <?php for($i=5;$i>=1;$i--): ?>
              <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
              <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
          </div>
          <div class="mb-4">
            <textarea name="feedback" class="form-control" rows="5" placeholder="Tulis komentar Anda..." required></textarea>
          </div>
          <button type="submit" class="btn-submit"><i class="bi bi-send-fill me-2"></i>Kirim Feedback</button>
        </form>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// SweetAlert feedback
<?php if ($success): ?>
Swal.fire({ icon: 'success', title: 'Terima kasih!', text: 'Feedback berhasil dikirim.', confirmButtonColor:'#27ae60' });
<?php elseif($error): ?>
Swal.fire({ icon: 'error', title: 'Oops...', text: <?= json_encode($error) ?>, confirmButtonColor:'#dc3545' });
<?php endif; ?>


// Logout dengan SweetAlert
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Sesi Anda akan diakhiri.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, logout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
});
</script>
</body>
</html>

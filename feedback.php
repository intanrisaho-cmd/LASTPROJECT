<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];
$success = false;
$error = '';

// Proses kirim feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
  $komentar = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

  if ($rating < 1 || $rating > 5 || empty($komentar)) {
    $error = "Silakan isi rating (1–5) dan komentar.";
  } else {
    // Pastikan kolom rating ada
    $checkColumn = $conn->query("SHOW COLUMNS FROM feedback LIKE 'rating'");
    if ($checkColumn->num_rows === 0) {
      // Tambahkan kolom rating jika belum ada
      $conn->query("ALTER TABLE feedback ADD COLUMN rating INT");
    }

    $query = "INSERT INTO feedback (username, rating, komentar, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rating & Feedback Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="mb-4 text-warning">
          <i class="bi bi-star-fill me-2"></i>Rating & Feedback Konseling
        </h3>

        <?php if ($success): ?>
          <div class="alert alert-success">✅ Feedback berhasil dikirim. Terima kasih atas penilaian Anda!</div>
        <?php elseif ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="mb-3">
            <label class="form-label">Rating (1–5)</label>
            <select name="rating" class="form-select" required>
              <option value="">Pilih rating</option>
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>" <?= isset($_POST['rating']) && $_POST['rating'] == $i ? 'selected' : '' ?>>
                  <?= $i ?> - <?= str_repeat('★', $i) ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Komentar / Masukan</label>
            <textarea name="feedback" class="form-control" rows="4" required><?= isset($_POST['feedback']) ? htmlspecialchars($_POST['feedback']) : '' ?></textarea>
          </div>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-send-fill"></i> Kirim Feedback
          </button>
          <a href="user_dashboard.php" class="btn btn-secondary ms-2">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

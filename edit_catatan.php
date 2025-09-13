<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data catatan
$result = $conn->query("SELECT * FROM catatan WHERE id = $id");
$data = $result->fetch_assoc();

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catatan = $_POST['catatan'];
    $stmt = $conn->prepare("UPDATE catatan SET catatan = ? WHERE id = ?");
    $stmt->bind_param("si", $catatan, $id);

    if ($stmt->execute()) {
        header("Location: catatan_guru.php?pesan=diedit");
        exit;
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Catatan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Catatan</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan</label>
            <textarea name="catatan" id="catatan" class="form-control" rows="5" required><?= htmlspecialchars($data['catatan']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="catatan_guru.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>

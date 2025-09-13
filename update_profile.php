<?php
session_start();
include 'config.php';

// Pastikan hanya user yang login yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil data lama user
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil data dari form
$new_username = $_POST['username'];
$new_email    = $_POST['email'];
$new_password = !empty($_POST['password']) 
                ? password_hash($_POST['password'], PASSWORD_DEFAULT) 
                : $user['password'];

// Upload foto (jika ada)
$foto = $user['foto'];
if (!empty($_FILES['foto']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_name = time() . "_" . basename($_FILES["foto"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        $foto = $file_name; // hanya simpan nama file, bukan path lengkap
    }
}

// Update data user
$update = $conn->prepare("UPDATE users SET username=?, email=?, password=?, foto=? WHERE username=?");
$update->bind_param("sssss", $new_username, $new_email, $new_password, $foto, $username);

if ($update->execute()) {
    $_SESSION['username'] = $new_username;

    // Simpan notifikasi di session untuk ditampilkan di dashboard
    $_SESSION['notif'] = [
        'type' => 'success',
        'title' => 'Berhasil!',
        'message' => 'Profil berhasil diperbarui.'
    ];

    header("Location: user_dashboard.php");
    exit;
} else {
    $_SESSION['notif'] = [
        'type' => 'error',
        'title' => 'Gagal!',
        'message' => 'Profil gagal diperbarui.'
    ];
    header("Location: user_dashboard.php");
    exit;
}
?>

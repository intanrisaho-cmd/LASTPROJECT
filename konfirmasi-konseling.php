<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $conn->query("UPDATE konseling SET status = 'Dikonfirmasi' WHERE id = $id");
}

header("Location: from-konseling-admin.php");
exit;
?>

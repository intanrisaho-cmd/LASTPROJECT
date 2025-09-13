<?php
include 'config.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $conn->query("DELETE FROM notifikasi WHERE id = $id");
}

header("Location: notifikasi-admin.php");
exit;

<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE notifikasi SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $id);
  $stmt->execute();

  echo "Status diperbarui.";
}

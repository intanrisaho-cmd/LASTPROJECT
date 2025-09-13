<?php
session_start();
include 'config.php';
$nama = $_SESSION['username'];
$conn->query("UPDATE notifikasi SET status='dibaca' WHERE penerima='$nama' OR penerima='all'");
header("Location: admin_dashboard.php");

<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID notifikasi tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM notifikasi WHERE id = $id");

if ($query->num_rows == 0) {
    echo "Notifikasi tidak ditemukan.";
    exit;
}

$data = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Notifikasi</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            color: #333;
        }
        .cetak-container {
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .btn-print {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 30px;
            font-weight: bold;
        }
        .btn-print:hover {
            background-color: #218838;
        }

        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="cetak-container">
        <h2>Detail Notifikasi</h2>
        <table>
            <tr>
                <td><strong>Pengirim</strong></td>
                <td><?= htmlspecialchars($data['wali_kelas']) ?></td>
            </tr>
            <tr>
                <td><strong>nama</strong></td>
                <td><?= htmlspecialchars($data['nama_siswa']) ?></td>
            </tr>
            <tr>
                <td><strong>Isi</strong></td>
                <td><?= nl2br(htmlspecialchars($data['pesan'])) ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td><?= date('d-m-Y H:i:s', strtotime($data['tanggal'])) ?></td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td><?= htmlspecialchars($data['status']) ?></td>
            </tr>
        </table>
        <a href="#" class="btn-print" onclick="window.print()">🖨️ Cetak Notifikasi</a>
    </div>
</body>
</html>

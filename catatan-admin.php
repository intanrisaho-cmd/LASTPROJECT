<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ================== HAPUS CATATAN ==================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM catatan WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['status'] = ['success', 'Berhasil!', 'Catatan berhasil dihapus.'];
    } else {
        $_SESSION['status'] = ['error', 'Gagal!', 'Catatan gagal dihapus.'];
    }
    header("Location: catatan-admin.php");
    exit;
}

// ================== EDIT MODE ==================
$editMode = false;
$editData = [];
if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = intval($_GET['edit']);
    $editQuery = $conn->prepare("SELECT * FROM catatan WHERE id=?");
    $editQuery->bind_param("i", $editId);
    $editQuery->execute();
    $result = $editQuery->get_result();
    if ($result && $result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
    $editQuery->close();
}

// ================== TAMBAH / UPDATE CATATAN ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_siswa = $_POST['username_siswa'];
    $guru           = $_POST['guru'];
    $tanggal        = $_POST['tanggal'];
    $jam            = $_POST['jam'];
    $catatan        = $_POST['catatan'];
    $nilai          = $_POST['nilai'] ?? null;

    // UPDATE
    if (isset($_POST['edit_id'])) {
        $id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE catatan SET username_siswa=?, guru=?, tanggal=?, jam=?, catatan=?, nilai=? WHERE id=?");
        $stmt->bind_param("sssssii", $username_siswa, $guru, $tanggal, $jam, $catatan, $nilai, $id);
        if ($stmt->execute()) {
            $_SESSION['status'] = ['success', 'Berhasil!', 'Catatan berhasil diperbarui.'];
        } else {
            $_SESSION['status'] = ['error', 'Gagal!', 'Catatan gagal diperbarui.'];
        }
        $stmt->close();
        header("Location: catatan-admin.php");
        exit;

    // INSERT
    } else {
        $stmt = $conn->prepare("INSERT INTO catatan (username_siswa, guru, tanggal, jam, catatan, nilai) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username_siswa, $guru, $tanggal, $jam, $catatan, $nilai);
        if ($stmt->execute()) {
            $_SESSION['status'] = ['success', 'Berhasil!', 'Catatan berhasil disimpan.'];
        } else {
            $_SESSION['status'] = ['error', 'Gagal!', 'Catatan gagal disimpan.'];
        }
        $stmt->close();
        header("Location: catatan-admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Guru BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; min-height: 100vh; margin: 0; }
        .sidebar { width: 250px; background-color: #2c3e50; padding: 20px; color: #fff; }
        .sidebar a { display: block; padding: 10px; color: #ecf0f1; text-decoration: none; margin-bottom: 8px; border-radius: 5px; }
        .sidebar a:hover { background-color: #34495e; }
        .logout-btn { margin-top: 20px; background-color: #c0392b; color: white; }
        .content { flex-grow: 1; padding: 20px; }
        table th, table td { font-size: 14px; vertical-align: middle; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Konseling</h2>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data-siswa.php"><i class="fas fa-user-graduate"></i> Data Siswa</a>
    <a href="data-guru.php"><i class="fas fa-user-tie"></i> Data Guru BK</a>
    <!-- <a href="data-pelanggaran.php"><i class="fas fa-exclamation-triangle"></i> Data Pelanggaran</a> -->
    <a href="jadwal-konseling.php"><i class="fas fa-calendar-alt"></i> Jadwal Konseling</a>
    <a href="catatan-admin.php"><i class="fas fa-book"></i> Catatan Guru BK</a>
    <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
    <a href="notifikasi-admin.php"><i class="fas fa-bell"></i> Notifikasi</a>
    <a href="admin_feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">
    <div class="container-fluid">
        <h2 class="mb-4">Catatan Konseling Siswa</h2>

        <!-- Form Tambah / Edit -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?= $editMode ? 'Edit Catatan' : 'Tulis Catatan Baru' ?>
            </div>
            <div class="card-body">
                <form id="formCatatan" method="POST">
                    <?php if ($editMode): ?>
                        <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <!-- Pilih Siswa -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Siswa</label>
                        <select name="username_siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php
                            $userQuery = $conn->query("SELECT username, nama FROM users WHERE role = 'user'");
                            while ($row = $userQuery->fetch_assoc()):
                                $selected = ($editMode && $editData['username_siswa'] === $row['username']) ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($row['username']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($row['nama']) ?> (<?= $row['username'] ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Pilih Guru BK -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Guru BK</label>
                        <select name="guru" class="form-select" required>
                            <option value="">-- Pilih Guru BK --</option>
                            <?php
                            $guru = $conn->query("SELECT username, nama FROM guru_bk");
                            while ($row = $guru->fetch_assoc()) {
                                $selected = ($editMode && $editData['guru'] === $row['username']) ? 'selected' : '';
                                echo "<option value='{$row['username']}' $selected>{$row['nama']} ({$row['username']})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $editData['tanggal'] ?? '' ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Jam</label>
                            <input type="time" name="jam" class="form-control" value="<?= $editData['jam'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Konseling</label>
                        <textarea name="catatan" class="form-control" rows="4" required><?= $editData['catatan'] ?? '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nilai (opsional)</label>
                        <input type="number" name="nilai" class="form-control" min="0" max="100" value="<?= $editData['nilai'] ?? '' ?>">
                    </div>

                    <button type="submit" class="btn btn-success"><?= $editMode ? 'Update Catatan' : 'Simpan Catatan' ?></button>
                    <?php if ($editMode): ?>
                        <a href="catatan-admin.php" class="btn btn-secondary ms-2">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Tabel Catatan -->
        <h4>Riwayat Catatan Konseling</h4>
        <div class="table-responsive">
            <table id="datatable" class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Guru BK</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Catatan</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT c.*, u.nama AS nama_siswa, g.nama AS nama_guru 
                          FROM catatan c 
                          LEFT JOIN users u ON c.username_siswa = u.username 
                          LEFT JOIN guru_bk g ON c.guru = g.username 
                          ORDER BY c.tanggal DESC, c.jam DESC";
                $result = $conn->query($query);
                $no = 1;
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>" . htmlspecialchars($row['nama_siswa']) . "</td>
                            <td>" . htmlspecialchars($row['nama_guru']) . "</td>
                            <td>" . htmlspecialchars($row['tanggal']) . "</td>
                            <td>" . htmlspecialchars($row['jam']) . "</td>
                            <td>" . nl2br(htmlspecialchars($row['catatan'])) . "</td>
                            <td>" . htmlspecialchars($row['nilai']) . "</td>
                            <td>
                                <a href='?edit={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                                <a href='?hapus={$row['id']}' class='btn btn-danger btn-sm btn-hapus'><i class='fas fa-trash'></i></a>
                            </td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Belum ada catatan.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<?php if (isset($_SESSION['status'])): ?>
<script>
Swal.fire({
    icon: "<?= $_SESSION['status'][0] ?>",
    title: "<?= $_SESSION['status'][1] ?>",
    text: "<?= $_SESSION['status'][2] ?>",
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php unset($_SESSION['status']); endif; ?>

<script>
$(document).ready(function() {
    $('#datatable').DataTable();

    // Konfirmasi hapus
    $(".btn-hapus").on("click", function(e) {
        e.preventDefault();
        let link = $(this).attr("href");
        Swal.fire({
            title: "Hapus Catatan?",
            text: "Data yang sudah dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    });
});
</script>

</body>
</html>

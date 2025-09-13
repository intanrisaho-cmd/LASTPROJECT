<?php
session_start();
include 'config.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $nama     = trim($_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    if ($role !== 'user' && $role !== 'wali') {
        $error = "Role tidak valid.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username atau email sudah digunakan.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, nama, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $username, $email, $nama, $password, $role);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil. Silakan login.";
            } else {
                $error = "Registrasi gagal: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Konseling</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #4ecdc4, #556270);
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      width: 100%;
      max-width: 1100px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .left-panel {
      flex: 1;
      min-width: 300px;
      padding: 40px;
      background: rgba(255,255,255,0.15);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
    }

    .left-panel img {
      width: 180px;
      margin-bottom: 20px;
    }

    .left-panel h1 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .left-panel p {
      font-size: 15px;
      line-height: 1.6;
      max-width: 400px;
    }

    .right-panel {
      flex: 1;
      min-width: 300px;
      padding: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .register-card {
      width: 100%;
      max-width: 400px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease;
    }

    .register-card h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
      font-weight: 600;
    }

    .register-card input,
    .register-card select {
      width: 100%;
      padding: 12px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.3s;
    }

    .register-card input:focus,
    .register-card select:focus {
      border-color: #4ecdc4;
      box-shadow: 0 0 0 3px rgba(78,205,196,0.2);
      outline: none;
    }

    .register-card button {
      width: 100%;
      padding: 12px;
      background-color: #4ecdc4;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
    }

    .register-card button:hover {
      background-color: #38ada9;
    }

    .register-card p {
      text-align: center;
      font-size: 14px;
      margin-top: 20px;
    }

    .register-card a {
      color: #4ecdc4;
      text-decoration: none;
      font-weight: 600;
    }

    .register-card a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .container { flex-direction: column; border-radius: 0; }
      .left-panel, .right-panel { width: 100%; padding: 30px 20px; }
      .left-panel img { width: 120px; }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <img src="images/sma2.png" alt="Logo Sekolah">
      <h1>Sistem Konseling SMA Negeri 2 Buru</h1>
      <p>Konseling sekolah hadir sebagai ruang aman bagi siswa untuk berbagi, berdiskusi, dan mencari solusi.  
      Daftarkan dirimu untuk mulai terhubung dengan guru BK dan wali kelas.</p>
    </div>

    <div class="right-panel">
      <form method="post" class="register-card">
        <h2>Buat Akun Baru</h2>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email Aktif" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
          <option value="">-- Pilih Peran --</option>
          <option value="user">Siswa</option>
          <option value="wali">Wali Kelas</option>
        </select>

        <button type="submit">Daftar</button>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
      </form>
    </div>
  </div>

<?php if ($error): ?>
<script>
Swal.fire({
  icon: 'error',
  title: 'Registrasi Gagal',
  text: '<?= $error ?>',
  confirmButtonColor: '#4ecdc4'
});
</script>
<?php elseif ($success): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Registrasi Berhasil',
  text: '<?= $success ?>',
  confirmButtonColor: '#4ecdc4'
}).then(() => {
  window.location = 'login.php';
});
</script>
<?php endif; ?>

</body>
</html>

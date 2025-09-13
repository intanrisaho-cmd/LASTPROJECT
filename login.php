<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin': header("Location: admin_dashboard.php"); exit;
        case 'guru': header("Location: dashboard_guru.php"); exit;
        case 'wali': header("Location: wali_dashboard.php"); exit;
        case 'kepsek': header("Location: kepsek_dashboard.php"); exit;
        case 'user': header("Location: user_dashboard.php"); exit;
        default:
            session_destroy();
            header("Location: login.php"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Konseling | SMA Negeri 2 Buru</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #4ecdc4, #556270);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      padding: 40px;
      width: 100%;
      max-width: 400px;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      animation: fadeIn 0.8s ease;
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-header h2 {
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 0.5rem;
    }

    .login-header p {
      font-size: 1rem;
      color: #eee;
    }

    .login-container input {
      width: 100%;
      padding: 14px;
      margin: 12px 0;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      outline: none;
      background: rgba(255, 255, 255, 0.9);
      transition: 0.3s;
    }

    .login-container input:focus {
      box-shadow: 0 0 0 3px rgba(78,205,196,0.4);
    }

    .login-container button {
      width: 100%;
      padding: 14px;
      margin-top: 10px;
      border: none;
      border-radius: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      background: #4ecdc4;
      color: white;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-container button:hover {
      background: #38ada9;
    }

    .login-container a {
      display: block;
      text-align: center;
      margin-top: 1.2rem;
      font-size: 0.95rem;
      color: #f1f1f1;
      text-decoration: none;
    }

    .login-container a:hover {
      text-decoration: underline;
    }

    .error {
      background: rgba(231, 76, 60, 0.8);
      padding: 10px;
      border-radius: 8px;
      color: #fff;
      text-align: center;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h2>Login Konseling</h2>
      <p>SMA Negeri 2 Buru - Namlea</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="proses_login.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Masuk</button>
    </form>

    <a href="register.php">Belum punya akun? Daftar di sini</a>
  </div>
</body>
</html>

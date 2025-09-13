<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'guru':
                    header("Location: dashboard_guru.php");
                    break;
                case 'wali':
                    header("Location: wali_dashboard.php");
                    break;
                case 'kepsek':
                    header("Location: kepsek_dashboard.php");
                    break;
                case 'user':
                    header("Location: user_dashboard.php");
                    break;
                default:
                    $_SESSION['error'] = "Role tidak dikenali.";
                    header("Location: login.php");
                    break;
            }
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit;
    }
}
?>

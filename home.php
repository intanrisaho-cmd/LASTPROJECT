<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Konseling BK | SMA Negeri 2 Buru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html {
      scroll-behavior: smooth;
    }
    body {
      font-family: 'Poppins', sans-serif;
    }
    .navbar {
      transition: background 0.4s;
    }
    .navbar.scrolled {
      background: rgba(33, 37, 41, 0.95) !important;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .hero {
     background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url('images/f.jpg') center/cover no-repeat;
      height: 100vh;
      display: flex;
      align-items: center;
      text-align: center;
      color: white;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
      animation: fadeInDown 1.2s ease;
    }
    .hero p {
      font-size: 1.2rem;
      margin-top: 15px;
      animation: fadeInUp 1.5s ease;
    }
    .btn-hero, .btn-login {
      margin-top: 25px;
      padding: 12px 30px;
      border-radius: 30px;
      font-size: 1.1rem;
      font-weight: 600;
      background-color: #4ecdc4;
      border: none;
      color: white !important;
      transition: 0.3s ease;
      display: inline-block;
    }
    .btn-hero:hover, .btn-login:hover {
      background-color: #3db7ae;
      color: #fff !important;
    }
    .section {
      padding: 80px 0;
    }
    .section h2 {
      font-weight: 700;
      margin-bottom: 30px;
    }
    .card-custom {
      border: none;
      border-radius: 18px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-custom:hover {
      transform: translateY(-8px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    footer {
      background: #212529;
      color: #bbb;
      padding: 25px;
      text-align: center;
      font-size: 0.9rem;
    }
    @keyframes fadeInDown {
      from {opacity: 0; transform: translateY(-40px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(40px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-transparent">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="images/sma2.png" alt="Logo" width="40" height="40" class="me-2">
      BK SMA 2 Buru
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
          <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
          <li class="nav-item ms-lg-3"><a class="btn btn-login fw-semibold" href="login.php">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero" id="home">
    <div class="container">
      <h1>Selamat Datang di Bimbingan Konseling</h1>
      <p>SMA Negeri 2 Buru - Namlea</p>
      <a href="login.php" class="btn btn-hero">Mulai Konseling</a>
    </div>
  </section>

  <!-- Tentang -->
  <section id="tentang" class="section bg-light text-center">
    <div class="container">
      <h2 class="text-dark">Tentang Konseling BK</h2>
      <p class="lead text-muted mx-auto" style="max-width: 800px;">
        Bimbingan Konseling SMA Negeri 2 Buru hadir untuk mendukung siswa dalam mengembangkan potensi, 
        menghadapi tantangan akademik, sosial, maupun pribadi. Dengan pendekatan profesional, 
        kami siap mendampingi siswa menuju kesuksesan dan kesejahteraan.
      </p>
    </div>
  </section>

  <!-- Layanan -->
  <section id="layanan" class="section text-center">
    <div class="container">
      <h2 class="mb-5">Layanan Kami</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card card-custom p-4 h-100">
            <img src="https://img.icons8.com/ios-filled/100/000000/user-male-circle.png" class="mb-3 mx-auto" width="70">
            <h5 class="fw-bold">Konseling Individu</h5>
            <p>Mendukung siswa dalam menyelesaikan masalah pribadi, sosial, maupun akademik secara tatap muka.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-custom p-4 h-100">
            <img src="https://img.icons8.com/ios-filled/100/000000/conference.png" class="mb-3 mx-auto" width="70">
            <h5 class="fw-bold">Konseling Kelompok</h5>
            <p>Sesi kelompok yang membangun kerja sama, empati, dan solidaritas antar siswa di sekolah.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-custom p-4 h-100">
         
            <h5 class="fw-bold">Pengembangan Karir</h5>
            <p>Memberikan arahan dan bimbingan untuk perencanaan pendidikan dan karir masa depan siswa.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak -->
  <section id="kontak" class="section bg-light text-center">
    <div class="container">
      <h2 class="mb-4">Kontak Kami</h2>
      <p class="lead text-muted">SMA Negeri 2 Buru, Namlea</p>
      <p>Email: <a href="mailto:bk.sma2buru@gmail.com">bk.sma2buru@gmail.com</a> | Telp: (0913) 123456</p>
      <p>Hubungi BK</p>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Bimbingan Konseling SMA Negeri 2 Buru | Namlea</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>

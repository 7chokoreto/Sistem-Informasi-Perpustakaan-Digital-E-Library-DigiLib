<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DigiLib - Masuk ke Sistem Perpustakaan</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
  
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <style>
    :root {
      --bg-canvas: #f0f3f8;
      --bg-card: #f3f6fa;
      --primary: #1d4ed8;
      --primary-dark: #1e3a8a;
      --primary-glow: rgba(29, 78, 216, 0.35);
      --text-main: #1e293b;
      --text-muted: #64748b;
      --text-light: #94a3b8;
      
      --shadow-outer: 14px 14px 28px #d6dde8, -14px -14px 28px #ffffff;
      --shadow-card: 10px 10px 22px #d8e0eb, -10px -10px 22px #ffffff;
      --shadow-floating: 8px 8px 16px #d1d9e6, -8px -8px 16px #ffffff;
      --shadow-sm: 5px 5px 12px #d4dce8, -5px -5px 12px #ffffff;
      --shadow-inset: inset 4px 4px 8px #d2dae7, inset -4px -4px 8px #ffffff;
      --shadow-inset-focus: inset 4px 4px 10px #c7d2e2, inset -4px -4px 10px #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background-color: var(--bg-canvas);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
      position: relative;
      overflow-x: hidden;
      color: var(--text-main);
    }

    .floating-circle {
      position: absolute;
      border-radius: 50%;
      background: var(--bg-canvas);
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      pointer-events: none;
      z-index: 1;
    }
    .fc-1 { width: 52px; height: 52px; top: 8%; left: 8%; font-size: 20px; }
    .fc-2 { width: 44px; height: 44px; top: 18%; left: 42%; font-size: 16px; color: var(--primary); }
    .fc-3 { width: 60px; height: 60px; bottom: 8%; left: 6%; font-size: 24px; font-weight: 800; color: var(--primary); }
    .fc-4 { width: 56px; height: 56px; top: 12%; right: 10%; font-size: 20px; }
    .fc-5 { width: 42px; height: 42px; bottom: 12%; right: 28%; font-size: 16px; }
    .fc-6 { width: 68px; height: 68px; bottom: -20px; right: 20%; font-size: 24px; }

    .login-container {
      width: 100%;
      max-width: 1100px;
      background: var(--bg-canvas);
      border-radius: 40px;
      box-shadow: var(--shadow-outer);
      padding: 56px 64px;
      position: relative;
      z-index: 10;
      display: grid;
      grid-template-columns: 1.15fr 0.95fr;
      gap: 50px;
      align-items: center;
    }

    .hero-side {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      min-height: 480px;
    }

    .brand-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 40px;
    }

    .brand-badge-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      box-shadow: 0 4px 10px var(--primary-glow);
    }

    .brand-title {
      font-size: 17px;
      font-weight: 800;
      letter-spacing: -0.3px;
      color: var(--text-main);
    }

    .hero-main h1 {
      font-size: 44px;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.15;
      letter-spacing: -1px;
      margin-bottom: 20px;
    }

    .hero-main p {
      font-size: 14.5px;
      line-height: 1.6;
      color: var(--text-muted);
      max-width: 440px;
      margin-bottom: 32px;
    }

    .btn-hero-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 14px 34px;
      border-radius: 14px;
      background: var(--primary);
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-decoration: none;
      box-shadow: 0 8px 20px var(--primary-glow);
      transition: all 0.25s ease;
      width: fit-content;
      border: none;
      cursor: pointer;
    }

    .btn-hero-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 24px var(--primary-glow);
    }

    .stats-footer {
      display: flex;
      align-items: center;
      gap: 24px;
      padding-top: 32px;
      border-top: 1px solid rgba(166, 180, 200, 0.25);
    }

    .stat-pill {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-main);
    }
    .stat-pill i { color: var(--primary); }

    .form-card {
      background: var(--bg-card);
      border-radius: 28px;
      box-shadow: var(--shadow-card);
      padding: 42px 38px;
      text-align: center;
      position: relative;
    }

    .form-card h2 {
      font-size: 24px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 22px;
    }

    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }

    .input-box {
      width: 100%;
      height: 52px;
      border-radius: 14px;
      background: var(--bg-canvas);
      border: 1px solid rgba(255, 255, 255, 0.7);
      box-shadow: var(--shadow-inset);
      padding: 0 20px;
      font-size: 14px;
      color: var(--text-main);
      font-weight: 500;
      outline: none;
      transition: all 0.2s ease;
    }

    .input-box:focus {
      box-shadow: var(--shadow-inset-focus);
      border-color: rgba(29, 78, 216, 0.3);
    }

    .btn-submit {
      width: 100%;
      height: 52px;
      border-radius: 14px;
      background: var(--primary);
      border: none;
      color: #ffffff;
      font-size: 14.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      box-shadow: 0 8px 20px var(--primary-glow);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
      transition: all 0.25s ease;
    }

    .btn-submit:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 12px 24px var(--primary-glow);
    }

    .footer-help {
      margin-top: 24px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .footer-help a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 700;
    }

    .error-alert {
      background: rgba(220, 38, 38, 0.1);
      border: 1px solid rgba(220, 38, 38, 0.3);
      color: #dc2626;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 12.5px;
      font-weight: 600;
      margin-bottom: 16px;
      text-align: left;
    }

    @media (max-width: 900px) {
      .login-container {
        grid-template-columns: 1fr;
        padding: 40px 24px;
        gap: 36px;
      }
      .hero-side { min-height: auto; text-align: center; align-items: center; }
      .hero-main h1 { font-size: 32px; }
      .stats-footer { justify-content: center; }
    }
  </style>
</head>
<body>

  <!-- Floating Neumorphic Ambient Shapes -->
  <div class="floating-circle fc-1"><i class="fa-solid fa-bookmark"></i></div>
  <div class="floating-circle fc-2"></div>
  <div class="floating-circle fc-3">DL</div>
  <div class="floating-circle fc-4"><i class="fa-solid fa-bell"></i></div>
  <div class="floating-circle fc-5"></div>
  <div class="floating-circle fc-6"><i class="fa-solid fa-shapes"></i></div>

  <div class="login-container">
    
    <!-- Left Hero Side -->
    <div class="hero-side">
      <div>
        <div class="brand-header">
          <div class="brand-badge-icon">
            <i class="fa-solid fa-check"></i>
          </div>
          <span class="brand-title">DigiLib System</span>
        </div>

        <div class="hero-main">
          <h1>Kelola Perpustakaan Modern Lebih Mudah</h1>
          <p>
            Platform terpadu untuk administrasi perpustakaan umum. Pantau data buku, layani peminjaman anggota, dan kelola akun pengguna dengan tampilan yang rapi dan nyaman.
          </p>
          <button type="button" class="btn-hero-action" onclick="document.getElementById('emailInput').focus();">
            <span>Mulai Sekarang</span>
          </button>
        </div>
      </div>

      <div class="stats-footer">
        <div class="stat-pill">
          <i class="fa-solid fa-users"></i>
          <span>3.400+ Anggota Aktif</span>
        </div>
        <div class="stat-pill">
          <i class="fa-solid fa-book"></i>
          <span>24.000+ Koleksi Buku</span>
        </div>
      </div>
    </div>

    <!-- Right Neumorphic Form Card -->
    <div class="form-card" id="loginFormCard">
      <h2>Masuk ke Akun</h2>

      @if($errors->any())
        <div class="error-alert">
          <i class="fa-solid fa-triangle-exclamation"></i>
          {{ $errors->first() }}
        </div>
      @endif

      @if(session('success'))
        <div style="background: rgba(5, 150, 105, 0.1); border: 1px solid rgba(5, 150, 105, 0.3); color: #059669; padding: 10px 14px; border-radius: 12px; font-size: 12.5px; font-weight: 600; margin-bottom: 16px; text-align: left;">
          <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <input 
            type="email" 
            class="input-box" 
            id="emailInput" 
            name="email" 
            value="{{ old('email') }}" 
            placeholder="Alamat Email" 
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group" style="margin-bottom: 8px; position: relative;">
          <input 
            type="password" 
            class="input-box" 
            id="passwordInput" 
            name="password" 
            placeholder="Kata Sandi" 
            required
            autocomplete="current-password"
            style="padding-right: 48px;"
          >
          <i class="fa-solid fa-eye" onclick="togglePassword('passwordInput', this)" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 16px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"></i>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
          <a href="{{ route('password.request') }}" style="color: var(--primary); font-size: 13px; font-weight: 700; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
            Lupa Kata Sandi?
          </a>
        </div>

        <button type="submit" class="btn-submit">
          <span>Masuk</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div class="footer-help" style="margin-top: 20px;">
        Belum memiliki akun anggota? <a href="{{ route('register') }}">Daftar Akun Baru</a>
      </div>
    </div>

  </div>

  <script>
    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  </script>
</body>
</html>

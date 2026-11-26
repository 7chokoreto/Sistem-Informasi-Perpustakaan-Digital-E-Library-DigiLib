<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DigiLib - Pendaftaran Anggota Baru</title>
  
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

    .register-container {
      width: 100%;
      max-width: 1050px;
      background: var(--bg-canvas);
      border-radius: 40px;
      box-shadow: var(--shadow-outer);
      padding: 50px 60px;
      position: relative;
      z-index: 10;
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 48px;
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
      margin-bottom: 30px;
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
      font-size: 40px;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.15;
      letter-spacing: -1px;
      margin-bottom: 18px;
    }

    .hero-main p {
      font-size: 14px;
      line-height: 1.6;
      color: var(--text-muted);
      max-width: 440px;
      margin-bottom: 28px;
    }

    .feature-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 10px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 13.5px;
      color: var(--text-main);
      font-weight: 600;
    }

    .feature-item i {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: rgba(37, 99, 235, 0.1);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
    }

    .form-card {
      background: var(--bg-card);
      border-radius: 28px;
      box-shadow: var(--shadow-card);
      padding: 38px 34px;
      text-align: center;
      position: relative;
    }

    .form-card h2 {
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 6px;
    }

    .form-card p.subtitle {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 22px;
    }

    .form-group {
      margin-bottom: 14px;
      text-align: left;
    }

    .input-box {
      width: 100%;
      height: 48px;
      border-radius: 12px;
      background: var(--bg-canvas);
      border: 1px solid rgba(255, 255, 255, 0.7);
      box-shadow: var(--shadow-inset);
      padding: 0 16px;
      font-size: 13.5px;
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
      height: 50px;
      border-radius: 12px;
      background: var(--primary);
      border: none;
      color: #ffffff;
      font-size: 14px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      box-shadow: 0 8px 20px var(--primary-glow);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 14px;
      transition: all 0.25s ease;
    }

    .btn-submit:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .footer-help {
      margin-top: 20px;
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
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 16px;
      text-align: left;
    }

    @media (max-width: 900px) {
      .register-container {
        grid-template-columns: 1fr;
        padding: 36px 20px;
        gap: 32px;
      }
      .hero-side { min-height: auto; text-align: center; align-items: center; }
      .hero-main h1 { font-size: 30px; }
    }
  </style>
</head>
<body>

  <div class="register-container">
    
    <!-- Left Hero Side -->
    <div class="hero-side">
      <div>
        <div class="brand-header">
          <div class="brand-badge-icon">
            <i class="fa-solid fa-book-open"></i>
          </div>
          <span class="brand-title">DigiLib System</span>
        </div>

        <div class="hero-main">
          <h1>Bergabung Sebagai Anggota Perpustakaan</h1>
          <p>
            Daftarkan diri Anda untuk menikmati akses peminjaman ribuan koleksi buku fisik, reservasi peminjaman online dari mana saja, dan riwayat bacaan mandiri.
          </p>

          <div class="feature-list">
            <div class="feature-item">
              <i class="fa-solid fa-check"></i>
              <span>Pinjam buku dari mana saja</span>
            </div>
            <div class="feature-item">
              <i class="fa-solid fa-check"></i>
              <span>Pantau tanggal jatuh tempo dan pengembalian</span>
            </div>
            <div class="feature-item">
              <i class="fa-solid fa-check"></i>
              <span>Pinjam buku jadi mudah, cepat, dan nyaman</span>
            </div>
          </div>
        </div>
      </div>

      <div style="font-size: 12px; color: var(--text-muted); padding-top: 24px;">
        &copy; {{ date('Y') }} DigiLib - Perpustakaan Umum Terpadu.
      </div>
    </div>

    <!-- Right Form Card -->
    <div class="form-card">
      <h2>Daftar Anggota Baru</h2>
      <p class="subtitle">Lengkapi formulir di bawah ini untuk membuat akun</p>

      @if($errors->any())
        <div class="error-alert">
          <i class="fa-solid fa-triangle-exclamation"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <input 
            type="text" 
            class="input-box" 
            name="name" 
            value="{{ old('name') }}" 
            placeholder="Nama Lengkap" 
            required
            autocomplete="name"
          >
        </div>

        <div class="form-group">
          <input 
            type="tel" 
            class="input-box" 
            name="phone" 
            value="{{ old('phone') }}" 
            placeholder="Nomor HP" 
            required
            autocomplete="tel"
          >
        </div>

        <div class="form-group">
          <input 
            type="email" 
            class="input-box" 
            name="email" 
            value="{{ old('email') }}" 
            placeholder="Alamat Email Aktif" 
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group" style="position: relative;">
          <input 
            type="password" 
            class="input-box" 
            id="registerPassword"
            name="password" 
            placeholder="Kata Sandi (Minimal 6 karakter)" 
            required
            autocomplete="new-password"
            style="padding-right: 48px;"
          >
          <i class="fa-solid fa-eye" onclick="togglePassword('registerPassword', this)" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 16px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"></i>
        </div>

        <div class="form-group" style="position: relative;">
          <input 
            type="password" 
            class="input-box" 
            id="registerPasswordConfirm"
            name="password_confirmation" 
            placeholder="Ulangi Kata Sandi" 
            required
            autocomplete="new-password"
            style="padding-right: 48px;"
          >
          <i class="fa-solid fa-eye" onclick="togglePassword('registerPasswordConfirm', this)" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 16px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"></i>
        </div>

        <button type="submit" class="btn-submit">
          <span>Daftar Sekarang</span>
          <i class="fa-solid fa-user-plus"></i>
        </button>
      </form>

      <div class="footer-help">
        Sudah memiliki akun? <a href="{{ route('login') }}">Masuk ke Akun</a>
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

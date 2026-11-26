<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DigiLib - Buat Kata Sandi Baru</title>
  
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
    .fc-1 { width: 52px; height: 52px; top: 10%; left: 8%; font-size: 20px; }
    .fc-2 { width: 44px; height: 44px; top: 15%; right: 12%; font-size: 16px; color: var(--primary); }
    .fc-3 { width: 60px; height: 60px; bottom: 10%; left: 10%; font-size: 24px; color: var(--primary); }
    .fc-4 { width: 50px; height: 50px; bottom: 12%; right: 10%; font-size: 18px; }

    .card-container {
      width: 100%;
      max-width: 480px;
      background: var(--bg-canvas);
      border-radius: 36px;
      box-shadow: var(--shadow-outer);
      padding: 44px 38px;
      position: relative;
      z-index: 10;
      text-align: center;
    }

    .brand-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .brand-badge-icon {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      box-shadow: 0 4px 10px var(--primary-glow);
    }

    .brand-title {
      font-size: 18px;
      font-weight: 800;
      letter-spacing: -0.3px;
      color: var(--text-main);
    }

    .icon-wrapper {
      width: 68px;
      height: 68px;
      border-radius: 20px;
      background: rgba(5, 150, 105, 0.1);
      color: #059669;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin: 0 auto 18px auto;
      box-shadow: var(--shadow-sm);
    }

    h1 {
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 8px;
    }

    .description {
      font-size: 13.5px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 22px;
    }

    .form-group {
      margin-bottom: 18px;
      text-align: left;
      position: relative;
    }

    .form-label {
      display: block;
      font-size: 12.5px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 8px;
      margin-left: 4px;
    }

    .input-box {
      width: 100%;
      height: 52px;
      border-radius: 14px;
      background: var(--bg-canvas);
      border: 1px solid rgba(255, 255, 255, 0.7);
      box-shadow: var(--shadow-inset);
      padding: 0 48px 0 18px;
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

    .toggle-pwd {
      position: absolute;
      right: 16px;
      bottom: 16px;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 16px;
      transition: color 0.2s ease;
    }

    .toggle-pwd:hover {
      color: var(--primary);
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
      transition: all 0.25s ease;
      margin-top: 14px;
    }

    .btn-submit:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 12px 24px var(--primary-glow);
    }

    .error-alert {
      background: rgba(220, 38, 38, 0.1);
      border: 1px solid rgba(220, 38, 38, 0.25);
      color: #dc2626;
      padding: 12px 16px;
      border-radius: 14px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .success-alert {
      background: rgba(5, 150, 105, 0.1);
      border: 1px solid rgba(5, 150, 105, 0.25);
      color: #059669;
      padding: 12px 16px;
      border-radius: 14px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .req-list {
      background: rgba(241, 245, 249, 0.6);
      border: 1px solid rgba(166, 180, 200, 0.25);
      border-radius: 12px;
      padding: 10px 14px;
      margin-bottom: 16px;
      font-size: 12px;
      color: var(--text-muted);
      text-align: left;
      line-height: 1.6;
    }

    .req-list i {
      color: #059669;
      margin-right: 6px;
    }
  </style>
</head>
<body>

  <!-- Floating Neumorphic Shapes -->
  <div class="floating-circle fc-1"><i class="fa-solid fa-key"></i></div>
  <div class="floating-circle fc-2"><i class="fa-solid fa-shield-halved"></i></div>
  <div class="floating-circle fc-3"><i class="fa-solid fa-lock"></i></div>
  <div class="floating-circle fc-4"><i class="fa-solid fa-check"></i></div>

  <div class="card-container">
    
    <!-- Brand -->
    <div class="brand-header">
      <div class="brand-badge-icon">
        <i class="fa-solid fa-book-bookmark"></i>
      </div>
      <span class="brand-title">DigiLib</span>
    </div>

    <!-- Icon & Header -->
    <div class="icon-wrapper">
      <i class="fa-solid fa-key"></i>
    </div>

    <h1>Buat Kata Sandi Baru</h1>
    <p class="description">
      Autentikasi OTP berhasil. Silakan tentukan kata sandi baru untuk akun Anda <strong>({{ $email }})</strong>.
    </p>

    @if(session('success'))
      <div class="success-alert">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div class="error-alert">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="password" class="form-label">Kata Sandi Baru</label>
        <input 
          type="password" 
          name="password" 
          id="password" 
          class="input-box" 
          placeholder="Minimal 6 karakter" 
          required 
          autofocus
        >
        <i class="fa-solid fa-eye toggle-pwd" onclick="togglePassword('password', this)"></i>
      </div>

      <div class="form-group">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
        <input 
          type="password" 
          name="password_confirmation" 
          id="password_confirmation" 
          class="input-box" 
          placeholder="Ulangi kata sandi baru" 
          required
        >
        <i class="fa-solid fa-eye toggle-pwd" onclick="togglePassword('password_confirmation', this)"></i>
      </div>

      <div class="req-list">
        <div><i class="fa-solid fa-circle-check"></i> Panjang kata sandi minimal 6 karakter.</div>
        <div><i class="fa-solid fa-circle-check"></i> Pastikan konfirmasi kata sandi sama persis.</div>
      </div>

      <button type="submit" class="btn-submit">
        <span>Simpan Kata Sandi Baru</span>
        <i class="fa-solid fa-check"></i>
      </button>
    </form>

  </div>

  <script>
    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
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

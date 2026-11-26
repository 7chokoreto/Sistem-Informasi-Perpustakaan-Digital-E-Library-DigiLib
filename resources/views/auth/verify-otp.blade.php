<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DigiLib - Verifikasi Kode OTP</title>
  
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
      max-width: 500px;
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
      background: rgba(29, 78, 216, 0.1);
      color: var(--primary);
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
      margin-bottom: 12px;
    }

    .email-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(29, 78, 216, 0.08);
      border: 1px solid rgba(29, 78, 216, 0.2);
      color: var(--primary);
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 24px;
    }

    .otp-input-container {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 22px;
    }

    .otp-box {
      width: 48px;
      height: 56px;
      border-radius: 14px;
      background: var(--bg-canvas);
      border: 1px solid rgba(255, 255, 255, 0.7);
      box-shadow: var(--shadow-inset);
      text-align: center;
      font-size: 22px;
      font-weight: 800;
      color: var(--primary);
      outline: none;
      transition: all 0.2s ease;
    }

    .otp-box:focus {
      box-shadow: var(--shadow-inset-focus);
      border-color: rgba(29, 78, 216, 0.4);
      transform: translateY(-2px);
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
      margin-top: 10px;
    }

    .btn-submit:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 12px 24px var(--primary-glow);
    }

    .timer-section {
      margin-top: 22px;
      font-size: 13px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .timer-value {
      font-weight: 800;
      color: #dc2626;
      font-family: monospace;
      font-size: 14.5px;
    }

    .resend-container {
      margin-top: 14px;
    }

    .btn-resend {
      background: none;
      border: none;
      color: var(--primary);
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: underline;
      transition: opacity 0.2s;
    }

    .btn-resend:hover {
      opacity: 0.8;
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

    .back-link {
      margin-top: 22px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.2s ease;
    }

    .back-link:hover {
      color: var(--primary);
    }
  </style>
</head>
<body>

  <!-- Floating Neumorphic Shapes -->
  <div class="floating-circle fc-1"><i class="fa-solid fa-key"></i></div>
  <div class="floating-circle fc-2"><i class="fa-solid fa-envelope"></i></div>
  <div class="floating-circle fc-3"><i class="fa-solid fa-shield-halved"></i></div>
  <div class="floating-circle fc-4"><i class="fa-solid fa-clock"></i></div>

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

    <h1>Verifikasi Kode OTP</h1>
    <p class="description">
      Silakan masukkan 6 digit kode keamanan yang telah dikirim ke email:
    </p>

    <div class="email-pill">
      <i class="fa-solid fa-envelope"></i>
      <span>{{ $email }}</span>
    </div>

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

    <form action="{{ route('password.otp.verify') }}" method="POST" id="otpForm">
      @csrf

      <!-- 6 Individual OTP Boxes -->
      <div class="otp-input-container">
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*" autofocus>
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*">
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*">
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*">
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*">
        <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]*">
      </div>

      <!-- Real OTP Hidden Input Sent to Backend -->
      <input type="hidden" name="otp" id="realOtpInput" value="">

      <button type="submit" class="btn-submit" id="btnSubmitOtp">
        <span>Verifikasi &amp; Lanjutkan</span>
        <i class="fa-solid fa-arrow-right"></i>
      </button>
    </form>

    <!-- Timer Countdown -->
    <div class="timer-section">
      <i class="fa-regular fa-clock"></i>
      <span>Masa berlaku kode: </span>
      <span class="timer-value" id="countdownTimer">15:00</span>
    </div>

    <!-- Resend OTP Form -->
    <div class="resend-container">
      <form action="{{ route('password.otp.resend') }}" method="POST">
        @csrf
        <span style="font-size: 13px; color: var(--text-muted);">Tidak menerima kode? </span>
        <button type="submit" class="btn-resend">
          <i class="fa-solid fa-rotate-right"></i>
          <span>Kirim Ulang OTP</span>
        </button>
      </form>
    </div>

    <div>
      <a href="{{ route('password.request') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Ganti Alamat Email</span>
      </a>
    </div>

  </div>

  <script>
    // OTP Inputs Auto-advance & Paste Handler
    const otpBoxes = document.querySelectorAll('.otp-box');
    const realOtpInput = document.getElementById('realOtpInput');
    const otpForm = document.getElementById('otpForm');

    function syncOtp() {
      let combined = '';
      otpBoxes.forEach(box => {
        combined += box.value;
      });
      realOtpInput.value = combined;
    }

    otpBoxes.forEach((box, index) => {
      box.addEventListener('input', (e) => {
        const val = e.target.value.replace(/[^0-9]/g, '');
        box.value = val;

        if (val.length === 1 && index < otpBoxes.length - 1) {
          otpBoxes[index + 1].focus();
        }
        syncOtp();
      });

      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !box.value && index > 0) {
          otpBoxes[index - 1].focus();
        }
      });

      // Handle Paste
      box.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
        if (!pastedData) return;

        for (let i = 0; i < otpBoxes.length; i++) {
          if (i < pastedData.length) {
            otpBoxes[i].value = pastedData[i];
          }
        }
        const nextIndex = Math.min(pastedData.length, otpBoxes.length - 1);
        otpBoxes[nextIndex].focus();
        syncOtp();
      });
    });

    otpForm.addEventListener('submit', (e) => {
      syncOtp();
      if (realOtpInput.value.length < 6) {
        e.preventDefault();
        alert('Harap masukkan 6 digit kode OTP secara lengkap.');
      }
    });

    // 15-Minute Countdown Timer
    let totalSeconds = 15 * 60;
    const timerElem = document.getElementById('countdownTimer');

    const countdownInterval = setInterval(() => {
      totalSeconds--;
      if (totalSeconds <= 0) {
        clearInterval(countdownInterval);
        timerElem.innerText = 'Kedaluwarsa';
        timerElem.style.color = '#ef4444';
        return;
      }

      const minutes = Math.floor(totalSeconds / 60);
      const seconds = totalSeconds % 60;
      timerElem.innerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }, 1000);
  </script>

</body>
</html>

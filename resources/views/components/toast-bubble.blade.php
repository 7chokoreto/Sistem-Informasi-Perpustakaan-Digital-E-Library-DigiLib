@if(session('success'))
<div class="toast-bubble-container" id="toastBubbleSuccess" role="alert">
  <div class="toast-bubble success">
    <!-- Icon Centang Hijau -->
    <div class="toast-bubble-icon success">
      <i class="fa-solid fa-check"></i>
    </div>
    
    <!-- Teks Notifikasi -->
    <div class="toast-bubble-content">
      <div class="toast-bubble-title success">Berhasil Disimpan</div>
      <div class="toast-bubble-msg">{{ session('success') }}</div>
    </div>

    <!-- Tombol Tutup Manual -->
    <button type="button" class="toast-bubble-close" onclick="closeToastBubble('toastBubbleSuccess')" title="Tutup">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- Bar Progress Countdown 5 Detik -->
    <div class="toast-bubble-progress-track">
      <div class="toast-bubble-progress-bar success"></div>
    </div>
  </div>
</div>
@endif

@if(session('error'))
<div class="toast-bubble-container" id="toastBubbleError" role="alert">
  <div class="toast-bubble error">
    <div class="toast-bubble-icon error">
      <i class="fa-solid fa-xmark"></i>
    </div>
    
    <div class="toast-bubble-content">
      <div class="toast-bubble-title error">Perhatian</div>
      <div class="toast-bubble-msg">{{ session('error') }}</div>
    </div>

    <button type="button" class="toast-bubble-close" onclick="closeToastBubble('toastBubbleError')" title="Tutup">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="toast-bubble-progress-track">
      <div class="toast-bubble-progress-bar error"></div>
    </div>
  </div>
</div>
@endif

<style>
/* ================= TOP-LEFT BUBBLE NOTIFICATION ================= */
.toast-bubble-container {
  position: fixed;
  top: 24px;
  left: 24px;
  z-index: 999999;
  pointer-events: none;
}

.toast-bubble {
  pointer-events: auto;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 18px 18px 16px;
  min-width: 320px;
  max-width: 420px;
  border-radius: 18px;
  background: #ffffff;
  box-shadow: 0 12px 32px rgba(5, 150, 105, 0.22), 0 4px 12px rgba(0, 0, 0, 0.06);
  border: 1.5px solid rgba(16, 185, 129, 0.35);
  animation: toastBubbleSlideInLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transition: transform 0.4s ease, opacity 0.4s ease;
}

.toast-bubble.error {
  border-color: rgba(239, 68, 68, 0.35);
  box-shadow: 0 12px 32px rgba(239, 68, 68, 0.22), 0 4px 12px rgba(0, 0, 0, 0.06);
}

.toast-bubble.hide {
  animation: toastBubbleSlideOutLeft 0.4s ease forwards !important;
}

.toast-bubble-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.toast-bubble-icon.success {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.4);
}

.toast-bubble-icon.error {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.4);
}

.toast-bubble-content {
  flex-grow: 1;
  padding-right: 6px;
}

.toast-bubble-title {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-bottom: 2px;
}

.toast-bubble-title.success {
  color: #059669;
}

.toast-bubble-title.error {
  color: #dc2626;
}

.toast-bubble-msg {
  font-size: 13.5px;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.35;
}

.toast-bubble-close {
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 15px;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.toast-bubble-close:hover {
  color: #475569;
  background: rgba(0, 0, 0, 0.05);
}

.toast-bubble-progress-track {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: rgba(16, 185, 129, 0.15);
}

.toast-bubble.error .toast-bubble-progress-track {
  background: rgba(239, 68, 68, 0.15);
}

.toast-bubble-progress-bar {
  height: 100%;
  width: 100%;
  animation: toastBubbleCountdown 5s linear forwards;
}

.toast-bubble-progress-bar.success {
  background: linear-gradient(90deg, #10b981, #059669);
}

.toast-bubble-progress-bar.error {
  background: linear-gradient(90deg, #ef4444, #dc2626);
}

/* Animasi Muncul dari Kiri ke Kanan */
@keyframes toastBubbleSlideInLeft {
  0% {
    transform: translateX(-120%);
    opacity: 0;
  }
  70% {
    transform: translateX(12px);
    opacity: 1;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Animasi Menghilang Kembali ke Kiri */
@keyframes toastBubbleSlideOutLeft {
  0% {
    transform: translateX(0);
    opacity: 1;
  }
  100% {
    transform: translateX(-120%);
    opacity: 0;
  }
}

/* Animasi Progress Bar 5 Detik */
@keyframes toastBubbleCountdown {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}
</style>

<script>
function closeToastBubble(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const bubble = container.querySelector('.toast-bubble');
  if (bubble) {
    bubble.classList.add('hide');
    setTimeout(() => container.remove(), 400);
  } else {
    container.remove();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  ['toastBubbleSuccess', 'toastBubbleError'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      setTimeout(() => {
        closeToastBubble(id);
      }, 5000);
    }
  });
});
</script>

/**
 * DigiLib - Member Common Script
 * Handles Active User Session (localStorage), Suspension Status, Topbar Profile, and Alert Banners
 */

// Default Empty Avatar Placeholder SVG
const DEFAULT_EMPTY_AVATAR = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 120' fill='%2394a3b8'><rect width='100%25' height='100%25' fill='%23e2e8f0'/><path d='M60 62c11.046 0 20-8.954 20-20s-8.954-20-20-20-20 8.954-20 20 8.954 20 20 20zm0 10c-15.56 0-40 7.82-40 23.44V102h80v-6.56C100 79.82 75.56 72 60 72z'/></svg>";

// Helper to get current user session
function getDigiLibCurrentUser() {
  const defaultUser = {
    id: 3,
    name: 'Ahmad Fauzi',
    email: 'ahmad@digilib.id',
    role: 'member',
    roleLabel: 'Anggota Umum',
    status: 'active', // 'active' | 'suspended'
    suspensionReason: '',
    joinedDate: '15 Agu 2024',
    phone: '0857-1122-3344',
    avatar: '' // avatar kosong jika belum upload
  };

  try {
    const raw = localStorage.getItem('digilib_current_user');
    if (raw) {
      const user = JSON.parse(raw);
      // Sinkronkan dengan data akun terbaru di digilib_accounts jika ada
      const accountsRaw = localStorage.getItem('digilib_accounts');
      if (accountsRaw) {
        const accounts = JSON.parse(accountsRaw);
        const latest = accounts.find(a => 
          (user.id && a.id === user.id) || 
          (a.email && user.email && a.email.toLowerCase() === user.email.toLowerCase())
        );
        if (latest) {
          const merged = { ...user, ...latest };
          localStorage.setItem('digilib_current_user', JSON.stringify(merged));
          return merged;
        }
      }
      return user;
    }
  } catch (e) {
    console.error('Failed to load current user', e);
  }

  // Jika belum ada di localStorage, simpan default
  try {
    localStorage.setItem('digilib_current_user', JSON.stringify(defaultUser));
  } catch (e) {}
  return defaultUser;
}

// Update Topbar Profile Display
function syncMemberTopbar() {
  const user = getDigiLibCurrentUser();
  
  const avatarEls = document.querySelectorAll('.member-topbar-avatar');
  avatarEls.forEach(el => {
    el.src = user.avatar && user.avatar.trim() !== '' ? user.avatar : DEFAULT_EMPTY_AVATAR;
  });

  const nameEls = document.querySelectorAll('.member-topbar-name');
  nameEls.forEach(el => {
    el.textContent = user.name;
  });

  const statusBadgeEls = document.querySelectorAll('.member-status-pill');
  statusBadgeEls.forEach(el => {
    if (user.status === 'suspended') {
      el.className = 'member-status-pill suspended';
      el.innerHTML = '<i class="fa-solid fa-ban"></i> Ditangguhkan';
    } else {
      el.className = 'member-status-pill active';
      el.innerHTML = '<i class="fa-solid fa-circle-check"></i> Aktif';
    }
  });

  // Tampilkan Suspension Banner jika ada di halaman
  const suspensionBanner = document.getElementById('suspensionAlertBanner');
  if (suspensionBanner) {
    if (user.status === 'suspended') {
      suspensionBanner.style.display = 'flex';
      const reasonEl = document.getElementById('suspensionBannerReason');
      if (reasonEl) {
        reasonEl.textContent = user.suspensionReason || 'Pelanggaran batas waktu pengembalian buku dan tata tertib umum perpustakaan.';
      }
    } else {
      suspensionBanner.style.display = 'none';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  syncMemberTopbar();
});

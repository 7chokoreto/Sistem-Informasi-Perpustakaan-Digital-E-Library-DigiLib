/**
 * DigiLib - Member Account Settings & Synchronization Script
 * Self-service Profile & Password Update, Status & Suspension Reason Display,
 * Local File Image Upload with Empty Avatar Fallback, and Bidirectional LocalStorage Sync with Superadmin
 */

document.addEventListener('DOMContentLoaded', () => {
  const STORAGE_ACCOUNTS = 'digilib_accounts';
  const STORAGE_CURRENT_USER = 'digilib_current_user';

  // Fallback Empty Avatar
  const DEFAULT_EMPTY = typeof DEFAULT_EMPTY_AVATAR !== 'undefined' 
    ? DEFAULT_EMPTY_AVATAR 
    : "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 120' fill='%2394a3b8'><rect width='100%25' height='100%25' fill='%23e2e8f0'/><path d='M60 62c11.046 0 20-8.954 20-20s-8.954-20-20-20-20 8.954-20 20 8.954 20 20 20zm0 10c-15.56 0-40 7.82-40 23.44V102h80v-6.56C100 79.82 75.56 72 60 72z'/></svg>";

  // DOM Elements
  const accountStatusContainer = document.getElementById('accountStatusContainer');
  const formEditProfile = document.getElementById('formEditProfile');
  const formChangePassword = document.getElementById('formChangePassword');

  const avatarPreviewCurrent = document.getElementById('avatarPreviewCurrent');
  const photoFileInput = document.getElementById('photoFileInput');
  const btnTriggerUploadPhoto = document.getElementById('btnTriggerUploadPhoto');
  const btnRemovePhoto = document.getElementById('btnRemovePhoto');

  const profileNameInput = document.getElementById('profileName');
  const profileEmailInput = document.getElementById('profileEmail');
  const profilePhoneInput = document.getElementById('profilePhone');

  const pwdCurrentInput = document.getElementById('pwdCurrent');
  const pwdNewInput = document.getElementById('pwdNew');
  const pwdConfirmInput = document.getElementById('pwdConfirm');

  let currentAvatarData = '';

  // Toast Function
  const showToast = (message, type = 'success') => {
    let toast = document.getElementById('memberToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'memberToast';
      toast.className = 'toast-notification';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-exclamation'}"></i> <span>${message}</span>`;
    toast.className = `toast-notification show ${type}`;
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  };

  // Helper sync with digilib_accounts in localStorage
  const syncWithSuperadminAccounts = (updatedUser, oldEmail) => {
    try {
      const rawAccounts = localStorage.getItem(STORAGE_ACCOUNTS);
      if (rawAccounts) {
        let accounts = JSON.parse(rawAccounts);
        const idx = accounts.findIndex(a => 
          (updatedUser.id && a.id === updatedUser.id) ||
          (oldEmail && a.email && a.email.toLowerCase() === oldEmail.toLowerCase()) ||
          (a.email && updatedUser.email && a.email.toLowerCase() === updatedUser.email.toLowerCase())
        );

        if (idx !== -1) {
          accounts[idx].name = updatedUser.name;
          accounts[idx].email = updatedUser.email;
          accounts[idx].phone = updatedUser.phone;
          accounts[idx].avatar = updatedUser.avatar;
          if (updatedUser.password) {
            accounts[idx].password = updatedUser.password;
          }
          localStorage.setItem(STORAGE_ACCOUNTS, JSON.stringify(accounts));
        }
      }
    } catch (e) {
      console.error('Failed to sync with accounts list', e);
    }
  };

  // Render Status Akun Card (Aktif vs Ditangguhkan)
  const renderStatusCard = (user) => {
    if (!accountStatusContainer) return;

    if (user.status === 'suspended') {
      const reason = user.suspensionReason || 'Pelanggaran ketentuan masa pengembalian buku dan tata tertib umum perpustakaan.';
      accountStatusContainer.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 18px; flex-wrap: wrap;">
          <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(220, 38, 38, 0.12); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-ban"></i>
          </div>
          <div style="flex-grow: 1;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
              <h2 style="font-size: 19px; font-weight: 800; color: #dc2626;">Status Keanggotaan: Ditangguhkan</h2>
              <span class="member-status-pill suspended" style="font-size: 11px;">
                <i class="fa-solid fa-lock"></i> Dibekukan
              </span>
            </div>
            <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;">
              Akun Anda saat ini ditangguhkan oleh <b>Superadmin</b>. Anda tetap dapat memperbarui profil dan melihat riwayat, namun akses peminjaman buku dibatasi.
            </p>

            <div style="background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: var(--radius-md); padding: 14px 18px; margin-top: 14px;">
              <div style="font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                <i class="fa-solid fa-circle-exclamation"></i> Alasan Penangguhan dari Superadmin:
              </div>
              <div style="font-size: 14px; font-weight: 700; color: var(--text-main); line-height: 1.5;">
                "${reason}"
              </div>
              <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                Untuk mengaktifkan kembali akun Anda, silakan selesaikan pengembalian buku di perpustakaan atau hubungi bagian administrasi.
              </div>
            </div>
          </div>
        </div>
      `;
    } else {
      accountStatusContainer.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(5, 150, 105, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 19px; font-weight: 800; color: #059669;">Status Keanggotaan: Aktif</h2>
                <span class="member-status-pill active" style="font-size: 11px;">
                  <i class="fa-solid fa-shield-check"></i> Normal
                </span>
              </div>
              <p style="font-size: 13.5px; color: var(--text-muted);">
                Akun Anda dalam kondisi prima. Anda berhak meminjam hingga 3 buku secara bersamaan.
              </p>
            </div>
          </div>
          <div style="font-size: 13px; color: var(--text-muted);">
            Terdaftar Sejak: <b style="color: var(--text-main);">${user.joinedDate || '15 Agu 2024'}</b>
          </div>
        </div>
      `;
    }
  };

  // Inisialisasi Data Profil
  const initProfileData = () => {
    const user = getDigiLibCurrentUser();

    // Render Status
    renderStatusCard(user);

    // Profile Form Fields
    if (profileNameInput) profileNameInput.value = user.name || '';
    if (profileEmailInput) profileEmailInput.value = user.email || '';
    if (profilePhoneInput) profilePhoneInput.value = user.phone || '';

    // Avatar
    currentAvatarData = user.avatar || '';
    if (avatarPreviewCurrent) {
      avatarPreviewCurrent.src = currentAvatarData.trim() !== '' ? currentAvatarData : DEFAULT_EMPTY;
    }
  };

  // Trigger Upload Button Click
  if (btnTriggerUploadPhoto && photoFileInput) {
    btnTriggerUploadPhoto.addEventListener('click', () => {
      photoFileInput.click();
    });
  }

  // Handle File Input Change (Upload Gambar Baru)
  if (photoFileInput) {
    photoFileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Validasi ukuran file (maksimal 2.5MB)
      if (file.size > 2.5 * 1024 * 1024) {
        showToast('Ukuran foto terlalu besar. Maksimal ukuran foto adalah 2.5 MB.', 'error');
        photoFileInput.value = '';
        return;
      }

      // Validasi tipe file
      if (!file.type.startsWith('image/')) {
        showToast('Format file tidak didukung. Harap pilih file gambar (JPG, PNG, WEBP).', 'error');
        photoFileInput.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        currentAvatarData = event.target.result;
        if (avatarPreviewCurrent) {
          avatarPreviewCurrent.src = currentAvatarData;
        }
        showToast('Foto profil berhasil dimuat. Klik "Simpan Perubahan Profil" untuk menyimpan.', 'success');
      };
      reader.readAsDataURL(file);
    });
  }

  // Handle Remove Photo (Kembali ke Avatar Kosong)
  if (btnRemovePhoto) {
    btnRemovePhoto.addEventListener('click', () => {
      currentAvatarData = '';
      if (avatarPreviewCurrent) {
        avatarPreviewCurrent.src = DEFAULT_EMPTY;
      }
      if (photoFileInput) {
        photoFileInput.value = '';
      }
      showToast('Foto profil dihapus (menggunakan avatar kosong). Klik Simpan untuk memperbarui.', 'success');
    });
  }

  // Form Edit Profile Submit
  if (formEditProfile) {
    formEditProfile.addEventListener('submit', (e) => {
      e.preventDefault();

      const currentUser = getDigiLibCurrentUser();
      const oldEmail = currentUser.email;

      const newName = profileNameInput.value.trim();
      const newEmail = profileEmailInput.value.trim();
      const newPhone = profilePhoneInput.value.trim();

      if (!newName || !newEmail || !newPhone) {
        showToast('Mohon lengkapi seluruh field profil.', 'error');
        return;
      }

      // Update current user
      currentUser.name = newName;
      currentUser.email = newEmail;
      currentUser.phone = newPhone;
      currentUser.avatar = currentAvatarData;

      // Simpan ke local storage sesi
      localStorage.setItem(STORAGE_CURRENT_USER, JSON.stringify(currentUser));

      // Sinkronkan ke daftar akun Superadmin
      syncWithSuperadminAccounts(currentUser, oldEmail);

      // Sinkronkan tampilan topbar seketika
      if (typeof syncMemberTopbar === 'function') {
        syncMemberTopbar();
      }

      showToast('Profil berhasil disimpan dan tersinkronisasi ke sistem!', 'success');
    });
  }

  // Form Change Password Submit
  if (formChangePassword) {
    formChangePassword.addEventListener('submit', (e) => {
      e.preventDefault();

      const pCurrent = pwdCurrentInput.value;
      const pNew = pwdNewInput.value;
      const pConfirm = pwdConfirmInput.value;

      if (pNew.length < 6) {
        showToast('Kata sandi baru minimal harus 6 karakter.', 'error');
        return;
      }

      if (pNew !== pConfirm) {
        showToast('Konfirmasi kata sandi baru tidak cocok.', 'error');
        return;
      }

      const currentUser = getDigiLibCurrentUser();
      currentUser.password = pNew;

      localStorage.setItem(STORAGE_CURRENT_USER, JSON.stringify(currentUser));
      syncWithSuperadminAccounts(currentUser, currentUser.email);

      formChangePassword.reset();
      showToast('Kata sandi berhasil diperbarui!', 'success');
    });
  }

  // Run
  initProfileData();
});

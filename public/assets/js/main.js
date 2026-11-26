/**
 * DigiLib - Main Global Scripts
 * Single Arrow Sidebar Toggle (<< and >>), Custom Neumorphic Logout Modal, Active Links, Modal Close Triggers
 */

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  const modalLogout = document.getElementById('modalLogoutConfirm');
  const btnConfirmLogout = document.getElementById('btnConfirmLogout');

  // Helper function to update arrow icon
  const updateToggleIcon = (isMinimized) => {
    if (!sidebarToggleBtn) return;
    const icon = sidebarToggleBtn.querySelector('i');
    if (icon) {
      if (isMinimized) {
        icon.className = 'fa-solid fa-angles-right';
        sidebarToggleBtn.setAttribute('title', 'Tampilkan Sidebar');
      } else {
        icon.className = 'fa-solid fa-angles-left';
        sidebarToggleBtn.setAttribute('title', 'Sembunyikan Sidebar');
      }
    }
  };

  // Restore saved sidebar state from localStorage for desktop
  if (sidebar && window.innerWidth > 900) {
    const isMinimized = localStorage.getItem('digilib_sidebar_minimized') === 'true';
    if (isMinimized) {
      sidebar.classList.add('minimized');
      updateToggleIcon(true);
    } else {
      updateToggleIcon(false);
    }
  }

  // Single Toggle Button Click Listener (<< and >>)
  if (sidebarToggleBtn && sidebar) {
    sidebarToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const willBeMinimized = !sidebar.classList.contains('minimized');
      sidebar.classList.toggle('minimized');
      updateToggleIcon(willBeMinimized);
      localStorage.setItem('digilib_sidebar_minimized', willBeMinimized ? 'true' : 'false');
    });
  }

  // Universal Modal Close Triggers (Tombol Batal, Tidak, Tutup, X)
  document.querySelectorAll('.modal-close-trigger').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const dialog = btn.closest('dialog');
      if (dialog) {
        dialog.close();
      }
    });
  });

  // Logout Interaction via Neumorphic Modal
  const logoutButtons = document.querySelectorAll('.nav-link-logout, .btn-logout, #sidebarLogoutBtn');
  logoutButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (modalLogout) {
        modalLogout.showModal();
      } else {
        const isSubfolder = window.location.pathname.includes('/superadmin/') || window.location.pathname.includes('/pustakawan/') || window.location.pathname.includes('/member/');
        window.location.href = isSubfolder ? '../../index.html' : '../index.html';
      }
    });
  });

  if (btnConfirmLogout) {
    btnConfirmLogout.addEventListener('click', () => {
      const isSubfolder = window.location.pathname.includes('/superadmin/') || window.location.pathname.includes('/pustakawan/') || window.location.pathname.includes('/member/');
      window.location.href = isSubfolder ? '../../index.html' : '../index.html';
    });
  }

  // Close modal when clicking backdrop
  document.querySelectorAll('dialog.modal-custom').forEach(dialog => {
    dialog.addEventListener('click', (e) => {
      // Pastikan hanya menutup jika yang diklik adalah backdrop (target adalah dialog itu sendiri, bukan elemen di dalamnya)
      if (e.target !== dialog) return;

      const rect = dialog.getBoundingClientRect();
      const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
        rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
      if (!isInDialog) {
        dialog.close();
      }
    });
  });

  // Global Quick Search
  const searchInput = document.querySelector('.search-input');
  if (searchInput && !window.location.pathname.includes('manajemen-akun.html')) {
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && searchInput.value.trim() !== '') {
        alert(`Mencari data untuk: "${searchInput.value.trim()}"`);
      }
    });
  }
});

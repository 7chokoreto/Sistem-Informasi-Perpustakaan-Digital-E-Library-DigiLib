/**
 * DigiLib - Superadmin Account Management Script (General Public Library)
 * Full Interactive Frontend: View, Add, Edit, Delete, Suspend/Activate, Filter, Search
 */

// Initial General Library Accounts Mock Data
let defaultAccounts = [
  {
    id: 1,
    name: 'Sarah Nurhaliza',
    email: 'sarah.nurhaliza@digilib.id',
    role: 'pustakawan',
    roleLabel: 'Pustakawan',
    status: 'active', // 'active' or 'suspended'
    suspensionReason: '',
    joinedDate: '12 Jan 2023',
    phone: '0812-3456-7890',
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80'
  },
  {
    id: 2,
    name: 'Rian Pratama',
    email: 'rian.pratama@digilib.id',
    role: 'pustakawan',
    roleLabel: 'Pustakawan',
    status: 'active',
    suspensionReason: '',
    joinedDate: '05 Mar 2024',
    phone: '0813-9876-5432',
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80'
  },
  {
    id: 3,
    name: 'Ahmad Fauzi',
    email: 'ahmad@digilib.id',
    role: 'member',
    roleLabel: 'Member',
    status: 'active',
    suspensionReason: '',
    joinedDate: '15 Agu 2024',
    phone: '0857-1122-3344',
    avatar: ''
  },
  {
    id: 4,
    name: 'Indah Permata',
    email: 'indah@digilib.id',
    role: 'member',
    roleLabel: 'Member',
    status: 'suspended',
    suspensionReason: 'Keterlambatan pengembalian buku lebih dari 30 hari pada judul Laut Bercerita.',
    joinedDate: '10 Feb 2024',
    phone: '0821-4455-6677',
    avatar: ''
  },
  {
    id: 5,
    name: 'Dimas Aditya',
    email: 'dimas.aditya@gmail.com',
    role: 'member',
    roleLabel: 'Member',
    status: 'active',
    suspensionReason: '',
    joinedDate: '01 Sep 2024',
    phone: '0896-3344-5566',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80'
  },
  {
    id: 6,
    name: 'Dewi Lestari',
    email: 'dewi.lestari@digilib.id',
    role: 'pustakawan',
    roleLabel: 'Pustakawan',
    status: 'active',
    suspensionReason: '',
    joinedDate: '18 Jul 2023',
    phone: '0812-7788-9900',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80'
  },
  {
    id: 7,
    name: 'Fikri Haikal',
    email: 'fikri.haikal@outlook.com',
    role: 'member',
    roleLabel: 'Member',
    status: 'suspended',
    suspensionReason: 'Terjadi kerusakan parah pada halaman buku ensiklopedia saat dipinjam.',
    joinedDate: '14 Agu 2023',
    phone: '0878-9988-7766',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80'
  }
];

// Load persisted accounts from localStorage if exists
let accountsData = defaultAccounts;
try {
  const storedAccs = localStorage.getItem('digilib_accounts');
  if (storedAccs) {
    accountsData = JSON.parse(storedAccs);
  } else {
    localStorage.setItem('digilib_accounts', JSON.stringify(defaultAccounts));
  }
} catch (e) {
  accountsData = defaultAccounts;
}

const saveAccountsToStorage = () => {
  try {
    localStorage.setItem('digilib_accounts', JSON.stringify(accountsData));
  } catch (e) {}
};

// Current filter states
let currentRoleFilter = 'all';
let currentStatusFilter = 'all';
let currentSearchQuery = '';
let targetAccountId = null;

// DOM Elements & Initialization
document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.getElementById('accountTableBody');
  const searchInput = document.getElementById('accountSearchInput');
  const globalTopSearch = document.getElementById('globalTopSearch');
  const filterTabs = document.querySelectorAll('.filter-tab');
  const statusFilterSelect = document.getElementById('statusFilterSelect');
  const addAccountBtn = document.getElementById('btnAddAccount');

  // Modals
  const modalAdd = document.getElementById('modalAddAccount');
  const modalEdit = document.getElementById('modalEditAccount');
  const modalDelete = document.getElementById('modalDeleteAccount');
  const modalDetail = document.getElementById('modalDetailAccount');
  const modalSuspend = document.getElementById('modalSuspendAccount');

  // Forms
  const formAddAccount = document.getElementById('formAddAccount');
  const formEditAccount = document.getElementById('formEditAccount');

  // Initial Render
  renderTable();
  updateCounts();

  // Search Input Listeners (Table & Global Search)
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      currentSearchQuery = e.target.value.toLowerCase().trim();
      if (globalTopSearch) globalTopSearch.value = e.target.value;
      renderTable();
    });
  }

  if (globalTopSearch) {
    globalTopSearch.addEventListener('input', (e) => {
      currentSearchQuery = e.target.value.toLowerCase().trim();
      if (searchInput) searchInput.value = e.target.value;
      renderTable();
    });
  }

  // Filter Tabs Listener (Semua / Member / Pustakawan)
  filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      filterTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentRoleFilter = tab.dataset.role;
      renderTable();
    });
  });

  // Status Filter Listener (Semua / Aktif / Ditangguhkan)
  if (statusFilterSelect) {
    statusFilterSelect.addEventListener('change', (e) => {
      currentStatusFilter = e.target.value;
      renderTable();
    });
  }

  // Open Modal Tambah Akun
  if (addAccountBtn && modalAdd) {
    addAccountBtn.addEventListener('click', () => {
      if (formAddAccount) formAddAccount.reset();
      setupRoleRadioListeners('add');
      modalAdd.showModal();
    });
  }

  // Handle Form Tambah Akun (Urutan: Role, Nama, Nomor, Email, Password)
  if (formAddAccount) {
    formAddAccount.addEventListener('submit', (e) => {
      e.preventDefault();
      const role = document.querySelector('input[name="addRole"]:checked').value;
      const name = document.getElementById('addName').value.trim();
      const phone = document.getElementById('addPhone').value.trim() || '-';
      const email = document.getElementById('addEmail').value.trim();
      const password = document.getElementById('addPassword').value;

      const newAccount = {
        id: Date.now(),
        name,
        email,
        phone,
        password: password || 'password123',
        role,
        roleLabel: role === 'pustakawan' ? 'Pustakawan' : 'Member',
        status: 'active', // Akun baru default berstatus aktif
        joinedDate: getFormattedDate(),
        avatar: getAvatarPlaceholder(name)
      };

      accountsData.unshift(newAccount);
      saveAccountsToStorage();
      modalAdd.close();
      renderTable();
      updateCounts();
      showToast('Berhasil Ditambahkan', `Akun untuk ${name} berhasil dibuat!`, 'success');
    });
  }

  // Handle Form Edit Akun (Urutan: Role, Nama, Nomor, Email, Password)
  if (formEditAccount) {
    formEditAccount.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!targetAccountId) return;

      const account = accountsData.find(a => a.id === targetAccountId);
      if (!account) return;

      account.role = document.querySelector('input[name="editRole"]:checked').value;
      account.roleLabel = account.role === 'pustakawan' ? 'Pustakawan' : 'Member';
      account.name = document.getElementById('editName').value.trim();
      account.phone = document.getElementById('editPhone').value.trim();
      account.email = document.getElementById('editEmail').value.trim();
      
      const newPassword = document.getElementById('editPassword').value;
      if (newPassword.trim() !== '') {
        account.password = newPassword;
      }

      saveAccountsToStorage();
      modalEdit.close();
      renderTable();
      updateCounts();
      showToast('Berhasil Diperbarui', `Data akun ${account.name} telah disimpan.`, 'success');
    });
  }

  // Handle Preset Change for Suspend Reason
  const reasonPreset = document.getElementById('suspendReasonPreset');
  const reasonText = document.getElementById('suspendReasonText');
  if (reasonPreset && reasonText) {
    reasonPreset.addEventListener('change', (e) => {
      if (e.target.value === 'custom') {
        reasonText.value = '';
        reasonText.placeholder = 'Tuliskan alasan penangguhan khusus...';
        reasonText.focus();
      } else {
        reasonText.value = e.target.value;
      }
    });
  }

  // Handle Suspend / Activate Confirmation Button with Reason
  const btnConfirmSuspend = document.getElementById('btnConfirmSuspend');
  if (btnConfirmSuspend && modalSuspend) {
    btnConfirmSuspend.addEventListener('click', () => {
      if (!targetAccountId) return;
      const account = accountsData.find(a => a.id === targetAccountId);
      if (!account) return;

      const isCurrentlyActive = account.status === 'active';
      if (isCurrentlyActive) {
        account.status = 'suspended';
        const reasonInput = document.getElementById('suspendReasonText');
        account.suspensionReason = (reasonInput && reasonInput.value.trim()) 
          ? reasonInput.value.trim() 
          : 'Keterlambatan pengembalian buku lebih dari 30 hari';
      } else {
        account.status = 'active';
        account.suspensionReason = '';
      }

      saveAccountsToStorage();

      // Sync if current logged in member is modified
      try {
        const curUserStr = localStorage.getItem('digilib_current_user');
        if (curUserStr) {
          const curUser = JSON.parse(curUserStr);
          if (curUser.id === account.id || curUser.email === account.email) {
            curUser.status = account.status;
            curUser.suspensionReason = account.suspensionReason;
            localStorage.setItem('digilib_current_user', JSON.stringify(curUser));
          }
        }
      } catch (err) {}

      modalSuspend.close();
      renderTable();
      updateCounts();

      if (isCurrentlyActive) {
        showToast('Akun Berhasil Ditangguhkan', `Status akun ${account.name} kini ditangguhkan dengan alasan dicatat.`, 'warning');
      } else {
        showToast('Akun Berhasil Diaktifkan', `Status akun ${account.name} telah aktif kembali.`, 'success');
      }
    });
  }

  // Handle Delete Confirmation Button
  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  if (btnConfirmDelete && modalDelete) {
    btnConfirmDelete.addEventListener('click', () => {
      if (!targetAccountId) return;
      const accountToDelete = accountsData.find(a => a.id === targetAccountId);
      const name = accountToDelete ? accountToDelete.name : 'Pengguna';

      accountsData = accountsData.filter(a => a.id !== targetAccountId);
      saveAccountsToStorage();
      modalDelete.close();
      renderTable();
      updateCounts();
      showToast('Akun Dihapus', `Akun ${name} telah berhasil dihapus dari sistem.`, 'danger');
    });
  }

  // Modal Close Triggers (Batal / X)
  document.querySelectorAll('.modal-close-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
      const dialog = btn.closest('dialog');
      if (dialog) dialog.close();
    });
  });

  // Close modal when clicking backdrop
  document.querySelectorAll('dialog.modal-custom').forEach(dialog => {
    dialog.addEventListener('click', (e) => {
      if (e.target !== dialog) return;

      const rect = dialog.getBoundingClientRect();
      const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
        rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
      if (!isInDialog) {
        dialog.close();
      }
    });
  });
});

/**
 * Render Data Table based on Active Filters & Search
 */
function renderTable() {
  const tableBody = document.getElementById('accountTableBody');
  if (!tableBody) return;

  const filtered = accountsData.filter(acc => {
    // Role filter
    if (currentRoleFilter !== 'all' && acc.role !== currentRoleFilter) return false;
    // Status filter (active or suspended)
    if (currentStatusFilter !== 'all' && acc.status !== currentStatusFilter) return false;
    // Search query
    if (currentSearchQuery) {
      const matchName = acc.name.toLowerCase().includes(currentSearchQuery);
      const matchEmail = acc.email.toLowerCase().includes(currentSearchQuery);
      const matchPhone = acc.phone.toLowerCase().includes(currentSearchQuery);
      const matchRole = acc.roleLabel.toLowerCase().includes(currentSearchQuery);
      if (!matchName && !matchEmail && !matchPhone && !matchRole) return false;
    }
    return true;
  });

  if (filtered.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="empty-table-state">
            <i class="fa-regular fa-folder-open"></i>
            <h4>Tidak ada akun ditemukan</h4>
            <p>Coba gunakan kata kunci pencarian lain atau ubah filter role/status.</p>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = filtered.map(acc => {
    const isSuspended = acc.status === 'suspended';
    return `
      <tr>
        <td>
          <div class="user-cell">
            <img src="${acc.avatar}" alt="${acc.name}" class="user-avatar" style="width: 38px; height: 38px;">
            <div class="user-cell-meta">
              <h4>${acc.name}</h4>
              <span style="font-size: 11.5px; color: var(--text-muted);">${acc.email}</span>
            </div>
          </div>
        </td>
        <td>
          <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">${acc.phone}</span>
        </td>
        <td>
          <span class="badge-role ${acc.role}">
            <i class="fa-solid ${acc.role === 'pustakawan' ? 'fa-user-tie' : 'fa-user'}"></i>
            ${acc.roleLabel}
          </span>
        </td>
        <td>
          <span class="badge-status ${acc.status}">
            <span class="status-dot"></span>
            ${isSuspended ? 'Ditangguhkan' : 'Aktif'}
          </span>
        </td>
        <td>
          <span style="font-size: 12px; color: var(--text-muted);">${acc.joinedDate}</span>
        </td>
        <td style="text-align: center;">
          <div class="action-btn-group">
            <!-- 1. Tombol Mata (Lihat Detail) -->
            <button class="action-icon-btn btn-view" title="Lihat Detail Akun" onclick="openDetailModal(${acc.id})">
              <i class="fa-regular fa-eye"></i>
            </button>
            <!-- 2. Tombol Simbol Dilarang (Lingkaran yang Dicoret: Tangguhkan / Aktifkan) -->
            <button class="action-icon-btn btn-suspend ${isSuspended ? 'is-suspended' : ''}" 
                    title="${isSuspended ? 'Aktifkan Kembali Akun' : 'Tangguhkan Akun'}" 
                    onclick="openSuspendModal(${acc.id})">
              <i class="fa-solid fa-ban"></i>
            </button>
            <!-- 3. Tombol Edit (Ubah Data) -->
            <button class="action-icon-btn btn-edit" title="Ubah Data Akun" onclick="openEditModal(${acc.id})">
              <i class="fa-regular fa-pen-to-square"></i>
            </button>
            <!-- 4. Tombol Hapus (Hapus Akun) -->
            <button class="action-icon-btn btn-delete" title="Hapus Akun" onclick="openDeleteModal(${acc.id})">
              <i class="fa-regular fa-trash-can"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join('');
}

/**
 * Update Counter Badges on Filter Tabs
 */
function updateCounts() {
  const totalCount = accountsData.length;
  const memberCount = accountsData.filter(a => a.role === 'member').length;
  const pustakawanCount = accountsData.filter(a => a.role === 'pustakawan').length;

  const countAll = document.getElementById('countAll');
  const countMember = document.getElementById('countMember');
  const countPustakawan = document.getElementById('countPustakawan');

  if (countAll) countAll.textContent = totalCount;
  if (countMember) countMember.textContent = memberCount;
  if (countPustakawan) countPustakawan.textContent = pustakawanCount;
}

/**
 * Open Suspend / Activate Confirmation Modal
 */
window.openSuspendModal = function(id) {
  const acc = accountsData.find(a => a.id === id);
  if (!acc) return;

  targetAccountId = id;
  const modal = document.getElementById('modalSuspendAccount');
  const suspendTitle = document.getElementById('suspendModalTitle');
  const suspendQuestion = document.getElementById('suspendModalQuestion');
  const suspendBtn = document.getElementById('btnConfirmSuspend');

  const isSuspended = acc.status === 'suspended';
  const reasonContainer = document.getElementById('suspendReasonContainer');
  const reasonPreset = document.getElementById('suspendReasonPreset');
  const reasonText = document.getElementById('suspendReasonText');
  const suspendIcon = document.getElementById('suspendModalIcon');

  if (isSuspended) {
    if (suspendTitle) suspendTitle.textContent = 'Aktifkan Kembali Akun?';
    if (suspendQuestion) {
      suspendQuestion.innerHTML = `Apakah Anda yakin ingin mengaktifkan kembali akun <br><strong style="color: var(--text-main); font-size: 14.5px;">${acc.name}</strong>? Pembatasan akses peminjaman akan dicabut.`;
    }
    if (reasonContainer) reasonContainer.style.display = 'none';
    if (suspendIcon) {
      suspendIcon.className = 'modal-icon-badge';
      suspendIcon.style.color = '#059669';
      suspendIcon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
    }
    if (suspendBtn) {
      suspendBtn.className = 'btn-pill btn-pill-primary';
      suspendBtn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Ya, Aktifkan Akun</span>';
    }
  } else {
    if (suspendTitle) suspendTitle.textContent = 'Tangguhkan Akun Pengguna?';
    if (suspendQuestion) {
      suspendQuestion.innerHTML = `Apakah Anda yakin ingin menangguhkan akun <br><strong style="color: var(--text-main); font-size: 14.5px;">${acc.name}</strong>?`;
    }
    if (reasonContainer) reasonContainer.style.display = 'block';
    if (reasonPreset) reasonPreset.value = 'Keterlambatan pengembalian buku lebih dari 30 hari';
    if (reasonText) reasonText.value = 'Keterlambatan pengembalian buku lebih dari 30 hari';
    if (suspendIcon) {
      suspendIcon.className = 'modal-icon-badge warning';
      suspendIcon.style.color = '#d97706';
      suspendIcon.innerHTML = '<i class="fa-solid fa-ban"></i>';
    }
    if (suspendBtn) {
      suspendBtn.className = 'btn-warning-action';
      suspendBtn.innerHTML = '<i class="fa-solid fa-ban"></i> <span>Ya, Tangguhkan Akun</span>';
    }
  }

  if (modal) modal.showModal();
};

/**
 * Open Detail Modal
 */
window.openDetailModal = function(id) {
  const acc = accountsData.find(a => a.id === id);
  if (!acc) return;

  const modal = document.getElementById('modalDetailAccount');
  const detailAvatar = document.getElementById('detailAvatar');
  const detailName = document.getElementById('detailName');
  const detailEmail = document.getElementById('detailEmail');
  const detailRole = document.getElementById('detailRole');
  const detailStatus = document.getElementById('detailStatus');
  const detailJoined = document.getElementById('detailJoined');
  const detailPhone = document.getElementById('detailPhone');

  if (detailAvatar) detailAvatar.src = acc.avatar;
  if (detailName) detailName.textContent = acc.name;
  if (detailEmail) detailEmail.textContent = acc.email;
  if (detailRole) detailRole.innerHTML = `
    <span class="badge-role ${acc.role}">
      <i class="fa-solid ${acc.role === 'pustakawan' ? 'fa-user-tie' : 'fa-user'}"></i>
      ${acc.roleLabel}
    </span>
  `;
  if (detailStatus) detailStatus.innerHTML = `
    <span class="badge-status ${acc.status}">
      <span class="status-dot"></span>
      ${acc.status === 'suspended' ? 'Ditangguhkan' : 'Aktif'}
    </span>
  `;
  if (detailJoined) detailJoined.textContent = acc.joinedDate;
  if (detailPhone) detailPhone.textContent = acc.phone;

  const detailSuspensionRow = document.getElementById('detailSuspensionRow');
  const detailSuspensionReason = document.getElementById('detailSuspensionReason');
  if (acc.status === 'suspended' && acc.suspensionReason) {
    if (detailSuspensionRow) detailSuspensionRow.style.display = 'block';
    if (detailSuspensionReason) detailSuspensionReason.textContent = acc.suspensionReason;
  } else {
    if (detailSuspensionRow) detailSuspensionRow.style.display = 'none';
  }

  if (modal) modal.showModal();
};

/**
 * Open Edit Modal with Prepopulated Data
 */
window.openEditModal = function(id) {
  const acc = accountsData.find(a => a.id === id);
  if (!acc) return;

  targetAccountId = id;
  const modal = document.getElementById('modalEditAccount');

  document.getElementById('editName').value = acc.name;
  document.getElementById('editPhone').value = acc.phone;
  document.getElementById('editEmail').value = acc.email;
  const editPasswordInput = document.getElementById('editPassword');
  if (editPasswordInput) editPasswordInput.value = '';

  // Set Radio Role
  const radio = document.querySelector(`input[name="editRole"][value="${acc.role}"]`);
  if (radio) {
    radio.checked = true;
    updateRadioVisual('edit');
  }

  setupRoleRadioListeners('edit');
  if (modal) modal.showModal();
};

/**
 * Open Delete Confirmation Modal
 */
window.openDeleteModal = function(id) {
  const acc = accountsData.find(a => a.id === id);
  if (!acc) return;

  targetAccountId = id;
  const modal = document.getElementById('modalDeleteAccount');
  const deleteNameTarget = document.getElementById('deleteTargetName');
  const deleteRoleTarget = document.getElementById('deleteTargetRole');

  if (deleteNameTarget) deleteNameTarget.textContent = acc.name;
  if (deleteRoleTarget) deleteRoleTarget.textContent = `${acc.roleLabel} (${acc.email})`;

  if (modal) modal.showModal();
};

/**
 * Radio Selector Visual Helper
 */
function setupRoleRadioListeners(prefix) {
  const cards = document.querySelectorAll(`.role-radio-card[data-prefix="${prefix}"]`);
  cards.forEach(card => {
    card.addEventListener('click', () => {
      const input = card.querySelector('input[type="radio"]');
      if (input) {
        input.checked = true;
        updateRadioVisual(prefix);
      }
    });
  });
  updateRadioVisual(prefix);
}

function updateRadioVisual(prefix) {
  const cards = document.querySelectorAll(`.role-radio-card[data-prefix="${prefix}"]`);
  cards.forEach(card => {
    const input = card.querySelector('input[type="radio"]');
    if (input && input.checked) {
      card.classList.add('selected');
    } else {
      card.classList.remove('selected');
    }
  });
}

/**
 * Toast Notification Dispatcher
 */
function showToast(title, message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  let iconClass = 'fa-check';
  if (type === 'danger') iconClass = 'fa-trash-can';
  if (type === 'warning') iconClass = 'fa-ban';

  const toast = document.createElement('div');
  toast.className = 'toast-item';
  toast.innerHTML = `
    <div class="toast-icon ${type}">
      <i class="fa-solid ${iconClass}"></i>
    </div>
    <div class="toast-text">
      <h4>${title}</h4>
      <p>${message}</p>
    </div>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('hide');
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

/**
 * Helper Date Formatter
 */
function getFormattedDate() {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  const now = new Date();
  return `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
}

/**
 * Helper Avatar Generator
 */
function getAvatarPlaceholder(name) {
  const formatted = encodeURIComponent(name);
  return `https://ui-avatars.com/api/?name=${formatted}&background=2563eb&color=fff&size=120&bold=true`;
}

/**
 * DigiLib - Pustakawan Peminjaman & Pengembalian Scripts
 * Frontend Transaction Handling, Return Confirmation with Fine Calculation, and Online Loan Validation Flow
 */

document.addEventListener('DOMContentLoaded', () => {
  // Key localStorage untuk pinjaman
  const STORAGE_KEY = 'digilib_loans';

  // Default Mock Data Peminjaman Perpustakaan Umum
  const defaultLoans = [
    {
      id: 'REQ-2026-001',
      namaMember: 'Ahmad Fauzi',
      waMember: '0857-1122-3344',
      judulBuku: 'Bumi Manusia',
      kategoriBuku: 'Sastra & Fiksi Sejarah',
      tglPinjam: '2026-11-09',
      jatuhTempo: '2026-11-16',
      tglKembali: null,
      status: 'Menunggu Validasi',
      tipe: 'online',
      durasiHari: 7,
      denda: 0
    },
    {
      id: 'TRX-2026-081',
      namaMember: 'Rina Wulandari',
      waMember: '0812-8877-6655',
      judulBuku: 'Laut Bercerita',
      kategoriBuku: 'Fiksi & Sastra',
      tglPinjam: '2026-10-20',
      jatuhTempo: '2026-10-27',
      tglKembali: null,
      status: 'Terlambat',
      tipe: 'offline',
      durasiHari: 7,
      denda: 5000 // Terlambat 5 hari (Rp 1.000/hari)
    },
    {
      id: 'TRX-2026-082',
      namaMember: 'Dimas Prasetyo',
      waMember: '0857-1234-9988',
      judulBuku: 'Filosofi Teras',
      kategoriBuku: 'Pengembangan Diri',
      tglPinjam: '2026-10-28',
      jatuhTempo: '2026-11-04',
      tglKembali: null,
      status: 'Dipinjam',
      tipe: 'offline',
      durasiHari: 7,
      denda: 0
    },
    {
      id: 'TRX-2026-083',
      namaMember: 'Dewi Lestari',
      waMember: '0813-4455-6677',
      judulBuku: 'Atomic Habits',
      kategoriBuku: 'Pengembangan Diri',
      tglPinjam: '2026-10-29',
      jatuhTempo: '2026-11-05',
      tglKembali: null,
      status: 'Dipinjam',
      tipe: 'offline',
      durasiHari: 7,
      denda: 0
    },
    {
      id: 'TRX-2026-080',
      namaMember: 'Budi Santoso',
      waMember: '0811-2233-4455',
      judulBuku: 'Laskar Pelangi',
      kategoriBuku: 'Fiksi & Sastra',
      tglPinjam: '2026-10-15',
      jatuhTempo: '2026-10-22',
      tglKembali: '2026-10-21',
      status: 'Dikembalikan',
      tipe: 'offline',
      durasiHari: 7,
      denda: 0
    },
    {
      id: 'TRX-2026-079',
      namaMember: 'Siti Rahma',
      waMember: '0878-9900-1122',
      judulBuku: 'Ensiklopedia Sains Populer',
      kategoriBuku: 'Sains & Teknologi',
      tglPinjam: '2026-10-10',
      jatuhTempo: '2026-10-17',
      tglKembali: '2026-10-18',
      status: 'Dikembalikan',
      tipe: 'offline',
      durasiHari: 7,
      denda: 1000 // Terlambat 1 hari, sudah dibayar saat kembali
    }
  ];

  // Inisialisasi Data dari LocalStorage
  let loans = [];
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
      loans = JSON.parse(stored);
    } else {
      loans = defaultLoans;
      localStorage.setItem(STORAGE_KEY, JSON.stringify(loans));
    }
  } catch (e) {
    loans = defaultLoans;
  }

  const saveLoans = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(loans));
    } catch (e) {
      console.error('Error saving loans to localStorage', e);
    }
  };

  // DOM Elements
  const loanTableBody = document.getElementById('loanTableBody');
  const loanSearchInput = document.getElementById('loanSearchInput');
  const filterTabs = document.querySelectorAll('.loan-filter-tab');
  const totalLoanCountEl = document.getElementById('totalLoanCount');
  const countPendingValidationEl = document.getElementById('countPendingValidation');

  // Modals
  const modalAddLoan = document.getElementById('modalAddLoan');
  const modalReturnBook = document.getElementById('modalReturnBook');
  const modalApproveOnline = document.getElementById('modalApproveOnlineLoan');
  const modalRejectOnline = document.getElementById('modalRejectOnlineLoan');

  // Form & Action Buttons
  const formAddLoan = document.getElementById('formAddLoan');
  const btnConfirmReturn = document.getElementById('btnConfirmReturn');
  const btnConfirmApproveOnline = document.getElementById('btnConfirmApproveOnline');
  const btnConfirmRejectOnline = document.getElementById('btnConfirmRejectOnline');
  const rejectReasonSelect = document.getElementById('rejectReasonSelect');
  const rejectCustomReasonWrap = document.getElementById('rejectCustomReasonWrap');
  const rejectCustomReasonText = document.getElementById('rejectCustomReasonText');

  let activeFilterStatus = 'all';
  let activeReturnLoanId = null;
  let activeValidationLoanId = null;

  // Toast Function
  const showToast = (message, type = 'success') => {
    let toast = document.getElementById('pustakawanToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'pustakawanToast';
      toast.className = 'toast-notification';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-exclamation'}"></i> <span>${message}</span>`;
    toast.className = `toast-notification show ${type}`;
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3200);
  };

  // Format IDR Currency
  const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
  };

  // Update Badge Hitungan Validasi Pending
  const updatePendingBadge = () => {
    if (countPendingValidationEl) {
      const pendingCount = loans.filter(l => l.status === 'Menunggu Validasi').length;
      countPendingValidationEl.textContent = pendingCount;
      countPendingValidationEl.style.display = pendingCount > 0 ? 'inline-flex' : 'none';
    }
  };

  // Render Table
  const renderLoans = () => {
    if (!loanTableBody) return;

    updatePendingBadge();

    const searchTerm = (loanSearchInput ? loanSearchInput.value : '').toLowerCase().trim();

    const filtered = loans.filter(l => {
      const matchSearch = (l.id || '').toLowerCase().includes(searchTerm) ||
                          (l.namaMember || '').toLowerCase().includes(searchTerm) ||
                          (l.waMember || '').includes(searchTerm) ||
                          (l.judulBuku || '').toLowerCase().includes(searchTerm);

      const matchFilter = activeFilterStatus === 'all' || l.status === activeFilterStatus;

      return matchSearch && matchFilter;
    });

    if (totalLoanCountEl) {
      totalLoanCountEl.textContent = `${filtered.length} Transaksi`;
    }

    if (filtered.length === 0) {
      loanTableBody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-folder-open" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
            <p style="font-size: 14px; font-weight: 600;">Tidak ada catatan peminjaman sesuai filter.</p>
          </td>
        </tr>
      `;
      return;
    }

    loanTableBody.innerHTML = filtered.map(l => {
      let badgeClass = 'dipinjam';
      let icon = 'clock';
      let badgeLabel = l.status;

      if (l.status === 'Dikembalikan') {
        badgeClass = 'kembali';
        icon = 'circle-check';
      } else if (l.status === 'Terlambat') {
        badgeClass = 'terlambat';
        icon = 'triangle-exclamation';
      } else if (l.status === 'Menunggu Validasi') {
        badgeClass = 'pending-validation';
        icon = 'inbox';
      } else if (l.status === 'Disetujui') {
        badgeClass = 'kembali';
        icon = 'file-circle-check';
        badgeLabel = 'Disetujui (Ambil)';
      } else if (l.status === 'Ditolak') {
        badgeClass = 'terlambat';
        icon = 'circle-xmark';
      }

      const fineHtml = l.denda > 0 
        ? `<span class="fine-badge has-fine">${formatRupiah(l.denda)}</span>` 
        : `<span class="fine-badge no-fine">Rp 0</span>`;

      // Tindakan berdasarkan status
      let actionHtml = '';
      if (l.status === 'Menunggu Validasi') {
        actionHtml = `
          <div style="display: flex; gap: 8px; justify-content: center;">
            <button class="btn-table-action btn-approve-trigger" data-id="${l.id}" title="Setujui Pengajuan Pinjam" style="background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.25); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
              <i class="fa-solid fa-check"></i> Setujui
            </button>
            <button class="btn-table-action btn-reject-trigger" data-id="${l.id}" title="Tolak Pengajuan Pinjam" style="background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.25); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
              <i class="fa-solid fa-xmark"></i> Tolak
            </button>
          </div>
        `;
      } else if (l.status === 'Disetujui') {
        actionHtml = `
          <button class="btn-return-book btn-activate-loan-trigger" data-id="${l.id}" title="Konfirmasi Pengambilan Buku Fisik" style="background: linear-gradient(135deg, #1d4ed8, #2563eb); color: #fff;">
            <i class="fa-solid fa-hand-holding-hand"></i>
            <span>Serahkan Buku</span>
          </button>
        `;
      } else if (l.status === 'Dipinjam' || l.status === 'Terlambat') {
        actionHtml = `
          <button class="btn-return-book btn-return-trigger" data-id="${l.id}" title="Terima Pengembalian Buku">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Kembalikan</span>
          </button>
        `;
      } else if (l.status === 'Dikembalikan') {
        actionHtml = `
          <span style="font-size: 12px; color: #059669; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            <i class="fa-solid fa-check"></i> Selesai
          </span>
        `;
      } else if (l.status === 'Ditolak') {
        actionHtml = `
          <span style="font-size: 12px; color: #dc2626; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            <i class="fa-solid fa-xmark"></i> Ditolak
          </span>
        `;
      }

      return `
        <tr>
          <td>
            <span style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #1d4ed8;">${l.id}</span>
            ${l.tipe === 'online' ? '<span style="display: block; font-size: 10px; color: #f59e0b; font-weight: 700; margin-top: 2px;"><i class="fa-solid fa-wifi"></i> Online</span>' : ''}
          </td>
          <td>
            <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">${l.namaMember}</div>
            <div style="font-size: 12px; color: var(--text-muted);"><i class="fa-brands fa-whatsapp"></i> ${l.waMember}</div>
          </td>
          <td>
            <div style="font-weight: 600; color: var(--text-main); font-size: 13.5px;">${l.judulBuku}</div>
            <div style="font-size: 11px; color: var(--text-muted);">${l.kategoriBuku}</div>
          </td>
          <td>
            <div style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">${l.tglPinjam}</div>
            <div style="font-size: 11px; color: var(--text-muted);">Batas: <b style="color: ${l.status === 'Terlambat' ? '#dc2626' : '#475569'};">${l.jatuhTempo}</b></div>
          </td>
          <td>
            <span class="loan-badge ${badgeClass}" style="${l.status === 'Menunggu Validasi' ? 'background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);' : ''}">
              <i class="fa-solid fa-${icon}"></i> ${badgeLabel}
            </span>
          </td>
          <td>
            ${fineHtml}
          </td>
          <td>
            <div class="table-actions" style="justify-content: center;">
              ${actionHtml}
            </div>
          </td>
        </tr>
      `;
    }).join('');

    // Attach Return Click Listeners
    document.querySelectorAll('.btn-return-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const l = loans.find(x => x.id === id);
        if (l && modalReturnBook) {
          activeReturnLoanId = id;
          document.getElementById('returnTrxId').textContent = l.id;
          document.getElementById('returnMemberName').textContent = l.namaMember;
          document.getElementById('returnBookTitle').textContent = l.judulBuku;
          document.getElementById('returnDueDate').textContent = l.jatuhTempo;
          
          const returnFineBox = document.getElementById('returnFineBox');
          const returnFineAmount = document.getElementById('returnFineAmount');

          if (l.denda > 0) {
            returnFineBox.style.display = 'block';
            returnFineAmount.textContent = formatRupiah(l.denda);
          } else {
            returnFineBox.style.display = 'none';
          }

          modalReturnBook.showModal();
        }
      });
    });

    // Attach Approve Online Click Listeners
    document.querySelectorAll('.btn-approve-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const l = loans.find(x => x.id === id);
        if (l && modalApproveOnline) {
          activeValidationLoanId = id;
          document.getElementById('approveTrxId').textContent = l.id;
          document.getElementById('approveMemberName').textContent = l.namaMember;
          document.getElementById('approveBookTitle').textContent = l.judulBuku;
          document.getElementById('approveDuration').textContent = `${l.durasiHari || 7} Hari (Batas: ${l.jatuhTempo})`;
          modalApproveOnline.showModal();
        }
      });
    });

    // Attach Reject Online Click Listeners
    document.querySelectorAll('.btn-reject-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const l = loans.find(x => x.id === id);
        if (l && modalRejectOnline) {
          activeValidationLoanId = id;
          document.getElementById('rejectTrxId').textContent = l.id;
          document.getElementById('rejectMemberName').textContent = l.namaMember;
          modalRejectOnline.showModal();
        }
      });
    });

    // Attach Activate/Serahkan Buku Click Listeners
    document.querySelectorAll('.btn-activate-loan-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const idx = loans.findIndex(x => x.id === id);
        if (idx !== -1) {
          loans[idx].status = 'Dipinjam';
          saveLoans();
          renderLoans();
          showToast(`Buku pada transaksi ${id} telah diserahkan ke anggota (Status: Dipinjam).`);
        }
      });
    });
  };

  // Add Loan Submission
  if (formAddLoan) {
    formAddLoan.addEventListener('submit', (e) => {
      e.preventDefault();
      const namaMember = document.getElementById('addLoanMember').value.trim();
      const waMember = document.getElementById('addLoanWa').value.trim();
      const judulBuku = document.getElementById('addLoanBook').value.trim();
      const kategoriBuku = document.getElementById('addLoanCategory').value;
      const tglPinjam = document.getElementById('addLoanDate').value || new Date().toISOString().split('T')[0];
      const durasiHari = parseInt(document.getElementById('addLoanDuration').value) || 7;

      // Calculate Due Date
      const pinjamDateObj = new Date(tglPinjam);
      pinjamDateObj.setDate(pinjamDateObj.getDate() + durasiHari);
      const jatuhTempo = pinjamDateObj.toISOString().split('T')[0];

      const newId = `TRX-2026-${String(loans.length + 84).padStart(3, '0')}`;
      loans.unshift({
        id: newId,
        namaMember,
        waMember: waMember || '0812-0000-0000',
        judulBuku,
        kategoriBuku,
        tglPinjam,
        jatuhTempo,
        tglKembali: null,
        status: 'Dipinjam',
        tipe: 'offline',
        durasiHari,
        denda: 0
      });

      saveLoans();
      modalAddLoan.close();
      formAddLoan.reset();
      renderLoans();
      showToast(`Peminjaman baru ${newId} berhasil dicatat.`);
    });
  }

  // Confirm Return Book
  if (btnConfirmReturn) {
    btnConfirmReturn.addEventListener('click', () => {
      if (!activeReturnLoanId) return;

      const idx = loans.findIndex(x => x.id === activeReturnLoanId);
      if (idx !== -1) {
        const todayStr = new Date().toISOString().split('T')[0];
        loans[idx].status = 'Dikembalikan';
        loans[idx].tglKembali = todayStr;
        
        saveLoans();
        modalReturnBook.close();
        renderLoans();
        showToast(`Buku pada transaksi ${activeReturnLoanId} berhasil dikembalikan.`);
        activeReturnLoanId = null;
      }
    });
  }

  // Confirm Approve Online Loan
  if (btnConfirmApproveOnline) {
    btnConfirmApproveOnline.addEventListener('click', () => {
      if (!activeValidationLoanId) return;

      const idx = loans.findIndex(x => x.id === activeValidationLoanId);
      if (idx !== -1) {
        loans[idx].status = 'Disetujui';
        saveLoans();
        modalApproveOnline.close();
        renderLoans();
        showToast(`Pengajuan ${activeValidationLoanId} disetujui. Siap diambil oleh anggota.`);
        activeValidationLoanId = null;
      }
    });
  }

  // Handle Reject Reason Toggle
  if (rejectReasonSelect && rejectCustomReasonWrap) {
    rejectReasonSelect.addEventListener('change', () => {
      if (rejectReasonSelect.value === 'custom') {
        rejectCustomReasonWrap.style.display = 'block';
      } else {
        rejectCustomReasonWrap.style.display = 'none';
      }
    });
  }

  // Confirm Reject Online Loan
  if (btnConfirmRejectOnline) {
    btnConfirmRejectOnline.addEventListener('click', () => {
      if (!activeValidationLoanId) return;

      const idx = loans.findIndex(x => x.id === activeValidationLoanId);
      if (idx !== -1) {
        let reason = rejectReasonSelect ? rejectReasonSelect.value : 'Peminjaman ditolak oleh pustakawan.';
        if (reason === 'custom' && rejectCustomReasonText) {
          reason = rejectCustomReasonText.value.trim() || 'Peminjaman ditolak oleh pustakawan.';
        }

        loans[idx].status = 'Ditolak';
        loans[idx].alasanPenolakan = reason;
        saveLoans();
        modalRejectOnline.close();
        renderLoans();
        showToast(`Pengajuan ${activeValidationLoanId} telah ditolak.`);
        activeValidationLoanId = null;
      }
    });
  }

  // Filter Tabs Listeners
  filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      filterTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      activeFilterStatus = tab.getAttribute('data-status');
      renderLoans();
    });
  });

  // Search Input Listener
  if (loanSearchInput) {
    loanSearchInput.addEventListener('input', () => {
      renderLoans();
    });
  }

  // Initial Render
  renderLoans();
});

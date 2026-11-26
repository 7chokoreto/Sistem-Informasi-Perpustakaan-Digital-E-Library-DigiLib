/**
 * DigiLib - Member Pinjam Buku Online Script
 * Manages Book Catalog, Interactive Online Loan Application, Suspension Check, and Real-Time Shared Loans Storage
 */

document.addEventListener('DOMContentLoaded', () => {
  const STORAGE_LOANS = 'digilib_loans';

  // Mock Book Catalog Perpustakaan Umum
  const bookCatalog = [
    {
      id: 'BK-001',
      title: 'Bumi Manusia',
      author: 'Pramoedya Ananta Toer',
      category: 'Fiksi & Sastra',
      stock: 4,
      cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Kisah Minke, seorang pribumi cerdas di era kolonial Hindia Belanda, dan perjuangannya menghadapi diskriminasi sosial dan feodalisme.'
    },
    {
      id: 'BK-002',
      title: 'Laut Bercerita',
      author: 'Leila S. Chudori',
      category: 'Fiksi & Sastra',
      stock: 0, // Habis dipinjam
      cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Novel tentang kehilangan, persahabatan, kekeluargaan, dan perjuangan aktivis mahasiswa di era Orde Baru.'
    },
    {
      id: 'BK-003',
      title: 'Filosofi Teras',
      author: 'Henry Manampiring',
      category: 'Pengembangan Diri',
      stock: 5,
      cover: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Penerapan filsafat Yunani-Romawi Kuno Stoisisme (Stoik) untuk mengatasi kekhawatiran dan mengendalikan emosi negatif sehari-hari.'
    },
    {
      id: 'BK-004',
      title: 'Atomic Habits',
      author: 'James Clear',
      category: 'Pengembangan Diri',
      stock: 6,
      cover: 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Panduan praktis membangun kebiasaan baik dan menghilangkan kebiasaan buruk melalui perubahan kecil 1% setiap hari.'
    },
    {
      id: 'BK-005',
      title: 'Ensiklopedia Sains Populer',
      author: 'Prof. Bambang Subagyo',
      category: 'Sains & Teknologi',
      stock: 2,
      cover: 'https://images.unsplash.com/photo-1532012164546-f432f2e3edd4?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Kumpulan fakta sains, fisika, alam semesta, dan evolusi teknologi modern yang dikemas dalam bahasa populer dan ilustrasi informatif.'
    },
    {
      id: 'BK-006',
      title: 'Sejarah Nusantara & Jalur Rempah',
      author: 'Dr. Taufik Abdullah',
      category: 'Sejarah & Budaya',
      stock: 3,
      cover: 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Eksplorasi jejak perdagangan rempah maritim Nusantara yang membentuk peradaban global dan hubungan antar bangsa di masa lampau.'
    },
    {
      id: 'BK-007',
      title: 'Laskar Pelangi',
      author: 'Andrea Hirata',
      category: 'Fiksi & Sastra',
      stock: 3,
      cover: 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Kisah inspiratif tentang sepuluh anak dari keluarga miskin di Belitung yang gigih meraih mimpi dan menimba ilmu di sekolah sederhana.'
    },
    {
      id: 'BK-008',
      title: 'The Psychology of Money',
      author: 'Morgan Housel',
      category: 'Pengembangan Diri',
      stock: 4,
      cover: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=400&auto=format&fit=crop&q=80',
      synopsis: 'Pelajaran berharga mengenai bagaimana emosi, perilaku, dan cara pandang manusia memengaruhi keputusan finansial mereka.'
    }
  ];

  // DOM Elements
  const bookCatalogGrid = document.getElementById('bookCatalogGrid');
  const bookSearchInput = document.getElementById('bookSearchInput');
  const totalBooksCountEl = document.getElementById('totalBooksCount');
  const categoryTabs = document.querySelectorAll('.book-category-tab');

  const tabCatalogView = document.getElementById('tabCatalogView');
  const tabHistoryView = document.getElementById('tabHistoryView');
  const sectionCatalogView = document.getElementById('sectionCatalogView');
  const sectionHistoryView = document.getElementById('sectionHistoryView');
  const memberHistoryTableBody = document.getElementById('memberHistoryTableBody');
  const memberPendingBadge = document.getElementById('memberPendingBadge');

  // Modal Request Loan
  const modalRequestLoan = document.getElementById('modalRequestLoan');
  const formRequestLoan = document.getElementById('formRequestLoan');
  const reqBookIdHidden = document.getElementById('reqBookIdHidden');
  const reqBookCover = document.getElementById('reqBookCover');
  const reqBookTitle = document.getElementById('reqBookTitle');
  const reqBookAuthor = document.getElementById('reqBookAuthor');
  const reqBookCategory = document.getElementById('reqBookCategory');
  const reqDatePick = document.getElementById('reqDatePick');
  const reqMemberPhone = document.getElementById('reqMemberPhone');
  const reqDuration = document.getElementById('reqDuration');
  const reqMemberNotes = document.getElementById('reqMemberNotes');

  let activeCategory = 'all';

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

  // Helper load all loans from localStorage
  const getLoansList = () => {
    try {
      const raw = localStorage.getItem(STORAGE_LOANS);
      if (raw) return JSON.parse(raw);
    } catch (e) {}
    return [];
  };

  const saveLoansList = (loans) => {
    try {
      localStorage.setItem(STORAGE_LOANS, JSON.stringify(loans));
    } catch (e) {}
  };

  // Render Catalog
  const renderCatalog = () => {
    if (!bookCatalogGrid) return;

    const searchTerm = (bookSearchInput ? bookSearchInput.value : '').toLowerCase().trim();

    const filtered = bookCatalog.filter(book => {
      const matchSearch = book.title.toLowerCase().includes(searchTerm) ||
                          book.author.toLowerCase().includes(searchTerm) ||
                          book.category.toLowerCase().includes(searchTerm);
      const matchCat = activeCategory === 'all' || book.category === activeCategory;
      return matchSearch && matchCat;
    });

    if (totalBooksCountEl) {
      totalBooksCountEl.textContent = `${filtered.length} Buku Ditampilkan`;
    }

    if (filtered.length === 0) {
      bookCatalogGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--text-muted);">
          <i class="fa-solid fa-book-journal-whills" style="font-size: 42px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main);">Tidak Menemukan Buku</h3>
          <p style="font-size: 13px; margin-top: 4px;">Coba gunakan kata kunci pencarian lain atau pilih kategori Semua.</p>
        </div>
      `;
      return;
    }

    const user = getDigiLibCurrentUser();

    bookCatalogGrid.innerHTML = filtered.map(b => {
      const isAvailable = b.stock > 0;
      return `
        <div class="book-card-item">
          <div class="book-card-cover-wrap">
            <img src="${b.cover}" alt="${b.title}" loading="lazy">
            <span class="book-card-category-tag">${b.category}</span>
            <span class="book-card-stock-tag ${isAvailable ? 'available' : 'empty'}">
              <i class="fa-solid fa-${isAvailable ? 'check' : 'xmark'}"></i>
              ${isAvailable ? `Tersedia: ${b.stock}` : 'Stok Habis'}
            </span>
          </div>

          <h3 class="book-card-title" title="${b.title}">${b.title}</h3>
          <div class="book-card-author">
            <i class="fa-solid fa-pen-nib" style="color: #1d4ed8;"></i>
            <span>${b.author}</span>
          </div>
          <p class="book-card-synopsis">${b.synopsis}</p>

          <div class="book-card-footer">
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
              ID: ${b.id}
            </span>
            <button 
              type="button" 
              class="btn-pill btn-pill-primary btn-apply-loan" 
              data-id="${b.id}"
              ${!isAvailable ? 'disabled style="opacity: 0.55; cursor: not-allowed; background: #94a3b8; box-shadow: none;"' : ''}
              style="padding: 9px 18px; font-size: 12.5px;"
            >
              <i class="fa-solid fa-laptop-file"></i>
              <span>${isAvailable ? 'Pinjam Online' : 'Tidak Tersedia'}</span>
            </button>
          </div>
        </div>
      `;
    }).join('');

    // Attach click event to "Pinjam Online" buttons
    document.querySelectorAll('.btn-apply-loan').forEach(btn => {
      btn.addEventListener('click', () => {
        const bookId = btn.getAttribute('data-id');
        handleApplyLoanClick(bookId);
      });
    });
  };

  // Handle Apply Loan Button Click with Suspension Validation
  const handleApplyLoanClick = (bookId) => {
    const user = getDigiLibCurrentUser();

    // 1. Cek Apakah Akun Sedang Ditangguhkan oleh Superadmin
    if (user.status === 'suspended') {
      const reason = user.suspensionReason || 'Pelanggaran ketentuan tata tertib peminjaman buku perpustakaan.';
      alert(
        `[AKUN DITANGGUHKAN]\n\nMohon maaf, Anda tidak dapat mengajukan pinjaman buku online karena akun Anda sedang DITANGGUHKAN oleh Superadmin.\n\nAlasan Penangguhan:\n"${reason}"\n\nSilakan kunjungi menu Akun Saya atau hubungi admin perpustakaan untuk informasi pengaktifan kembali.`
      );
      showToast('Akses ditolak: Akun Anda sedang ditangguhkan oleh Superadmin.', 'error');
      return;
    }

    // 2. Jika akun aktif, buka modal pengajuan
    const book = bookCatalog.find(b => b.id === bookId);
    if (!book || !modalRequestLoan) return;

    reqBookIdHidden.value = book.id;
    reqBookTitle.textContent = book.title;
    reqBookAuthor.textContent = book.author;
    reqBookCategory.textContent = book.category;
    reqBookCover.src = book.cover;

    // Set Default Tanggal Pengambilan (Besok)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    reqDatePick.value = tomorrow.toISOString().split('T')[0];
    reqDatePick.min = new Date().toISOString().split('T')[0];

    // Pre-fill user contact phone
    if (reqMemberPhone) {
      reqMemberPhone.value = user.phone || '0857-1122-3344';
    }

    modalRequestLoan.showModal();
  };

  // Handle Form Submission Pengajuan Pinjam Online
  if (formRequestLoan) {
    formRequestLoan.addEventListener('submit', (e) => {
      e.preventDefault();

      const user = getDigiLibCurrentUser();
      if (user.status === 'suspended') {
        alert('Pengajuan dibatalkan karena akun Anda dalam status Ditangguhkan.');
        modalRequestLoan.close();
        return;
      }

      const bookId = reqBookIdHidden.value;
      const book = bookCatalog.find(b => b.id === bookId);
      if (!book) return;

      const tglAmbil = reqDatePick.value;
      const durasiHari = parseInt(reqDuration.value) || 7;
      const phone = reqMemberPhone.value.trim();
      const notes = reqMemberNotes.value.trim();

      // Hitung Tanggal Jatuh Tempo
      const pinjamDateObj = new Date(tglAmbil);
      pinjamDateObj.setDate(pinjamDateObj.getDate() + durasiHari);
      const jatuhTempo = pinjamDateObj.toISOString().split('T')[0];

      const allLoans = getLoansList();
      const newReqId = `REQ-2026-${String(allLoans.length + 101).padStart(3, '0')}`;

      const newLoanRecord = {
        id: newReqId,
        namaMember: user.name,
        waMember: phone || user.phone || '0857-1122-3344',
        judulBuku: book.title,
        kategoriBuku: book.category,
        tglPinjam: tglAmbil,
        jatuhTempo: jatuhTempo,
        tglKembali: null,
        status: 'Menunggu Validasi',
        tipe: 'online',
        durasiHari: durasiHari,
        catatan: notes,
        denda: 0
      };

      allLoans.unshift(newLoanRecord);
      saveLoansList(allLoans);

      modalRequestLoan.close();
      formRequestLoan.reset();

      showToast(`Pengajuan ${newReqId} berhasil diajukan! Menunggu validasi pustakawan.`);
      
      // Beralih ke view riwayat pengajuan agar user langsung melihat hasilnya
      switchToHistoryView();
    });
  }

  // Render Riwayat Pengajuan Saya
  const renderHistoryTable = () => {
    if (!memberHistoryTableBody) return;

    const user = getDigiLibCurrentUser();
    const allLoans = getLoansList();

    // Filter transaksi milik user saat ini
    const myHistory = allLoans.filter(l => {
      return (l.namaMember && l.namaMember.toLowerCase() === user.name.toLowerCase()) ||
             (user.phone && l.waMember === user.phone) ||
             l.tipe === 'online';
    });

    // Update pending badge
    const pendingCount = myHistory.filter(l => l.status === 'Menunggu Validasi').length;
    if (memberPendingBadge) {
      if (pendingCount > 0) {
        memberPendingBadge.textContent = pendingCount;
        memberPendingBadge.style.display = 'inline-flex';
      } else {
        memberPendingBadge.style.display = 'none';
      }
    }

    if (myHistory.length === 0) {
      memberHistoryTableBody.innerHTML = `
        <tr>
          <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
            <p style="font-size: 14px; font-weight: 600;">Belum ada pengajuan pinjam online.</p>
            <p style="font-size: 12px; margin-top: 4px;">Pilih buku dari tab Katalog Buku untuk mulai meminjam.</p>
          </td>
        </tr>
      `;
      return;
    }

    memberHistoryTableBody.innerHTML = myHistory.map(item => {
      let badgeStyle = '';
      let icon = 'clock';
      let infoText = '';

      if (item.status === 'Menunggu Validasi') {
        badgeStyle = 'background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);';
        icon = 'clock';
        infoText = '<span style="font-size: 11.5px; color: #b45309;"><i class="fa-solid fa-hourglass-half"></i> Sedang ditinjau petugas</span>';
      } else if (item.status === 'Disetujui') {
        badgeStyle = 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);';
        icon = 'circle-check';
        infoText = '<span style="font-size: 11.5px; color: #059669; font-weight: 700;"><i class="fa-solid fa-location-dot"></i> Siap diambil di meja sirkulasi</span>';
      } else if (item.status === 'Ditolak') {
        badgeStyle = 'background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.3);';
        icon = 'circle-xmark';
        const reason = item.alasanPenolakan || 'Stok tidak mencukupi atau kendala internal';
        infoText = `<span style="font-size: 11.5px; color: #dc2626;" title="${reason}"><i class="fa-solid fa-info-circle"></i> ${reason}</span>`;
      } else if (item.status === 'Dipinjam') {
        badgeStyle = 'background: rgba(29, 78, 216, 0.08); color: #1d4ed8; border: 1px solid rgba(29, 78, 216, 0.2);';
        icon = 'book-open';
        infoText = `<span style="font-size: 11.5px; color: #1d4ed8;">Batas: <b>${item.jatuhTempo}</b></span>`;
      } else if (item.status === 'Dikembalikan') {
        badgeStyle = 'background: rgba(16, 185, 129, 0.08); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);';
        icon = 'check-double';
        infoText = '<span style="font-size: 11.5px; color: #059669;">Selesai dikembalikan</span>';
      }

      return `
        <tr>
          <td>
            <span style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #1d4ed8;">${item.id}</span>
          </td>
          <td>
            <div style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">${item.judulBuku}</div>
            <div style="font-size: 11px; color: var(--text-muted);">${item.kategoriBuku}</div>
          </td>
          <td>
            <div style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">${item.tglPinjam}</div>
            <div style="font-size: 11px; color: var(--text-muted);">Rencana Ambil</div>
          </td>
          <td>
            <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">${item.durasiHari || 7} Hari</span>
          </td>
          <td style="text-align: center;">
            <span class="loan-badge" style="${badgeStyle}">
              <i class="fa-solid fa-${icon}"></i> ${item.status}
            </span>
          </td>
          <td style="text-align: center;">
            ${infoText}
          </td>
        </tr>
      `;
    }).join('');
  };

  // Switch Tabs (Katalog vs Riwayat Pengajuan)
  const switchToCatalogView = () => {
    if (tabCatalogView) tabCatalogView.classList.add('active');
    if (tabHistoryView) tabHistoryView.classList.remove('active');
    if (sectionCatalogView) sectionCatalogView.style.display = 'block';
    if (sectionHistoryView) sectionHistoryView.style.display = 'none';
  };

  const switchToHistoryView = () => {
    if (tabHistoryView) tabHistoryView.classList.add('active');
    if (tabCatalogView) tabCatalogView.classList.remove('active');
    if (sectionHistoryView) sectionHistoryView.style.display = 'block';
    if (sectionCatalogView) sectionCatalogView.style.display = 'none';
    renderHistoryTable();
  };

  if (tabCatalogView) {
    tabCatalogView.addEventListener('click', switchToCatalogView);
  }
  if (tabHistoryView) {
    tabHistoryView.addEventListener('click', switchToHistoryView);
  }

  // Category Filter Tabs
  categoryTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      categoryTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      activeCategory = tab.getAttribute('data-category');
      renderCatalog();
    });
  });

  // Search Input Listener
  if (bookSearchInput) {
    bookSearchInput.addEventListener('input', () => {
      renderCatalog();
    });
  }

  // Initial Load
  renderCatalog();
  renderHistoryTable();
});

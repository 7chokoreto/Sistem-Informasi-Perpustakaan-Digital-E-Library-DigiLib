/**
 * DigiLib - Pustakawan Katalog Buku Scripts
 * Frontend CRUD, Real-time Filter & Search, Neumorphic Modals
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initial Public Library Book Catalog Data
  let books = [
    {
      id: 'BK-001',
      isbn: '978-602-03-8591-4',
      judul: 'Laskar Pelangi',
      pengarang: 'Andrea Hirata',
      penerbit: 'Bentang Pustaka',
      tahun: 2008,
      kategori: 'Fiksi & Sastra',
      rak: 'Rak A-01',
      stokTotal: 6,
      stokTersedia: 4,
      status: 'Tersedia'
    },
    {
      id: 'BK-002',
      isbn: '978-602-291-663-5',
      judul: 'Filosofi Teras',
      pengarang: 'Henry Manampiring',
      penerbit: 'Buku Kompas',
      tahun: 2019,
      kategori: 'Pengembangan Diri',
      rak: 'Rak B-03',
      stokTotal: 5,
      stokTersedia: 2,
      status: 'Tersedia'
    },
    {
      id: 'BK-003',
      isbn: '978-602-441-020-9',
      judul: 'Sapiens: Riwayat Singkat Umat Manusia',
      pengarang: 'Yuval Noah Harari',
      penerbit: 'KPG',
      tahun: 2017,
      kategori: 'Sejarah & Budaya',
      rak: 'Rak C-02',
      stokTotal: 4,
      stokTersedia: 0,
      status: 'Habis Dipinjam'
    },
    {
      id: 'BK-004',
      isbn: '978-602-06-3317-6',
      judul: 'Atomic Habits: Perubahan Kecil Hasil Luar Biasa',
      pengarang: 'James Clear',
      penerbit: 'Gramedia Pustaka Utama',
      tahun: 2020,
      kategori: 'Pengembangan Diri',
      rak: 'Rak B-04',
      stokTotal: 7,
      stokTersedia: 5,
      status: 'Tersedia'
    },
    {
      id: 'BK-005',
      isbn: '978-979-91-0886-9',
      judul: 'Laut Bercerita',
      pengarang: 'Leila S. Chudori',
      penerbit: 'KPG',
      tahun: 2017,
      kategori: 'Fiksi & Sastra',
      rak: 'Rak A-02',
      stokTotal: 5,
      stokTersedia: 3,
      status: 'Tersedia'
    },
    {
      id: 'BK-006',
      isbn: '978-602-03-3164-5',
      judul: 'Ensiklopedia Sains Populer',
      pengarang: 'Tim Redaksi Sains',
      penerbit: 'Erlangga',
      tahun: 2021,
      kategori: 'Sains & Teknologi',
      rak: 'Rak D-01',
      stokTotal: 3,
      stokTersedia: 1,
      status: 'Tersedia'
    },
    {
      id: 'BK-007',
      isbn: '978-602-291-789-2',
      judul: 'Petualangan Detektif Cilik',
      pengarang: 'Riri Novita',
      penerbit: 'Mizan',
      tahun: 2022,
      kategori: 'Anak & Remaja',
      rak: 'Rak E-01',
      stokTotal: 4,
      stokTersedia: 0,
      status: 'Habis Dipinjam'
    }
  ];

  // DOM Elements
  const bookTableBody = document.getElementById('bookTableBody');
  const bookSearchInput = document.getElementById('bookSearchInput');
  const categoryFilter = document.getElementById('categoryFilter');
  const availabilityFilter = document.getElementById('availabilityFilter');
  const totalBooksCountEl = document.getElementById('totalBooksCount');

  // Modals
  const modalAddBook = document.getElementById('modalAddBook');
  const modalEditBook = document.getElementById('modalEditBook');
  const modalDeleteBook = document.getElementById('modalDeleteBook');
  const formAddBook = document.getElementById('formAddBook');
  const formEditBook = document.getElementById('formEditBook');
  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  const deleteBookTitleSpan = document.getElementById('deleteBookTitle');

  let activeDeleteBookId = null;

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

  // Render Table
  const renderBooks = () => {
    if (!bookTableBody) return;

    const searchTerm = (bookSearchInput ? bookSearchInput.value : '').toLowerCase().trim();
    const selectedCategory = categoryFilter ? categoryFilter.value : 'all';
    const selectedAvail = availabilityFilter ? availabilityFilter.value : 'all';

    const filtered = books.filter(b => {
      const matchSearch = b.judul.toLowerCase().includes(searchTerm) ||
                          b.pengarang.toLowerCase().includes(searchTerm) ||
                          b.isbn.toLowerCase().includes(searchTerm) ||
                          b.rak.toLowerCase().includes(searchTerm);

      const matchCategory = selectedCategory === 'all' || b.kategori === selectedCategory;
      const matchAvail = selectedAvail === 'all' || b.status === selectedAvail;

      return matchSearch && matchCategory && matchAvail;
    });

    if (totalBooksCountEl) {
      totalBooksCountEl.textContent = `${filtered.length} Judul Buku`;
    }

    if (filtered.length === 0) {
      bookTableBody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-book-open" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
            <p style="font-size: 14px; font-weight: 600;">Tidak ada koleksi buku yang sesuai kriteria pencarian.</p>
          </td>
        </tr>
      `;
      return;
    }

    bookTableBody.innerHTML = filtered.map(b => {
      const isAvailable = b.stokTersedia > 0;
      return `
        <tr>
          <td>
            <div class="book-cell">
              <div class="book-thumb">
                <i class="fa-solid fa-book"></i>
              </div>
              <div>
                <span class="book-title-text">${b.judul}</span>
                <span class="book-author-text">
                  <i class="fa-solid fa-pen-nib"></i> ${b.pengarang} &bull; ${b.tahun}
                </span>
              </div>
            </div>
          </td>
          <td>
            <span style="font-family: monospace; font-size: 12.5px; font-weight: 600; color: #475569;">${b.isbn}</span>
            <div style="font-size: 11px; color: var(--text-muted);">${b.penerbit}</div>
          </td>
          <td>
            <span class="badge-category">${b.kategori}</span>
          </td>
          <td>
            <span class="badge-shelf"><i class="fa-solid fa-layer-group"></i> ${b.rak}</span>
          </td>
          <td>
            <div style="display: flex; align-items: baseline; gap: 4px;">
              <span class="stock-indicator ${isAvailable ? 'available' : 'empty'}">${b.stokTersedia}</span>
              <span style="font-size: 11px; color: var(--text-muted);">/ ${b.stokTotal} unit</span>
            </div>
          </td>
          <td>
            <span class="badge-status ${isAvailable ? 'active' : 'suspended'}">
              <i class="fa-solid fa-${isAvailable ? 'check' : 'circle-xmark'}"></i>
              ${b.status}
            </span>
          </td>
          <td>
            <div class="table-actions">
              <button class="btn-table-action btn-edit-book" data-id="${b.id}" title="Ubah Data Buku">
                <i class="fa-solid fa-pencil"></i>
              </button>
              <button class="btn-table-action delete btn-delete-book" data-id="${b.id}" title="Hapus Buku">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    // Attach Event Listeners to Edit and Delete buttons
    document.querySelectorAll('.btn-edit-book').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const b = books.find(x => x.id === id);
        if (b && modalEditBook) {
          document.getElementById('editBookId').value = b.id;
          document.getElementById('editJudul').value = b.judul;
          document.getElementById('editPengarang').value = b.pengarang;
          document.getElementById('editPenerbit').value = b.penerbit;
          document.getElementById('editTahun').value = b.tahun;
          document.getElementById('editIsbn').value = b.isbn;
          document.getElementById('editKategori').value = b.kategori;
          document.getElementById('editRak').value = b.rak;
          document.getElementById('editStokTotal').value = b.stokTotal;
          document.getElementById('editStokTersedia').value = b.stokTersedia;
          modalEditBook.showModal();
        }
      });
    });

    document.querySelectorAll('.btn-delete-book').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const b = books.find(x => x.id === id);
        if (b && modalDeleteBook) {
          activeDeleteBookId = id;
          if (deleteBookTitleSpan) deleteBookTitleSpan.textContent = `"${b.judul}"`;
          modalDeleteBook.showModal();
        }
      });
    });
  };

  // Add Book Submission
  if (formAddBook) {
    formAddBook.addEventListener('submit', (e) => {
      e.preventDefault();
      const judul = document.getElementById('addJudul').value.trim();
      const pengarang = document.getElementById('addPengarang').value.trim();
      const penerbit = document.getElementById('addPenerbit').value.trim();
      const tahun = parseInt(document.getElementById('addTahun').value) || 2024;
      const isbn = document.getElementById('addIsbn').value.trim();
      const kategori = document.getElementById('addKategori').value;
      const rak = document.getElementById('addRak').value.trim();
      const stok = parseInt(document.getElementById('addStok').value) || 1;

      const newId = `BK-${String(books.length + 1).padStart(3, '0')}`;
      books.unshift({
        id: newId,
        isbn: isbn || '978-602-00-0000-0',
        judul,
        pengarang,
        penerbit: penerbit || 'Penerbit Umum',
        tahun,
        kategori,
        rak: rak || 'Rak A-01',
        stokTotal: stok,
        stokTersedia: stok,
        status: stok > 0 ? 'Tersedia' : 'Habis Dipinjam'
      });

      renderBooks();
      modalAddBook.close();
      formAddBook.reset();
      showToast(`Buku "${judul}" berhasil ditambahkan ke katalog.`);
    });
  }

  // Edit Book Submission
  if (formEditBook) {
    formEditBook.addEventListener('submit', (e) => {
      e.preventDefault();
      const id = document.getElementById('editBookId').value;
      const index = books.findIndex(x => x.id === id);
      if (index !== -1) {
        const total = parseInt(document.getElementById('editStokTotal').value) || 1;
        let available = parseInt(document.getElementById('editStokTersedia').value) || 0;
        if (available > total) available = total;

        books[index] = {
          ...books[index],
          judul: document.getElementById('editJudul').value.trim(),
          pengarang: document.getElementById('editPengarang').value.trim(),
          penerbit: document.getElementById('editPenerbit').value.trim(),
          tahun: parseInt(document.getElementById('editTahun').value) || books[index].tahun,
          isbn: document.getElementById('editIsbn').value.trim(),
          kategori: document.getElementById('editKategori').value,
          rak: document.getElementById('editRak').value.trim(),
          stokTotal: total,
          stokTersedia: available,
          status: available > 0 ? 'Tersedia' : 'Habis Dipinjam'
        };

        renderBooks();
        modalEditBook.close();
        showToast(`Perubahan buku "${books[index].judul}" berhasil disimpan.`);
      }
    });
  }

  // Delete Book Confirmation
  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener('click', () => {
      if (activeDeleteBookId) {
        const target = books.find(x => x.id === activeDeleteBookId);
        books = books.filter(x => x.id !== activeDeleteBookId);
        renderBooks();
        modalDeleteBook.close();
        showToast(`Buku "${target ? target.judul : ''}" berhasil dihapus.`);
        activeDeleteBookId = null;
      }
    });
  }

  // Filter Listeners
  if (bookSearchInput) bookSearchInput.addEventListener('input', renderBooks);
  if (categoryFilter) categoryFilter.addEventListener('change', renderBooks);
  if (availabilityFilter) availabilityFilter.addEventListener('change', renderBooks);

  // Trigger Open Add Book Modal
  const btnOpenAddBook = document.getElementById('btnOpenAddBook');
  if (btnOpenAddBook && modalAddBook) {
    btnOpenAddBook.addEventListener('click', () => {
      modalAddBook.showModal();
    });
  }

  // Initial render
  renderBooks();
});

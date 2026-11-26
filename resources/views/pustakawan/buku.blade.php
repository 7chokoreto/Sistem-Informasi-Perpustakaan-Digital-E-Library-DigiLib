@extends('layouts.app')

@section('title', 'Katalog Buku Perpustakaan')

@push('styles')
<style>
/* Perbaikan single clean scrollbar pada modal */
dialog.modal-custom {
  max-height: 88vh;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0;
  scrollbar-width: thin;
  scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
}
dialog.modal-custom::-webkit-scrollbar {
  width: 6px;
}
dialog.modal-custom::-webkit-scrollbar-track {
  background: transparent;
}
dialog.modal-custom::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.45);
  border-radius: 999px;
}
dialog.modal-custom::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.7);
}

/* Drag & Drop Zone Cover Neumorphic */
.cover-upload-dropzone {
  border: 2px dashed rgba(59, 130, 246, 0.35);
  border-radius: var(--radius-md);
  background: var(--bg-main);
  box-shadow: inset 2px 2px 5px rgba(166, 180, 200, 0.3), inset -2px -2px 5px rgba(255, 255, 255, 0.7);
  padding: 22px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}
.cover-upload-dropzone:hover,
.cover-upload-dropzone.dragover {
  border-color: var(--primary);
  background: rgba(59, 130, 246, 0.04);
  box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.4), inset -3px -3px 6px rgba(255, 255, 255, 0.9);
}
.dropzone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.dropzone-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(59, 130, 246, 0.1);
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}
.dropzone-title {
  font-size: 13.5px;
  font-weight: 600;
  color: var(--text-main);
  margin: 0;
}
.dropzone-browse {
  color: var(--primary);
  text-decoration: underline;
  font-weight: 700;
}
.dropzone-subtitle {
  font-size: 11.5px;
  color: var(--text-muted);
  margin: 0;
}
.dropzone-preview {
  display: flex;
  align-items: center;
  gap: 16px;
  background: var(--bg-main);
  padding: 12px 14px;
  border-radius: var(--radius-sm);
  box-shadow: 2px 2px 6px rgba(166, 180, 200, 0.35), -2px -2px 6px rgba(255, 255, 255, 0.7);
}
.dropzone-preview img {
  width: 58px;
  height: 82px;
  object-fit: cover;
  border-radius: 6px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.18);
  flex-shrink: 0;
}
.preview-info {
  flex: 1;
  text-align: left;
}
.preview-filename {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-main);
  display: block;
  max-width: 320px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.preview-filesize {
  font-size: 11.5px;
  color: var(--text-muted);
}
.btn-remove-preview {
  padding: 6px 12px;
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.18s ease;
}
.btn-remove-preview:hover {
  background: #ef4444;
  color: #fff;
}
</style>
@endpush

@section('content')
<!-- Topbar Header -->
<header class="topbar" style="justify-content: flex-end;">
  <div class="topbar-actions">
    <div class="user-profile-btn" style="cursor: default;">
      <img src="{{ auth()->user()->avatar ?: asset('assets/img/avatar-default.png') }}" 
           alt="{{ auth()->user()->name }}" class="user-avatar"
           onerror="this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80'">
      <div class="user-info">
        <span class="user-name">{{ auth()->user()->name }}</span>
        <span class="user-role" style="color: #1d4ed8; font-weight: 700;">Pustakawan Pelayanan</span>
      </div>
    </div>
  </div>
</header>

<!-- Main Body Content -->
<div class="content-body">
  
  <!-- Page Title & Header Actions -->
  <div style="margin-bottom: 26px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
      <i class="fa-solid fa-book-bookmark"></i> Manajemen Koleksi
    </span>
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 2px;">
      <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.15;">Katalog Buku Perpustakaan</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Kelola data koleksi buku umum, penempatan rak fisik, dan ketersediaan stok pinjaman.</p>
      </div>
      <button class="btn-pill btn-pill-primary" id="btnOpenAddBook" style="box-shadow: 4px 6px 16px var(--primary-glow); padding: 12px 22px; flex-shrink: 0;">
        <i class="fa-solid fa-plus"></i>
        <span>+ Tambah Buku Baru</span>
      </button>
    </div>
  </div>

  <!-- ================= TOOLBAR & FILTER SECTION ================= -->
  <div class="card-neumorphic" style="padding: 20px 24px; margin-bottom: 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      
      <!-- Search Box -->
      <div class="input-inset-box" style="width: 320px;">
        <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
        <input type="text" id="bookSearchInput" placeholder="Cari judul buku, pengarang, kode..." style="padding: 10px 14px 10px 38px; font-size: 13px;">
      </div>

      <!-- Filters & Counter -->
      <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        
        <!-- Category Select -->
        <div style="position: relative;">
          <select id="categoryFilter" style="padding: 9px 36px 9px 14px; border-radius: var(--radius-md); background: var(--bg-main); box-shadow: var(--shadow-flat-sm); font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; border: var(--border-subtle); appearance: none; -webkit-appearance: none;">
            <option value="all">Semua Kategori</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->name }}">{{ $cat->name }}</option>
            @endforeach
          </select>
          <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 11px; color: var(--text-muted); pointer-events: none;"></i>
        </div>

        <!-- Availability Select -->
        <div style="position: relative;">
          <select id="availabilityFilter" style="padding: 9px 36px 9px 14px; border-radius: var(--radius-md); background: var(--bg-main); box-shadow: var(--shadow-flat-sm); font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; border: var(--border-subtle); appearance: none; -webkit-appearance: none;">
            <option value="all">Semua Status Stok</option>
            <option value="Tersedia">Stok: Tersedia</option>
            <option value="Habis">Stok: Habis Dipinjam</option>
          </select>
          <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 11px; color: var(--text-muted); pointer-events: none;"></i>
        </div>

        <!-- Counter Pill -->
        <div style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 9px 16px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-md); box-shadow: var(--shadow-flat-sm);" id="totalBooksCount">
          {{ count($books) }} Judul Buku
        </div>

      </div>

    </div>
  </div>

  <!-- ================= BOOK TABLE CARD ================= -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 8px;">
        <thead>
          <tr>
            <th style="width: 32%;">Info Buku</th>
            <th style="width: 16%;">Kategori</th>
            <th style="width: 14%;">Lokasi Rak</th>
            <th style="width: 12%;">Stok Tersedia</th>
            <th style="width: 12%;">Status</th>
            <th style="width: 14%; text-align: center;">Tindakan</th>
          </tr>
        </thead>
        <tbody id="bookTableBody">
          @forelse($books as $book)
            <tr class="book-row" 
                data-title="{{ strtolower($book->title) }}" 
                data-author="{{ strtolower($book->author) }}" 
                data-code="{{ strtolower($book->book_code) }}"
                data-category="{{ $book->category }}" 
                data-available="{{ $book->stock > 0 ? 'Tersedia' : 'Habis' }}">
              <td>
                <div style="display: flex; align-items: center; gap: 14px;">
                  <img src="{{ $book->cover ?: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80' }}" 
                       alt="{{ $book->title }}" 
                       style="width: 44px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: var(--shadow-flat-sm); flex-shrink: 0;"
                       onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80'">
                  <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--text-main); line-height: 1.3;">{{ $book->title }}</h4>
                    <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-pen-nib"></i> {{ $book->author }}</span>
                    <span style="font-size: 11px; font-family: monospace; color: #1d4ed8; display: block; margin-top: 2px;">{{ $book->book_code }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="role-badge" style="background: rgba(37,99,235,0.08); color: #1d4ed8; font-size: 12px; border: 1px solid rgba(37,99,235,0.2);">
                  {{ $book->category }}
                </span>
              </td>
              <td>
                <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">
                  <i class="fa-solid fa-layer-group" style="color: #64748b; margin-right: 4px;"></i> {{ $book->shelf ?: 'Rak Umum' }}
                </span>
              </td>
              <td>
                <span style="font-size: 13.5px; font-weight: 800; color: {{ $book->stock > 0 ? 'var(--text-main)' : '#dc2626' }};">
                  {{ $book->stock }} unit
                </span>
              </td>
              <td>
                @if($book->stock > 0)
                  <span class="status-indicator active" style="font-weight: 700; font-size: 12px;">
                    <span class="status-dot"></span>
                    Tersedia
                  </span>
                @else
                  <span class="status-indicator inactive" style="font-weight: 700; font-size: 12px;">
                    <span class="status-dot"></span>
                    Habis Dipinjam
                  </span>
                @endif
              </td>
              <td>
                <div style="display: flex; gap: 8px; justify-content: center;">
                  <button type="button" class="btn-icon-action btn-edit-book" 
                          data-id="{{ $book->id }}"
                          data-title="{{ $book->title }}"
                          data-author="{{ $book->author }}"
                          data-category="{{ $book->category }}"
                          data-shelf="{{ $book->shelf }}"
                          data-stock="{{ $book->stock }}"
                          data-readplace="{{ $book->read_in_place_stock ?? '' }}"
                          data-location="{{ $book->location ?? 'Perpustakaan Jakarta Timur - Jatinegara' }}"
                          data-publisher="{{ $book->publisher ?? '' }}"
                          data-year="{{ $book->publication_year ?? '' }}"
                          data-edition="{{ $book->edition ?? '' }}"
                          data-isbn="{{ $book->isbn ?? '' }}"
                          data-physical="{{ $book->physical_description ?? '' }}"
                          data-language="{{ $book->language ?? '' }}"
                          data-synopsis="{{ $book->synopsis ?? '' }}"
                          data-cover="{{ $book->cover ?? '' }}"
                          title="Ubah Data Buku">
                    <i class="fa-regular fa-pen-to-square"></i>
                  </button>
                  <button type="button" class="btn-icon-action danger btn-delete-book" 
                          data-id="{{ $book->id }}"
                          data-title="{{ $book->title }}"
                          title="Hapus Buku">
                    <i class="fa-regular fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-book" style="font-size: 32px; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                Belum ada buku di katalog. Silakan tambahkan buku baru!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ================= MODAL TAMBAH BUKU BARU ================= -->
<dialog class="modal-custom" id="modalAddBook" style="max-width: 680px;">
  <div class="modal-content" style="padding: 28px 32px; overflow: visible;">
    <div class="modal-header">
      <div class="modal-header-left">
        <div class="modal-icon-badge">
          <i class="fa-solid fa-book"></i>
        </div>
        <div class="modal-title-wrap">
          <h3>Tambah Buku Baru</h3>
          <p>Daftarkan judul buku baru beserta detail penerbitan dan lokasi ketersediaan.</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form action="{{ route('pustakawan.books.store') }}" method="POST" id="formAddBook" enctype="multipart/form-data">
      @csrf
      <div class="modal-form-grid">
        
        <!-- Judul Buku -->
        <div>
          <label class="input-label-clean">Judul Buku <span class="required">*</span></label>
          <div class="input-inset-box">
            <i class="fa-solid fa-book input-icon-left"></i>
            <input type="text" name="title" placeholder="Masukkan Judul Buku" required>
          </div>
        </div>

        <!-- Penulis & Kategori -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Pengarang / Penulis <span class="required">*</span></label>
            <div class="input-inset-box">
              <i class="fa-solid fa-pen-nib input-icon-left"></i>
              <input type="text" name="author" placeholder="Nama Penulis" required>
            </div>
          </div>
          <div>
            <label class="input-label-clean">Kategori / Subjek <span class="required">*</span></label>
            <div class="input-inset-box select-wrapper">
              <i class="fa-solid fa-tags input-icon-left"></i>
              <select name="category" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Penerbit & Tahun Terbit -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Penerbit</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-building input-icon-left"></i>
              <input type="text" name="publisher" placeholder="Contoh: DigiLib Media">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Tahun Terbit</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-calendar-days input-icon-left"></i>
              <input type="number" name="publication_year" id="addBookYear" placeholder="Tahun Terbit">
            </div>
          </div>
        </div>

        <!-- ISBN & Bahasa -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Nomor ISBN</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-barcode input-icon-left"></i>
              <input type="text" name="isbn" placeholder="Contoh: 978-602-000-000-0">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Bahasa</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-globe input-icon-left"></i>
              <input type="text" name="language" placeholder="Bahasa">
            </div>
          </div>
        </div>

        <!-- Nomor Rak / Call & Deskripsi Fisik -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Nomor Rak / Call Number</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-layer-group input-icon-left"></i>
              <input type="text" name="shelf" placeholder="Contoh: J-02-251">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Deskripsi Fisik</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-ruler-combined input-icon-left"></i>
              <input type="text" name="physical_description" placeholder="Contoh: viii + 240 hlm. : Ilustrasi ; 21 cm.">
            </div>
          </div>
        </div>

        <!-- Lokasi Ketersediaan (Tersedia Di) -->
        <div>
          <label class="input-label-clean">Tersedia di (Cabang Perpustakaan) <span class="required">*</span></label>
          <div class="input-inset-box select-wrapper">
            <i class="fa-solid fa-location-dot input-icon-left"></i>
            <select name="location" required>
              <option value="Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)" selected>Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)</option>
              <option value="Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)">Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)</option>
              <option value="Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)">Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)</option>
              <option value="Perpustakaan DigiLib - Jakarta Selatan (Kuningan)">Perpustakaan DigiLib - Jakarta Selatan (Kuningan)</option>
              <option value="Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)">Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)</option>
            </select>
          </div>
        </div>

        <!-- Stok Dapat Dipinjam & Stok Baca di Tempat -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Stok Dapat Dipinjam <span class="required">*</span></label>
            <div class="input-inset-box">
              <i class="fa-solid fa-boxes-stacked input-icon-left"></i>
              <input type="number" name="stock" placeholder="0" min="0" required>
            </div>
          </div>
          <div>
            <label class="input-label-clean">Stok Baca di Tempat</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-book-reader input-icon-left"></i>
              <input type="number" name="read_in_place_stock" placeholder="0" min="0">
            </div>
          </div>
        </div>

        <!-- Drag & Drop Upload Cover Buku -->
        <div>
          <label class="input-label-clean">Foto / Cover Buku (Unggah Gambar)</label>
          <div class="cover-upload-dropzone" id="addCoverDropzone">
            <input type="file" name="cover_file" id="addCoverFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <div class="dropzone-content" id="addCoverDropzonePrompt">
              <div class="dropzone-icon">
                <i class="fa-solid fa-cloud-arrow-up"></i>
              </div>
              <div class="dropzone-text">
                <p class="dropzone-title">Tarik & lepas file gambar cover ke sini, atau <span class="dropzone-browse">Pilih File</span></p>
                <p class="dropzone-subtitle">Mendukung format JPG, PNG, atau WebP (Maks. 5MB)</p>
              </div>
            </div>
            <div class="dropzone-preview" id="addCoverDropzonePreview" style="display: none;">
              <img id="addCoverPreviewImg" src="" alt="Pratinjau Cover">
              <div class="preview-info">
                <span id="addCoverFileName" class="preview-filename"></span>
                <span id="addCoverFileSize" class="preview-filesize"></span>
              </div>
              <button type="button" class="btn-remove-preview" id="btnRemoveAddCover" title="Hapus Gambar">
                <i class="fa-solid fa-xmark"></i> Hapus
              </button>
            </div>
          </div>
        </div>

        <!-- Sinopsis -->
        <div>
          <label class="input-label-clean">Sinopsis / Deskripsi Buku</label>
          <div class="input-inset-box" style="padding: 4px;">
            <textarea name="synopsis" rows="3" class="form-input" placeholder="Ringkasan atau sinopsis isi buku..."></textarea>
          </div>
        </div>

      </div>

      <div class="modal-footer" style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 24px;">
          <i class="fa-solid fa-check"></i>
          <span>Simpan Buku</span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- ================= MODAL EDIT BUKU ================= -->
<dialog class="modal-custom" id="modalEditBook" style="max-width: 680px;">
  <div class="modal-content" style="padding: 28px 32px; overflow: visible;">
    <div class="modal-header">
      <div class="modal-header-left">
        <div class="modal-icon-badge" style="color: var(--primary);">
          <i class="fa-regular fa-pen-to-square"></i>
        </div>
        <div class="modal-title-wrap">
          <h3>Ubah Data Buku</h3>
          <p>Perbarui informasi katalog, spesifikasi penerbitan, atau lokasi cabang.</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form method="POST" id="formEditBook" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="modal-form-grid">
        
        <!-- Judul Buku -->
        <div>
          <label class="input-label-clean">Judul Buku <span class="required">*</span></label>
          <div class="input-inset-box">
            <i class="fa-solid fa-book input-icon-left"></i>
            <input type="text" name="title" id="editTitle" required>
          </div>
        </div>

        <!-- Penulis & Kategori -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Pengarang / Penulis <span class="required">*</span></label>
            <div class="input-inset-box">
              <i class="fa-solid fa-pen-nib input-icon-left"></i>
              <input type="text" name="author" id="editAuthor" required>
            </div>
          </div>
          <div>
            <label class="input-label-clean">Kategori / Subjek <span class="required">*</span></label>
            <div class="input-inset-box select-wrapper">
              <i class="fa-solid fa-tags input-icon-left"></i>
              <select name="category" id="editCategory" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Penerbit & Tahun Terbit -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Penerbit</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-building input-icon-left"></i>
              <input type="text" name="publisher" id="editPublisher" placeholder="Contoh: DigiLib Media">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Tahun Terbit</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-calendar-days input-icon-left"></i>
              <input type="number" name="publication_year" id="editYear" placeholder="Contoh: {{ date('Y') }}" min="1900" max="{{ date('Y') + 1 }}">
            </div>
          </div>
        </div>

        <!-- ISBN & Bahasa -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Nomor ISBN</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-barcode input-icon-left"></i>
              <input type="text" name="isbn" id="editIsbn" placeholder="Contoh: 978-602-000-000-0">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Bahasa</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-globe input-icon-left"></i>
              <input type="text" name="language" id="editLanguage" placeholder="Contoh: Indonesia">
            </div>
          </div>
        </div>

        <!-- Nomor Rak / Call & Deskripsi Fisik -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Nomor Rak / Call Number</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-layer-group input-icon-left"></i>
              <input type="text" name="shelf" id="editShelf" placeholder="Contoh: J-02-251">
            </div>
          </div>
          <div>
            <label class="input-label-clean">Deskripsi Fisik</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-ruler-combined input-icon-left"></i>
              <input type="text" name="physical_description" id="editPhysical" placeholder="Contoh: viii + 240 hlm. : Ilustrasi ; 21 cm.">
            </div>
          </div>
        </div>

        <!-- Lokasi Ketersediaan (Tersedia Di) -->
        <div>
          <label class="input-label-clean">Tersedia di (Cabang Perpustakaan) <span class="required">*</span></label>
          <div class="input-inset-box select-wrapper">
            <i class="fa-solid fa-location-dot input-icon-left"></i>
            <select name="location" id="editLocation" required>
              <option value="Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)">Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)</option>
              <option value="Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)">Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)</option>
              <option value="Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)">Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)</option>
              <option value="Perpustakaan DigiLib - Jakarta Selatan (Kuningan)">Perpustakaan DigiLib - Jakarta Selatan (Kuningan)</option>
              <option value="Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)">Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)</option>
            </select>
          </div>
        </div>

        <!-- Stok Dapat Dipinjam & Stok Baca di Tempat -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Stok Dapat Dipinjam <span class="required">*</span></label>
            <div class="input-inset-box">
              <i class="fa-solid fa-boxes-stacked input-icon-left"></i>
              <input type="number" name="stock" id="editStock" min="0" required>
            </div>
          </div>
          <div>
            <label class="input-label-clean">Stok Baca di Tempat</label>
            <div class="input-inset-box">
              <i class="fa-solid fa-book-reader input-icon-left"></i>
              <input type="number" name="read_in_place_stock" id="editReadPlace" min="0">
            </div>
          </div>
        </div>

        <!-- Drag & Drop Upload Cover Edit -->
        <div>
          <label class="input-label-clean">Foto / Cover Buku</label>
          <div class="cover-upload-dropzone" id="editCoverDropzone">
            <input type="file" name="cover_file" id="editCoverFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <div class="dropzone-content" id="editCoverDropzonePrompt">
              <div class="dropzone-icon">
                <i class="fa-solid fa-cloud-arrow-up"></i>
              </div>
              <div class="dropzone-text">
                <p class="dropzone-title">Tarik & lepas gambar cover baru, atau <span class="dropzone-browse">Pilih File</span></p>
                <p class="dropzone-subtitle">Kosongkan jika tetap menggunakan cover saat ini</p>
              </div>
            </div>
            <div class="dropzone-preview" id="editCoverDropzonePreview" style="display: none;">
              <img id="editCoverPreviewImg" src="" alt="Pratinjau Cover">
              <div class="preview-info">
                <span id="editCoverFileName" class="preview-filename">Cover Saat Ini</span>
                <span id="editCoverFileSize" class="preview-filesize">Tersimpan di katalog</span>
              </div>
              <button type="button" class="btn-remove-preview" id="btnChangeEditCover" title="Ganti Cover">
                <i class="fa-solid fa-rotate"></i> Ganti
              </button>
            </div>
          </div>
        </div>

        <!-- Sinopsis -->
        <div>
          <label class="input-label-clean">Sinopsis Singkat</label>
          <div class="input-inset-box" style="padding: 4px;">
            <textarea name="synopsis" id="editSynopsis" rows="3" class="form-input" placeholder="Ringkasan atau sinopsis buku..."></textarea>
          </div>
        </div>

      </div>

      <div class="modal-footer" style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 24px;">
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Simpan Perubahan</span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- ================= MODAL HAPUS BUKU ================= -->
<dialog class="modal-custom" id="modalDeleteBook" style="max-width: 440px;">
  <div class="modal-content" style="text-align: center; padding: 34px 28px;">
    
    <div class="modal-icon-badge danger" style="width: 56px; height: 56px; font-size: 24px; margin: 0 auto 16px auto;">
      <i class="fa-regular fa-trash-can"></i>
    </div>

    <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Hapus Buku dari Katalog?</h3>
    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
      Apakah Anda yakin ingin menghapus buku <br>
      <strong id="deleteBookTitle" style="color: var(--text-main); font-size: 14px;">-</strong>? Data yang dihapus tidak dapat dipulihkan.
    </p>

    <form method="POST" id="formDeleteBook">
      @csrf
      @method('DELETE')
      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-danger-action" style="min-width: 130px; justify-content: center; background: #dc2626; color: #ffffff !important; border: none;">
          <i class="fa-solid fa-trash-can" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Ya, Hapus</span>
        </button>
      </div>
    </form>

  </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalAdd = document.getElementById('modalAddBook');
  const modalEdit = document.getElementById('modalEditBook');
  const modalDelete = document.getElementById('modalDeleteBook');

  // Helper File Size Formatter
  function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
  }

  // --- DROPZONE HANDLER: ADD BOOK ---
  const addDropzone = document.getElementById('addCoverDropzone');
  const addFileInput = document.getElementById('addCoverFileInput');
  const addPrompt = document.getElementById('addCoverDropzonePrompt');
  const addPreview = document.getElementById('addCoverDropzonePreview');
  const addPreviewImg = document.getElementById('addCoverPreviewImg');
  const addFileName = document.getElementById('addCoverFileName');
  const addFileSize = document.getElementById('addCoverFileSize');
  const btnRemoveAdd = document.getElementById('btnRemoveAddCover');

  function showAddPreview(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      addPreviewImg.src = e.target.result;
      addFileName.textContent = file.name;
      addFileSize.textContent = formatBytes(file.size);
      addPrompt.style.display = 'none';
      addPreview.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  }

  function resetAddPreview() {
    addFileInput.value = '';
    addPreviewImg.src = '';
    addFileName.textContent = '';
    addFileSize.textContent = '';
    addPreview.style.display = 'none';
    addPrompt.style.display = 'flex';
  }

  addFileInput?.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  addDropzone?.addEventListener('click', (e) => {
    if (e.target === addFileInput) return;
    if (e.target.closest('#btnRemoveAddCover')) return;
    e.stopPropagation();
    addFileInput.click();
  });

  addFileInput?.addEventListener('change', () => {
    if (addFileInput.files && addFileInput.files[0]) {
      showAddPreview(addFileInput.files[0]);
    }
  });

  btnRemoveAdd?.addEventListener('click', (e) => {
    e.stopPropagation();
    resetAddPreview();
  });

  ['dragenter', 'dragover'].forEach(evt => {
    addDropzone?.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      addDropzone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach(evt => {
    addDropzone?.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      addDropzone.classList.remove('dragover');
    });
  });

  addDropzone?.addEventListener('drop', (e) => {
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      addFileInput.files = e.dataTransfer.files;
      showAddPreview(e.dataTransfer.files[0]);
    }
  });

  // --- DROPZONE HANDLER: EDIT BOOK ---
  const editDropzone = document.getElementById('editCoverDropzone');
  const editFileInput = document.getElementById('editCoverFileInput');
  const editPrompt = document.getElementById('editCoverDropzonePrompt');
  const editPreview = document.getElementById('editCoverDropzonePreview');
  const editPreviewImg = document.getElementById('editCoverPreviewImg');
  const editFileName = document.getElementById('editCoverFileName');
  const editFileSize = document.getElementById('editCoverFileSize');
  const btnChangeEdit = document.getElementById('btnChangeEditCover');

  function showEditPreview(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      editPreviewImg.src = e.target.result;
      editFileName.textContent = file.name;
      editFileSize.textContent = formatBytes(file.size);
      editPrompt.style.display = 'none';
      editPreview.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  }

  editFileInput?.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  editDropzone?.addEventListener('click', (e) => {
    if (e.target === editFileInput) return;
    if (e.target.closest('#btnChangeEditCover')) return;
    e.stopPropagation();
    editFileInput.click();
  });

  editFileInput?.addEventListener('change', () => {
    if (editFileInput.files && editFileInput.files[0]) {
      showEditPreview(editFileInput.files[0]);
    }
  });

  btnChangeEdit?.addEventListener('click', (e) => {
    e.stopPropagation();
    editFileInput.click();
  });

  ['dragenter', 'dragover'].forEach(evt => {
    editDropzone?.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      editDropzone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach(evt => {
    editDropzone?.addEventListener(evt, (e) => {
      e.preventDefault();
      e.stopPropagation();
      editDropzone.classList.remove('dragover');
    });
  });

  editDropzone?.addEventListener('drop', (e) => {
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      editFileInput.files = e.dataTransfer.files;
      showEditPreview(e.dataTransfer.files[0]);
    }
  });


  // --- OPEN MODALS ---
  // Open Add Modal
  document.getElementById('btnOpenAddBook')?.addEventListener('click', () => {
    document.getElementById('formAddBook')?.reset();
    resetAddPreview();
    modalAdd?.showModal();
  });

  // Open Edit Modal
  document.querySelectorAll('.btn-edit-book').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      document.getElementById('formEditBook').action = `/pustakawan/buku/${id}`;
      document.getElementById('editTitle').value = btn.getAttribute('data-title') || '';
      document.getElementById('editAuthor').value = btn.getAttribute('data-author') || '';
      document.getElementById('editCategory').value = btn.getAttribute('data-category') || '';
      document.getElementById('editShelf').value = btn.getAttribute('data-shelf') || '';
      document.getElementById('editLocation').value = btn.getAttribute('data-location') || 'Perpustakaan Jakarta Timur - Jatinegara';
      document.getElementById('editStock').value = btn.getAttribute('data-stock') || '';
      document.getElementById('editReadPlace').value = btn.getAttribute('data-readplace') || '';
      document.getElementById('editPublisher').value = btn.getAttribute('data-publisher') || '';
      
      document.getElementById('editYear').value = btn.getAttribute('data-year') || '';

      document.getElementById('editIsbn').value = btn.getAttribute('data-isbn') || '';
      document.getElementById('editPhysical').value = btn.getAttribute('data-physical') || '';
      document.getElementById('editLanguage').value = btn.getAttribute('data-language') || '';
      document.getElementById('editSynopsis').value = btn.getAttribute('data-synopsis') || '';

      // Cover handling
      const currentCover = btn.getAttribute('data-cover');
      editFileInput.value = '';
      if (currentCover && currentCover !== 'null') {
        editPreviewImg.src = currentCover;
        editFileName.textContent = 'Cover Terpasang';
        editFileSize.textContent = 'Tersimpan di katalog';
        editPrompt.style.display = 'none';
        editPreview.style.display = 'flex';
      } else {
        editPreview.style.display = 'none';
        editPrompt.style.display = 'flex';
      }

      modalEdit?.showModal();
    });
  });

  // Open Delete Modal
  document.querySelectorAll('.btn-delete-book').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const title = btn.getAttribute('data-title');
      document.getElementById('deleteBookTitle').textContent = `"${title}"`;
      document.getElementById('formDeleteBook').action = `/pustakawan/buku/${id}`;
      modalDelete?.showModal();
    });
  });

  // Close triggers
  document.querySelectorAll('.modal-close-trigger').forEach(trigger => {
    trigger.addEventListener('click', () => {
      modalAdd?.close();
      modalEdit?.close();
      modalDelete?.close();
    });
  });

  // Client-side Search & Filters
  const searchInput = document.getElementById('bookSearchInput');
  const catFilter = document.getElementById('categoryFilter');
  const availFilter = document.getElementById('availabilityFilter');
  const rows = document.querySelectorAll('.book-row');
  const counter = document.getElementById('totalBooksCount');

  function filterBooks() {
    const q = (searchInput?.value || '').trim().toLowerCase();
    const cat = catFilter?.value || 'all';
    const avail = availFilter?.value || 'all';

    let visibleCount = 0;
    rows.forEach(row => {
      const title = row.getAttribute('data-title');
      const author = row.getAttribute('data-author');
      const code = row.getAttribute('data-code');
      const category = row.getAttribute('data-category');
      const available = row.getAttribute('data-available');

      const matchesSearch = !q || title.includes(q) || author.includes(q) || code.includes(q);
      const matchesCat = cat === 'all' || category === cat;
      const matchesAvail = avail === 'all' || available === avail;

      if (matchesSearch && matchesCat && matchesAvail) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (counter) {
      counter.textContent = `${visibleCount} Judul Buku`;
    }
  }

  searchInput?.addEventListener('input', filterBooks);
  catFilter?.addEventListener('change', filterBooks);
  availFilter?.addEventListener('change', filterBooks);
});
</script>
@endpush
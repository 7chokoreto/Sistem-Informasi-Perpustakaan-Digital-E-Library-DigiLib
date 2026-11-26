@extends('layouts.app')

@section('title', 'Pinjam Buku Online')

@section('content')
<div class="content-body" style="padding-top: 32px;">
  
  <!-- ================= SUSPENSION ALERT BANNER ================= -->
  @if($user->isSuspended())
    <div class="suspension-banner" id="suspensionAlertBanner">
      <div class="suspension-banner-icon">
        <i class="fa-solid fa-ban"></i>
      </div>
      <div style="flex-grow: 1;">
        <div class="suspension-banner-title">Peminjaman Online Dibekukan Sementara</div>
        <div class="suspension-banner-text">
          Akun Anda sedang dalam status <b>Ditangguhkan oleh Superadmin</b>. Anda tidak dapat melakukan pengajuan pinjam baru sampai status akun dipulihkan.
        </div>
        <div class="suspension-banner-reason">
          <i class="fa-solid fa-circle-info"></i> Alasan: <span>{{ $user->suspension_reason ?: 'Pelanggaran tata tertib perpustakaan.' }}</span>
        </div>
      </div>
      <a href="{{ route('member.akun') }}" class="btn-pill btn-pill-danger" style="background: #dc2626; color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); font-size: 12px; padding: 8px 16px; white-space: nowrap; text-decoration: none;">
        <span style="color: #ffffff !important; font-weight: 700;">Periksa Akun</span>
        <i class="fa-solid fa-arrow-right" style="color: #ffffff !important;"></i>
      </a>
    </div>
  @endif

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
      <i class="fa-solid fa-globe"></i> Layanan Online
    </span>
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
      <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.2;">Katalog &amp; Peminjaman Buku Online</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Pilih buku dari katalog, ajukan peminjaman secara online, dan pustakawan kami akan memvalidasi pesanan Anda.</p>
      </div>
      
      <!-- Navigation Tabs (Katalog vs Riwayat Pengajuan) -->
      <div class="filter-tabs-container" style="background: var(--bg-main); box-shadow: var(--shadow-inset); border-radius: var(--radius-full); padding: 4px;">
        <button type="button" class="filter-tab view-tab-btn active" id="tabCatalogView" style="border: none; cursor: pointer;">
          <i class="fa-solid fa-book-open"></i>
          <span>Katalog Buku</span>
        </button>
        <button type="button" class="filter-tab view-tab-btn" id="tabHistoryView" style="border: none; cursor: pointer;">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <span>Riwayat Pengajuan</span>
          @if($pendingCount > 0)
            <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; border-radius: 10px; padding: 1px 6px; margin-left: 4px;">{{ $pendingCount }}</span>
          @endif
        </button>
      </div>
    </div>
  </div>

  <!-- ================= SECTION 1: KATALOG BUKU ================= -->
  <div id="sectionCatalogView">
    
    <!-- Filter & Search Toolbar -->
    <div class="card-neumorphic" style="padding: 18px 24px; margin-bottom: 24px;">
      <form action="{{ route('member.pinjam-online') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        
        <!-- Category Filter Tabs -->
        <div class="filter-tabs-container">
          <a href="{{ route('member.pinjam-online', ['category' => 'all', 'search' => request('search')]) }}" class="filter-tab {{ !request('category') || request('category') === 'all' ? 'active' : '' }}" style="text-decoration: none;">
            <span>Semua Kategori</span>
          </a>
          <a href="{{ route('member.pinjam-online', ['category' => 'Fiksi & Sastra', 'search' => request('search')]) }}" class="filter-tab {{ request('category') === 'Fiksi & Sastra' ? 'active' : '' }}" style="text-decoration: none;">
            <span>Fiksi &amp; Sastra</span>
          </a>
          <a href="{{ route('member.pinjam-online', ['category' => 'Pengembangan Diri', 'search' => request('search')]) }}" class="filter-tab {{ request('category') === 'Pengembangan Diri' ? 'active' : '' }}" style="text-decoration: none;">
            <span>Pengembangan Diri</span>
          </a>
          <a href="{{ route('member.pinjam-online', ['category' => 'Sains & Teknologi', 'search' => request('search')]) }}" class="filter-tab {{ request('category') === 'Sains & Teknologi' ? 'active' : '' }}" style="text-decoration: none;">
            <span>Sains &amp; Teknologi</span>
          </a>
          <a href="{{ route('member.pinjam-online', ['category' => 'Sejarah & Budaya', 'search' => request('search')]) }}" class="filter-tab {{ request('category') === 'Sejarah & Budaya' ? 'active' : '' }}" style="text-decoration: none;">
            <span>Sejarah &amp; Budaya</span>
          </a>
        </div>

        <!-- Search Box & Count -->
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
          <div class="input-inset-box" style="width: 260px;">
            <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis..." style="padding: 10px 14px 10px 38px; font-size: 13px;" onchange="this.form.submit()">
          </div>
          <div style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 9px 16px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-md); box-shadow: var(--shadow-flat-sm);">
            {{ $books->count() }} Buku Ditampilkan
          </div>
        </div>

      </form>
    </div>

    <!-- Books Grid Container -->
    <div class="book-catalog-grid">
      @forelse($books as $book)
        @php $isAvailable = $book->stock > 0; @endphp
        <div class="book-card-item">
          <div class="book-card-cover-wrap">
            <img src="{{ $book->cover ?: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80' }}" alt="{{ $book->title }}" loading="lazy">
            <span class="book-card-category-tag">{{ $book->category }}</span>
            <span class="book-card-stock-tag {{ $isAvailable ? 'available' : 'empty' }}">
              <i class="fa-solid {{ $isAvailable ? 'fa-check' : 'fa-xmark' }}"></i>
              {{ $isAvailable ? 'Tersedia: ' . $book->stock : 'Stok Habis' }}
            </span>
          </div>

          <h3 class="book-card-title" title="{{ $book->title }}">{{ $book->title }}</h3>
          <div class="book-card-author">
            <i class="fa-solid fa-pen-nib" style="color: #1d4ed8;"></i>
            <span>{{ $book->author }}</span>
          </div>
          <p class="book-card-synopsis">{{ $book->synopsis }}</p>

          <div class="book-card-footer">
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
              ID: {{ $book->book_code }}
            </span>
            <button 
              type="button" 
              class="btn-pill btn-pill-primary btn-open-request-modal" 
              data-id="{{ $book->id }}"
              data-title="{{ $book->title }}"
              data-author="{{ $book->author }}"
              data-category="{{ $book->category }}"
              data-cover="{{ $book->cover ?: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80' }}"
              {{ !$isAvailable || $user->isSuspended() ? 'disabled' : '' }}
              style="padding: 9px 18px; font-size: 12.5px; {{ !$isAvailable || $user->isSuspended() ? 'opacity: 0.65; cursor: not-allowed; background: #94a3b8; color: #ffffff !important; border: none; box-shadow: none;' : '' }}"
            >
              <i class="fa-solid fa-laptop-file" style="{{ !$isAvailable || $user->isSuspended() ? 'color: #ffffff !important;' : '' }}"></i>
              <span style="{{ !$isAvailable || $user->isSuspended() ? 'color: #ffffff !important;' : '' }}">{{ $user->isSuspended() ? 'Akun Ditangguhkan' : ($isAvailable ? 'Pinjam Online' : 'Stok Habis') }}</span>
            </button>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--text-muted);">
          <i class="fa-solid fa-book-journal-whills" style="font-size: 42px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main);">Tidak Menemukan Buku</h3>
          <p style="font-size: 13px; margin-top: 4px;">Coba gunakan kata kunci pencarian lain atau pilih kategori Semua.</p>
        </div>
      @endforelse
    </div>

  </div>

  <!-- ================= SECTION 2: RIWAYAT PENGAJUAN ONLINE ================= -->
  <div id="sectionHistoryView" style="display: none;">
    <div class="card-neumorphic" style="padding: 24px 26px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <div>
          <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main);">Daftar Permohonan Pinjam Online Anda</h2>
          <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Pantau proses validasi oleh pustakawan hingga instruksi pengambilan buku di perpustakaan.</p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="custom-table" style="border-spacing: 0 8px;">
          <thead>
            <tr>
              <th style="width: 16%;">No. Pengajuan</th>
              <th style="width: 26%;">Buku yang Dipinjam</th>
              <th style="width: 16%;">Tgl Pengajuan</th>
              <th style="width: 14%;">Durasi Pinjam</th>
              <th style="width: 16%; text-align: center;">Status Validasi</th>
              <th style="width: 12%; text-align: center;">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($myHistory as $item)
              @php
                $badgeStyle = '';
                $icon = 'clock';
                $infoText = '';

                if ($item->status === 'Menunggu Validasi') {
                  $badgeStyle = 'background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);';
                  $icon = 'clock';
                  $infoText = '<span style="font-size: 11.5px; color: #b45309;"><i class="fa-solid fa-hourglass-half"></i> Sedang ditinjau petugas</span>';
                } elseif ($item->status === 'Disetujui') {
                  $badgeStyle = 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);';
                  $icon = 'circle-check';
                  $infoText = '<span style="font-size: 11.5px; color: #059669; font-weight: 700;"><i class="fa-solid fa-location-dot"></i> Siap diambil di meja sirkulasi</span>';
                } elseif ($item->status === 'Ditolak') {
                  $badgeStyle = 'background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.3);';
                  $icon = 'circle-xmark';
                  $infoText = '<span style="font-size: 11.5px; color: #dc2626;"><i class="fa-solid fa-info-circle"></i> ' . ($item->rejection_reason ?: 'Ditolak oleh petugas') . '</span>';
                } elseif ($item->status === 'Dipinjam') {
                  $badgeStyle = 'background: rgba(29, 78, 216, 0.08); color: #1d4ed8; border: 1px solid rgba(29, 78, 216, 0.2);';
                  $icon = 'book-open';
                  $infoText = '<span style="font-size: 11.5px; color: #1d4ed8;">Batas: <b>' . $item->due_date . '</b></span>';
                } elseif ($item->status === 'Dikembalikan') {
                  $badgeStyle = 'background: rgba(16, 185, 129, 0.08); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);';
                  $icon = 'check-double';
                  $infoText = '<span style="font-size: 11.5px; color: #059669;">Selesai dikembalikan</span>';
                }
              @endphp
              <tr>
                <td>
                  <span style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #1d4ed8;">{{ $item->trx_code }}</span>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">{{ $item->book->title ?? '-' }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $item->book->category ?? '-' }}</div>
                </td>
                <td>
                  <div style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">{{ $item->borrow_date }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">Rencana Ambil</div>
                </td>
                <td>
                  <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">{{ $item->duration_days }} Hari</span>
                </td>
                <td style="text-align: center;">
                  <span class="loan-badge" style="{!! $badgeStyle !!}">
                    <i class="fa-solid fa-{{ $icon }}"></i> {{ $item->status }}
                  </span>
                </td>
                <td style="text-align: center;">
                  {!! $infoText !!}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                  <i class="fa-solid fa-inbox" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
                  <p style="font-size: 14px; font-weight: 600;">Belum ada pengajuan pinjam online.</p>
                  <p style="font-size: 12px; margin-top: 4px;">Pilih buku dari tab Katalog Buku untuk mulai meminjam.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ================= MODAL PENGAJUAN PINJAM ONLINE ================= -->
<dialog class="modal-custom" id="modalRequestLoan" style="max-width: 520px;">
  <div class="modal-content">
    
    <div class="modal-header">
      <div class="modal-title-wrap">
        <div class="modal-icon-badge" style="color: #1d4ed8;">
          <i class="fa-solid fa-laptop-file"></i>
        </div>
        <div>
          <h3 class="modal-title">Ajukan Pinjam Buku Online</h3>
          <p class="modal-subtitle">Isi rencana tanggal ambil dan durasi pinjam buku ini</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger">&times;</button>
    </div>

    <!-- Book Selected Preview Box -->
    <div class="loan-detail-card" style="margin-bottom: 18px;">
      <div style="display: flex; gap: 14px; align-items: center;">
        <div style="width: 46px; height: 56px; border-radius: 8px; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-flat-sm);">
          <img id="reqBookCover" src="" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div>
          <div id="reqBookTitle" style="font-weight: 800; font-size: 14.5px; color: var(--text-main); line-height: 1.3;">-</div>
          <div id="reqBookAuthor" style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">-</div>
          <div id="reqBookCategory" style="font-size: 11px; font-weight: 700; color: #1d4ed8; margin-top: 4px;">-</div>
        </div>
      </div>
    </div>

    <!-- Form Pengajuan -->
    <form action="{{ route('member.pinjam-online.request') }}" method="POST">
      @csrf
      <input type="hidden" name="book_id" id="reqBookIdHidden">

      <!-- Tanggal Rencana Ambil -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" for="reqDatePick">Tanggal Rencana Pengambilan Buku <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="date" name="borrow_date" id="reqDatePick" class="form-input" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}" required>
        </div>
        <span style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px; display: block;">
          Maksimal 2 hari setelah tanggal pengajuan disetujui.
        </span>
      </div>

      <!-- Durasi Pinjam -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" for="reqDuration">Durasi Peminjaman <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box select-wrapper">
          <select name="duration_days" id="reqDuration" class="form-input">
            <option value="7" selected>7 Hari (Standar Perpustakaan Umum)</option>
            <option value="3">3 Hari (Masa Singkat)</option>
            <option value="14">14 Hari (Dua Minggu)</option>
          </select>
        </div>
      </div>

      <!-- Nomor Telepon / WA Konfirmasi -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label" for="reqMemberPhone">Nomor WhatsApp Aktif untuk Konfirmasi <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="tel" name="phone" id="reqMemberPhone" class="form-input" value="{{ $user->phone }}" placeholder="Contoh: 0812-3456-7890" required>
        </div>
      </div>

      <!-- Catatan Tambahan -->
      <div class="form-group" style="margin-bottom: 22px;">
        <label class="form-label" for="reqMemberNotes">Catatan untuk Pustakawan (Opsional)</label>
        <div class="input-inset-box">
          <textarea name="notes" id="reqMemberNotes" class="form-input" rows="2" placeholder="Contoh: Tolong siapkan jilid cetakan terbaru jika ada..."></textarea>
        </div>
      </div>

      <div class="modal-footer" style="padding-top: 0; border-top: none;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 24px;">
          <i class="fa-solid fa-paper-plane"></i>
          <span>Kirim Pengajuan</span>
        </button>
      </div>

    </form>
  </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabCatalogView = document.getElementById('tabCatalogView');
  const tabHistoryView = document.getElementById('tabHistoryView');
  const sectionCatalogView = document.getElementById('sectionCatalogView');
  const sectionHistoryView = document.getElementById('sectionHistoryView');

  const modalRequestLoan = document.getElementById('modalRequestLoan');
  const reqBookIdHidden = document.getElementById('reqBookIdHidden');
  const reqBookCover = document.getElementById('reqBookCover');
  const reqBookTitle = document.getElementById('reqBookTitle');
  const reqBookAuthor = document.getElementById('reqBookAuthor');
  const reqBookCategory = document.getElementById('reqBookCategory');

  // Tab Toggle
  if (tabCatalogView && tabHistoryView) {
    tabCatalogView.addEventListener('click', () => {
      tabCatalogView.classList.add('active');
      tabHistoryView.classList.remove('active');
      sectionCatalogView.style.display = 'block';
      sectionHistoryView.style.display = 'none';
    });

    tabHistoryView.addEventListener('click', () => {
      tabHistoryView.classList.add('active');
      tabCatalogView.classList.remove('active');
      sectionCatalogView.style.display = 'none';
      sectionHistoryView.style.display = 'block';
    });
  }

  // Open Modal
  document.querySelectorAll('.btn-open-request-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      @if($user->isSuspended())
        alert('Akses Ditolak: Akun Anda sedang ditangguhkan oleh Superadmin.');
        return;
      @endif

      reqBookIdHidden.value = btn.getAttribute('data-id');
      reqBookTitle.textContent = btn.getAttribute('data-title');
      reqBookAuthor.textContent = btn.getAttribute('data-author');
      reqBookCategory.textContent = btn.getAttribute('data-category');
      reqBookCover.src = btn.getAttribute('data-cover');

      modalRequestLoan.showModal();
    });
  });
});
</script>
@endpush

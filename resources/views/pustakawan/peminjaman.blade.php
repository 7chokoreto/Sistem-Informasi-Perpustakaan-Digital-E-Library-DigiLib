@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian Buku')

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
  
  <!-- Header Actions -->
  <div style="margin-bottom: 26px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
      <i class="fa-solid fa-arrow-right-arrow-left"></i> Layanan Sirkulasi
    </span>
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 2px;">
      <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.15;">Peminjaman &amp; Pengembalian Buku</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Catat transaksi peminjaman anggota, verifikasi pengajuan online, pantau jatuh tempo, dan proses pengembalian buku.</p>
      </div>
      <button class="btn-pill btn-pill-primary" id="btnOpenAddLoan" style="box-shadow: 4px 6px 16px var(--primary-glow); padding: 12px 22px; flex-shrink: 0;">
        <i class="fa-solid fa-plus"></i>
        <span>+ Catat Peminjaman Baru</span>
      </button>
    </div>
  </div>

  <!-- ================= TOOLBAR & FILTER SECTION ================= -->
  <div class="card-neumorphic" style="padding: 20px 24px; margin-bottom: 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      
      <!-- Status Tabs -->
      <div class="filter-tabs-container">
        <div class="filter-tab loan-filter-tab active" data-status="all">
          <i class="fa-solid fa-layer-group"></i>
          <span>Semua Transaksi</span>
        </div>
        <div class="filter-tab loan-filter-tab" data-status="Menunggu Validasi" style="position: relative;">
          <i class="fa-solid fa-inbox" style="color: #f59e0b;"></i>
          <span>Validasi Online</span>
          @if($pendingCount > 0)
            <span id="countPendingValidation" style="display: inline-flex; align-items: center; justify-content: center; background: #f59e0b; color: #fff; font-size: 11px; font-weight: 800; border-radius: 12px; padding: 2px 7px; margin-left: 6px; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4);">{{ $pendingCount }}</span>
          @endif
        </div>
        <div class="filter-tab loan-filter-tab" data-status="Disetujui">
          <i class="fa-solid fa-box-open" style="color: #0284c7;"></i>
          <span>Siap Diambil</span>
        </div>
        <div class="filter-tab loan-filter-tab" data-status="Dipinjam">
          <i class="fa-solid fa-clock"></i>
          <span>Sedang Dipinjam</span>
        </div>
        <div class="filter-tab loan-filter-tab" data-status="Dikembalikan">
          <i class="fa-solid fa-circle-check"></i>
          <span>Selesai (Kembali)</span>
        </div>
      </div>

      <!-- Search Box & Count -->
      <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <div class="input-inset-box" style="width: 280px;">
          <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
          <input type="text" id="loanSearchInput" placeholder="Cari nama, buku, ID transaksi..." style="padding: 10px 14px 10px 38px; font-size: 13px;">
        </div>
        <div style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 9px 16px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-md); box-shadow: var(--shadow-flat-sm); white-space: nowrap;" id="totalLoanCount">
          {{ count($loans) }} Transaksi
        </div>
      </div>

    </div>
  </div>

  <!-- ================= LOANS TABLE CARD ================= -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 8px;">
        <thead>
          <tr>
            <th style="width: 15%;">No. Transaksi</th>
            <th style="width: 21%;">Peminjam (Anggota)</th>
            <th style="width: 23%;">Buku Dipinjam</th>
            <th style="width: 15%;">Jadwal Pinjam</th>
            <th style="width: 13%;">Status</th>
            <th style="width: 13%; text-align: center;">Tindakan</th>
          </tr>
        </thead>
        <tbody id="loanTableBody">
          @forelse($loans as $loan)
            @php
              $isOverdue = ($loan->status === 'Dipinjam') && now()->gt(\Carbon\Carbon::parse($loan->due_date));
              $overdueDays = $isOverdue ? now()->diffInDays(\Carbon\Carbon::parse($loan->due_date)) : 0;
              $estimatedFine = $overdueDays * 1000;
            @endphp
            <tr class="loan-row" 
                data-code="{{ strtolower($loan->trx_code) }}"
                data-member="{{ strtolower($loan->user ? $loan->user->name : '') }}"
                data-book="{{ strtolower($loan->book ? $loan->book->title : '') }}"
                data-status="{{ $isOverdue ? 'Terlambat' : $loan->status }}">
              <td>
                <span style="font-family: monospace; font-weight: 700; color: #1d4ed8;">{{ $loan->trx_code }}</span>
                @if($loan->type === 'online')
                  <span style="font-size: 10px; background: rgba(37,99,235,0.1); color: #2563eb; padding: 2px 6px; border-radius: 6px; font-weight: 700; display: inline-block; margin-left: 4px;">Online</span>
                @else
                  <span style="font-size: 10px; background: rgba(100,116,139,0.1); color: #64748b; padding: 2px 6px; border-radius: 6px; font-weight: 700; display: inline-block; margin-left: 4px;">Di Tempat</span>
                @endif
              </td>
              <td>
                <div style="font-weight: 700; color: var(--text-main);">{{ $loan->user ? $loan->user->name : 'Anggota Tidak Dikenal' }}</div>
                @if($loan->user && $loan->user->phone)
                  <div style="font-size: 11.5px; color: var(--text-muted);"><i class="fa-brands fa-whatsapp"></i> {{ $loan->user->phone }}</div>
                @endif
              </td>
              <td>
                <div style="font-weight: 600;">{{ $loan->book ? $loan->book->title : 'Buku Dihapus' }}</div>
                @if($loan->book)
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $loan->book->category }} &bull; {{ $loan->book->shelf }}</div>
                @endif
                @if($loan->pickup_location)
                  <div style="font-size: 10.5px; color: #1d4ed8; font-weight: 700; margin-top: 3px;">
                    <i class="fa-solid fa-map-pin"></i> {{ $loan->pickup_location }}
                  </div>
                @endif
              </td>
              <td>
                <div style="font-size: 12px; color: var(--text-main);">Pinjam: <b>{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d M Y') }}</b></div>
                <div style="font-size: 12px; color: {{ $isOverdue ? '#dc2626; font-weight: 700;' : 'var(--text-muted)' }};">
                  Tempo: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                </div>
              </td>
              <td>
                @if($loan->status === 'Menunggu Validasi')
                  <span class="loan-badge" style="background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Validasi
                  </span>
                @elseif($loan->status === 'Disetujui')
                  <span class="loan-badge" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.3);">
                    <i class="fa-solid fa-box-open"></i> Siap Diambil
                  </span>
                @elseif($loan->status === 'Dipinjam')
                  @if($isOverdue)
                    <span class="loan-badge terlambat">
                      <i class="fa-solid fa-triangle-exclamation"></i> Terlambat (+{{ $overdueDays }}h)
                    </span>
                  @else
                    <span class="loan-badge dipinjam">
                      <i class="fa-solid fa-clock"></i> Dipinjam
                    </span>
                  @endif
                @elseif($loan->status === 'Dikembalikan')
                  <span class="loan-badge kembali">
                    <i class="fa-solid fa-circle-check"></i> Selesai
                  </span>
                  @if($loan->fine > 0)
                    <div style="font-size: 11px; color: #dc2626; font-weight: 700; margin-top: 2px;">Denda Rp {{ number_format($loan->fine, 0, ',', '.') }}</div>
                  @endif
                @elseif($loan->status === 'Ditolak')
                  <span class="loan-badge" style="background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);" title="{{ $loan->rejection_reason }}">
                    <i class="fa-solid fa-ban"></i> Ditolak
                  </span>
                @else
                  <span class="loan-badge">{{ $loan->status }}</span>
                @endif
              </td>
              <td style="text-align: center;">
                @if($loan->status === 'Menunggu Validasi')
                  <div style="display: flex; gap: 6px; justify-content: center;">
                    <button type="button" class="btn-icon-action btn-approve-online" 
                            style="color: #059669;"
                            data-id="{{ $loan->id }}"
                            data-code="{{ $loan->trx_code }}"
                            data-member="{{ $loan->user ? $loan->user->name : '-' }}"
                            data-book="{{ $loan->book ? $loan->book->title : '-' }}"
                            data-duration="{{ $loan->duration_days }} Hari"
                            title="Setujui Pinjam Online">
                      <i class="fa-solid fa-check"></i>
                    </button>
                    <button type="button" class="btn-icon-action danger btn-reject-online" 
                            data-id="{{ $loan->id }}"
                            data-code="{{ $loan->trx_code }}"
                            data-member="{{ $loan->user ? $loan->user->name : '-' }}"
                            title="Tolak Pengajuan">
                      <i class="fa-solid fa-xmark"></i>
                    </button>
                  </div>
                @elseif($loan->status === 'Disetujui')
                  <form action="{{ route('pustakawan.loans.handover', $loan->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-pill btn-pill-info" style="padding: 6px 12px; font-size: 11.5px; background: #0284c7; color: #ffffff !important; border: none; box-shadow: none;">
                      <i class="fa-solid fa-hand-holding-hand" style="color: #ffffff !important;"></i>
                      <span style="color: #ffffff !important; font-weight: 700;">Serahkan Buku</span>
                    </button>
                  </form>
                @elseif($loan->status === 'Dipinjam')
                  <button type="button" class="btn-pill btn-pill-success btn-return-book" 
                          data-id="{{ $loan->id }}"
                          data-code="{{ $loan->trx_code }}"
                          data-member="{{ $loan->user ? $loan->user->name : '-' }}"
                          data-book="{{ $loan->book ? $loan->book->title : '-' }}"
                          data-due="{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}"
                          data-overdue="{{ $isOverdue ? 1 : 0 }}"
                          data-fine="{{ $estimatedFine }}"
                          style="padding: 6px 12px; font-size: 11.5px; background: #059669; color: #ffffff !important; border: none; box-shadow: none;">
                    <i class="fa-solid fa-rotate-left" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important; font-weight: 700;">Terima Kembali</span>
                  </button>
                @else
                  <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-check"></i> Arsip</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size: 32px; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                Belum ada data sirkulasi peminjaman buku.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ================= MODAL CATAT PEMINJAMAN BARU ================= -->
<dialog class="modal-custom" id="modalAddLoan" style="max-width: 540px;">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-header-left">
        <div class="modal-icon-badge">
          <i class="fa-solid fa-plus"></i>
        </div>
        <div class="modal-title-wrap">
          <h3>Catat Peminjaman Buku</h3>
          <p>Daftarkan peminjaman koleksi buku fisik di meja pelayanan perpustakaan.</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form action="{{ route('pustakawan.loans.store') }}" method="POST" id="formAddLoan">
      @csrf
      <div class="modal-form-grid">
        
        <div>
          <label class="input-label-clean">Pilih Anggota Terdaftar <span class="required">*</span></label>
          <div class="input-inset-box select-wrapper">
            <i class="fa-solid fa-user input-icon-left"></i>
            <select name="user_id" required>
              <option value="">-- Pilih Anggota --</option>
              @foreach($members as $m)
                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <div>
          <label class="input-label-clean">Pilih Buku yang Dipinjam <span class="required">*</span></label>
          <div class="input-inset-box select-wrapper">
            <i class="fa-solid fa-book input-icon-left"></i>
            <select name="book_id" required>
              <option value="">-- Pilih Buku Tersedia --</option>
              @foreach($availableBooks as $b)
                <option value="{{ $b->id }}">{{ $b->title }} (Stok: {{ $b->stock }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label class="input-label-clean">Tanggal Pinjam <span class="required">*</span></label>
            <div class="input-inset-box">
              <i class="fa-solid fa-calendar input-icon-left"></i>
              <input type="date" name="borrow_date" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          <div>
            <label class="input-label-clean">Durasi Peminjaman <span class="required">*</span></label>
            <div class="input-inset-box select-wrapper">
              <i class="fa-solid fa-hourglass-half input-icon-left"></i>
              <select name="duration_days" required>
                <option value="7">7 Hari (Standar)</option>
                <option value="14">14 Hari (Perpanjangan)</option>
                <option value="3">3 Hari (Koleksi Khusus)</option>
              </select>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer" style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 24px;">
          <i class="fa-solid fa-check"></i>
          <span>Simpan Peminjaman</span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- ================= MODAL KONFIRMASI PENGEMBALIAN BUKU ================= -->
<dialog class="modal-custom" id="modalReturnBook" style="max-width: 480px;">
  <div class="modal-content" style="padding: 34px 28px;">
    <div style="text-align: center; margin-bottom: 20px;">
      <div class="modal-icon-badge" style="width: 56px; height: 56px; font-size: 24px; color: #059669; margin: 0 auto 16px auto;">
        <i class="fa-solid fa-rotate-left"></i>
      </div>
      <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Konfirmasi Pengembalian Buku</h3>
      <p style="font-size: 13px; color: var(--text-muted);">Pastikan buku fisik telah diterima oleh petugas dalam kondisi baik.</p>
    </div>

    <div class="loan-detail-card" style="margin-bottom: 18px;">
      <div class="loan-detail-row">
        <span class="loan-detail-label">ID Transaksi</span>
        <span class="loan-detail-value" id="returnTrxId">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Nama Anggota</span>
        <span class="loan-detail-value" id="returnMemberName">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Judul Buku</span>
        <span class="loan-detail-value" id="returnBookTitle">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Batas Jatuh Tempo</span>
        <span class="loan-detail-value" id="returnDueDate">-</span>
      </div>
    </div>

    <div id="returnFineBox" style="display: none; padding: 14px; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 20px; text-align: center;">
      <div style="font-size: 12px; font-weight: 700; color: #dc2626; margin-bottom: 2px;">
        <i class="fa-solid fa-triangle-exclamation"></i> Keterlambatan Pengembalian
      </div>
      <div style="font-size: 16px; font-weight: 800; color: #dc2626;">
        Denda: <span id="returnFineAmount">Rp 0</span>
      </div>
      <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
        Denda terhitung Rp 1.000 / hari keterlambatan.
      </div>
    </div>

    <form method="POST" id="formReturnBook">
      @csrf
      @method('PATCH')
      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-pill btn-pill-success" style="min-width: 150px; justify-content: center; text-align: center; background: #059669; color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);">
          <i class="fa-solid fa-check" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Terima Buku</span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- ================= MODAL PERSETUJUAN PINJAM ONLINE ================= -->
<dialog class="modal-custom" id="modalApproveOnlineLoan" style="max-width: 480px;">
  <div class="modal-content" style="padding: 34px 28px;">
    <div style="text-align: center; margin-bottom: 20px;">
      <div class="modal-icon-badge" style="width: 56px; height: 56px; font-size: 24px; color: #059669; margin: 0 auto 16px auto;">
        <i class="fa-solid fa-file-circle-check"></i>
      </div>
      <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Setujui Pinjam Online</h3>
      <p style="font-size: 13px; color: var(--text-muted);">Setujui pengajuan peminjaman mandiri anggota untuk disiapkan buku fisiknya.</p>
    </div>

    <div class="loan-detail-card" style="margin-bottom: 20px;">
      <div class="loan-detail-row">
        <span class="loan-detail-label">No. Pengajuan</span>
        <span class="loan-detail-value" id="approveTrxId">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Nama Anggota</span>
        <span class="loan-detail-value" id="approveMemberName">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Judul Buku</span>
        <span class="loan-detail-value" id="approveBookTitle">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Durasi Pinjam</span>
        <span class="loan-detail-value" id="approveDuration">-</span>
      </div>
    </div>

    <form method="POST" id="formApproveOnline">
      @csrf
      @method('PATCH')
      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-pill btn-pill-success" style="min-width: 150px; justify-content: center; text-align: center; background: #059669; color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);">
          <i class="fa-solid fa-check" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Ya, Setujui</span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- ================= MODAL PENOLAKAN PINJAM ONLINE ================= -->
<dialog class="modal-custom" id="modalRejectOnlineLoan" style="max-width: 480px;">
  <div class="modal-content" style="padding: 34px 28px;">
    <div style="text-align: center; margin-bottom: 20px;">
      <div class="modal-icon-badge danger" style="width: 56px; height: 56px; font-size: 24px; margin: 0 auto 16px auto;">
        <i class="fa-solid fa-ban"></i>
      </div>
      <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Tolak Pengajuan Pinjam</h3>
      <p style="font-size: 13px; color: var(--text-muted);">Berikan alasan penolakan agar member mengetahui kendala peminjaman.</p>
    </div>

    <div class="loan-detail-card" style="margin-bottom: 16px;">
      <div class="loan-detail-row">
        <span class="loan-detail-label">No. Pengajuan</span>
        <span class="loan-detail-value" id="rejectTrxId">-</span>
      </div>
      <div class="loan-detail-row">
        <span class="loan-detail-label">Peminjam</span>
        <span class="loan-detail-value" id="rejectMemberName">-</span>
      </div>
    </div>

    <form method="POST" id="formRejectOnline">
      @csrf
      @method('PATCH')
      <div class="form-group" style="margin-bottom: 20px; text-align: left;">
        <label class="form-label" for="rejectReasonSelect">Pilih Alasan Penolakan</label>
        <div class="input-inset-box select-wrapper" style="margin-bottom: 8px;">
          <select id="rejectReasonSelect" name="rejection_reason" class="form-input">
            <option value="Stok fisik buku saat ini sedang dalam perawatan/rusak">Stok fisik buku sedang dalam perbaikan</option>
            <option value="Buku telah dipinjam di tempat oleh anggota lain">Buku telah dipinjam di tempat oleh anggota lain</option>
            <option value="Batas maksimal peminjaman aktif Anda telah tercapai">Batas peminjaman aktif telah tercapai</option>
            <option value="custom">Alasan Lainnya...</option>
          </select>
        </div>
        <div class="input-inset-box" id="rejectCustomReasonWrap" style="display: none;">
          <textarea id="rejectCustomReasonText" class="form-input" rows="2" placeholder="Tuliskan alasan penolakan..."></textarea>
        </div>
      </div>

      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-danger-action" style="min-width: 140px; justify-content: center; background: #dc2626; color: #ffffff !important; border: none;">
          <i class="fa-solid fa-xmark" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Tolak Pengajuan</span>
        </button>
      </div>
    </form>
  </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalAdd = document.getElementById('modalAddLoan');
  const modalReturn = document.getElementById('modalReturnBook');
  const modalApprove = document.getElementById('modalApproveOnlineLoan');
  const modalReject = document.getElementById('modalRejectOnlineLoan');

  // Open Add Modal
  document.getElementById('btnOpenAddLoan')?.addEventListener('click', () => {
    modalAdd?.showModal();
  });

  // Open Return Modal
  document.querySelectorAll('.btn-return-book').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      document.getElementById('formReturnBook').action = `/pustakawan/peminjaman/${id}/return`;
      document.getElementById('returnTrxId').textContent = btn.getAttribute('data-code');
      document.getElementById('returnMemberName').textContent = btn.getAttribute('data-member');
      document.getElementById('returnBookTitle').textContent = btn.getAttribute('data-book');
      document.getElementById('returnDueDate').textContent = btn.getAttribute('data-due');

      const isOverdue = btn.getAttribute('data-overdue') === '1';
      const fine = parseInt(btn.getAttribute('data-fine') || '0', 10);
      const fineBox = document.getElementById('returnFineBox');
      if (isOverdue && fine > 0) {
        fineBox.style.display = 'block';
        document.getElementById('returnFineAmount').textContent = 'Rp ' + fine.toLocaleString('id-ID');
      } else {
        fineBox.style.display = 'none';
      }

      modalReturn?.showModal();
    });
  });

  // Open Approve Modal
  document.querySelectorAll('.btn-approve-online').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      document.getElementById('formApproveOnline').action = `/pustakawan/peminjaman/${id}/approve`;
      document.getElementById('approveTrxId').textContent = btn.getAttribute('data-code');
      document.getElementById('approveMemberName').textContent = btn.getAttribute('data-member');
      document.getElementById('approveBookTitle').textContent = btn.getAttribute('data-book');
      document.getElementById('approveDuration').textContent = btn.getAttribute('data-duration');
      modalApprove?.showModal();
    });
  });

  // Open Reject Modal
  document.querySelectorAll('.btn-reject-online').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      document.getElementById('formRejectOnline').action = `/pustakawan/peminjaman/${id}/reject`;
      document.getElementById('rejectTrxId').textContent = btn.getAttribute('data-code');
      document.getElementById('rejectMemberName').textContent = btn.getAttribute('data-member');
      modalReject?.showModal();
    });
  });

  // Handle custom reject reason
  const reasonSelect = document.getElementById('rejectReasonSelect');
  const customReasonWrap = document.getElementById('rejectCustomReasonWrap');
  const customReasonText = document.getElementById('rejectCustomReasonText');
  reasonSelect?.addEventListener('change', () => {
    if (reasonSelect.value === 'custom') {
      customReasonWrap.style.display = 'block';
      customReasonText.setAttribute('name', 'rejection_reason');
      reasonSelect.removeAttribute('name');
    } else {
      customReasonWrap.style.display = 'none';
      reasonSelect.setAttribute('name', 'rejection_reason');
      customReasonText.removeAttribute('name');
    }
  });

  // Close triggers
  document.querySelectorAll('.modal-close-trigger').forEach(trigger => {
    trigger.addEventListener('click', () => {
      modalAdd?.close();
      modalReturn?.close();
      modalApprove?.close();
      modalReject?.close();
    });
  });

  // Client-side Filter Tabs & Search
  const searchInput = document.getElementById('loanSearchInput');
  const tabs = document.querySelectorAll('.loan-filter-tab');
  const rows = document.querySelectorAll('.loan-row');
  const countEl = document.getElementById('totalLoanCount');
  let currentStatus = 'all';

  function filterLoans() {
    const q = (searchInput?.value || '').trim().toLowerCase();
    let visibleCount = 0;

    rows.forEach(row => {
      const code = row.getAttribute('data-code');
      const member = row.getAttribute('data-member');
      const book = row.getAttribute('data-book');
      const status = row.getAttribute('data-status');

      const matchesSearch = !q || code.includes(q) || member.includes(q) || book.includes(q);
      const matchesStatus = currentStatus === 'all' || status === currentStatus;

      if (matchesSearch && matchesStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (countEl) {
      countEl.textContent = `${visibleCount} Transaksi`;
    }
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentStatus = tab.getAttribute('data-status');
      filterLoans();
    });
  });

  searchInput?.addEventListener('input', filterLoans);
});
</script>
@endpush
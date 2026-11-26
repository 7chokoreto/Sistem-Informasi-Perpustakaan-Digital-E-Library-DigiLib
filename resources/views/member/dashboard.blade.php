@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')
<div class="content-body" style="padding-top: 32px;">
  
  <!-- ================= SUSPENSION ALERT BANNER ================= -->
  @if($user->isSuspended())
    <div class="suspension-banner" id="suspensionAlertBanner">
      <div class="suspension-banner-icon">
        <i class="fa-solid fa-ban"></i>
      </div>
      <div style="flex-grow: 1;">
        <div class="suspension-banner-title">Akun Anda Sedang Ditangguhkan oleh Superadmin</div>
        <div class="suspension-banner-text">
          Status keanggotaan Anda saat ini dinonaktifkan sementara sehingga tidak dapat melakukan peminjaman buku fisik maupun pengajuan pinjam online.
        </div>
        <div class="suspension-banner-reason">
          <i class="fa-solid fa-circle-info"></i> Alasan: <span>{{ $user->suspension_reason ?: 'Pelanggaran batas waktu pengembalian buku dan tata tertib umum perpustakaan.' }}</span>
        </div>
      </div>
      <a href="{{ route('member.akun') }}" class="btn-pill btn-pill-danger" style="background: #dc2626; color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); font-size: 12px; padding: 8px 16px; white-space: nowrap; text-decoration: none;">
        <span style="color: #ffffff !important; font-weight: 700;">Lihat Akun</span>
        <i class="fa-solid fa-arrow-right" style="color: #ffffff !important;"></i>
      </a>
    </div>
  @endif

  <!-- Welcome Banner -->
  <div class="card-neumorphic" style="padding: 26px 30px; margin-bottom: 24px; position: relative; overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div style="max-width: 600px;">
        <span class="badge-role superadmin" style="margin-bottom: 10px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
          <i class="fa-solid fa-id-card"></i> Anggota Resmi Perpustakaan
        </span>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
          Selamat Datang, <span style="color: #1d4ed8;">{{ $user->name }}</span>!
        </h1>
        <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">
          Jelajahi ribuan koleksi buku bacaan, pinjam secara online dari rumah, dan pantau masa pengembalian Anda dengan mudah di satu tempat.
        </p>
      </div>
      <div>
        <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="padding: 13px 24px; font-size: 14px; box-shadow: 4px 6px 16px var(--primary-glow); text-decoration: none;">
          <i class="fa-solid fa-book-bookmark"></i>
          <span>Jelajahi Koleksi Buku</span>
        </a>
      </div>
    </div>
  </div>

  <!-- ================= STAT CARDS ================= -->
  <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
    <!-- Card 1: Sedang Dipinjam -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Buku Dipinjam</span>
        <span class="stat-value">{{ $activeLoansCount }}</span>
        <span class="stat-meta">Sedang Anda bawa pulang</span>
      </div>
      <div class="stat-icon-wrapper icon-blue">
        <i class="fa-solid fa-book-bookmark"></i>
      </div>
    </div>

    <!-- Card 2: Menunggu Validasi -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Menunggu Validasi</span>
        <span class="stat-value" style="color: #b45309;">{{ $pendingValidationCount }}</span>
        <span class="stat-meta">Pengajuan pinjam online</span>
      </div>
      <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
    </div>

    <!-- Card 3: Total Selesai -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Riwayat Selesai</span>
        <span class="stat-value">{{ $completedLoansCount }}</span>
        <span class="stat-meta">Buku telah dikembalikan</span>
      </div>
      <div class="stat-icon-wrapper icon-teal">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>

    <!-- Card 4: Status Keanggotaan -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Status Akun</span>
        <span class="stat-value" style="font-size: 20px; color: {{ $user->isSuspended() ? '#dc2626' : '#059669' }};">
          {{ $user->isSuspended() ? 'Ditangguhkan' : 'Aktif' }}
        </span>
        <span class="stat-meta">
          {{ $user->isSuspended() ? 'Akses peminjaman dibekukan' : 'Bebas meminjam buku' }}
        </span>
      </div>
      <div class="stat-icon-wrapper {{ $user->isSuspended() ? 'icon-red' : 'icon-purple' }}">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
    </div>
  </div>

  <!-- ================= MAIN TWO-COLUMN CONTENT ================= -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    
    <!-- Left Column: Aktivitas Peminjaman Saya -->
    <div class="card-neumorphic" style="padding: 26px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
          <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main);">Peminjaman &amp; Pengajuan Aktif</h2>
          <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Daftar buku yang sedang Anda bawa dan status verifikasi permohonan pinjam.</p>
        </div>
        <a href="{{ route('member.aktifitas') }}" style="font-size: 13px; font-weight: 700; color: #1d4ed8; text-decoration: none;">
          Lihat Semua Aktifitas &rarr;
        </a>
      </div>

      <div class="table-responsive">
        <table class="custom-table" style="border-spacing: 0 8px;">
          <thead>
            <tr>
              <th style="width: 25%;">ID Transaksi</th>
              <th style="width: 35%;">Judul Buku</th>
              <th style="width: 20%;">Batas Tempo</th>
              <th style="width: 20%; text-align: center;">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentLoans as $loan)
              <tr>
                <td>
                  <span style="font-family: monospace; font-size: 12px; font-weight: 700; color: #1d4ed8;">{{ $loan->trx_code }}</span>
                  @if($loan->type === 'online')
                    <span style="display: block; font-size: 10px; color: #f59e0b; font-weight: 700; margin-top: 2px;"><i class="fa-solid fa-wifi"></i> Online</span>
                  @endif
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">{{ $loan->book->title ?? '-' }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $loan->book->category ?? '-' }}</div>
                </td>
                <td>
                  <div style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">{{ $loan->due_date }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">
                    {{ $loan->status === 'Dikembalikan' ? 'Kembali: ' . $loan->return_date : ($loan->status === 'Menunggu Validasi' ? 'Rencana Ambil' : 'Batas Tempo') }}
                  </div>
                </td>
                <td style="text-align: center;">
                  <span class="loan-badge {{ $loan->status === 'Dipinjam' ? 'dipinjam' : ($loan->status === 'Dikembalikan' ? 'kembali' : ($loan->status === 'Terlambat' ? 'terlambat' : 'pending-validation')) }}">
                    <i class="fa-solid {{ $loan->status === 'Dipinjam' ? 'fa-clock' : ($loan->status === 'Dikembalikan' ? 'fa-check-double' : ($loan->status === 'Terlambat' ? 'fa-triangle-exclamation' : 'fa-inbox')) }}"></i>
                    {{ $loan->status }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                  <i class="fa-solid fa-book-open" style="font-size: 32px; opacity: 0.35; margin-bottom: 10px; display: block;"></i>
                  <p style="font-size: 13.5px; font-weight: 600;">Belum ada riwayat peminjaman.</p>
                  <a href="{{ route('member.koleksi') }}" style="font-size: 13px; color: #1d4ed8; font-weight: 700; margin-top: 6px; display: inline-block;">Jelajahi Koleksi Buku &rarr;</a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right Column: Informasi Layanan Fisik -->
    <div style="display: flex; flex-direction: column; gap: 22px;">
      
      <!-- Jam Layanan Fisik -->
      <div class="card-neumorphic" style="padding: 22px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
          <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 15px;">
            <i class="fa-solid fa-clock"></i>
          </div>
          <h3 style="font-size: 15px; font-weight: 800; color: var(--text-main);">Jam Layanan Fisik</h3>
        </div>
        <div style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>Senin - Jumat:</span>
            <b style="color: var(--text-main);">08.00 - 17.00 WIB</b>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>Sabtu:</span>
            <b style="color: var(--text-main);">09.00 - 15.00 WIB</b>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>Minggu / Libur:</span>
            <b style="color: #dc2626;">Tutup</b>
          </div>
        </div>

        <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(166, 180, 200, 0.25); font-size: 12.5px; color: var(--text-muted); line-height: 1.5;">
          <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i> Pengambilan buku yang telah disetujui dapat dilakukan di meja resepsionis perpustakaan.
        </div>
      </div>

    </div>

  </div>

  <!-- ================= REKOMENDASI BUKU POPULER ================= -->
  @if($recommendedBooks->isNotEmpty())
    <div style="margin-top: 36px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: #1d4ed8;"></span>
          Koleksi Buku Rekomendasi
        </h2>
        <a href="{{ route('member.koleksi') }}" style="font-size: 13px; font-weight: 700; color: #1d4ed8; text-decoration: none;">
          Lihat Semua Koleksi &rarr;
        </a>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 18px;">
        @foreach($recommendedBooks as $rb)
          <a href="{{ route('member.buku.detail', $rb->id) }}" class="card-neumorphic" style="padding: 14px; border-radius: 16px; text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 100%; height: 230px; border-radius: 10px; overflow: hidden; background: #e2e8f0; margin-bottom: 10px;">
              @if($rb->cover)
                <img src="{{ $rb->cover }}" alt="{{ $rb->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #94a3b8; color: #fff;">
                  <i class="fa-solid fa-book" style="font-size: 32px;"></i>
                </div>
              @endif
            </div>
            <div style="font-size: 11px; font-weight: 700; color: var(--primary); margin-bottom: 3px;">
              {{ $rb->category ?: 'Umum' }}
            </div>
            <h4 style="font-size: 13.5px; font-weight: 800; color: var(--text-main); line-height: 1.3; height: 34px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 4px;">
              {{ $rb->title }}
            </h4>
            <p style="font-size: 12px; color: var(--text-muted); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              {{ $rb->author }}
            </p>
          </a>
        @endforeach
      </div>
    </div>
  @endif

</div>
@endsection

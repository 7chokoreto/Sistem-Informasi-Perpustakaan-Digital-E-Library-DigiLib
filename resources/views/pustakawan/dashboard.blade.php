@extends('layouts.app')

@section('title', 'Dashboard Pustakawan')

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

<!-- Dashboard Main Body Content -->
<div class="content-body">
  
  <!-- Welcome Banner & Title Section -->
  <div class="page-header" style="margin-bottom: 24px;">
    <div class="header-left">
      <span class="badge-role superadmin" style="background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2); margin-bottom: 8px; display: inline-flex;">
        <i class="fa-solid fa-book-reader"></i> Layanan Perpustakaan Umum
      </span>
      <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 2px;">
        <div>
          <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.15;">Dashboard Pustakawan</h1>
          <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Pantau ketersediaan buku, aktivitas peminjaman harian, dan pengembalian anggota secara real-time.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-shrink: 0;">
          <a href="{{ route('pustakawan.loans') }}" class="btn-pill btn-pill-primary" style="text-decoration: none; padding: 12px 20px; box-shadow: 4px 6px 16px var(--primary-glow);">
            <i class="fa-solid fa-plus"></i>
            <span>Catat Peminjaman</span>
          </a>
          <a href="{{ route('pustakawan.books') }}" class="btn-secondary-flat" style="text-decoration: none; padding: 12px 20px;">
            <i class="fa-solid fa-book"></i>
            <span>Katalog Buku</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- 4 Key Statistic Cards (Neumorphic) -->
  <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
    
    <!-- Card 1: Total Koleksi Buku -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Total Koleksi Buku</span>
        <span class="stat-value">{{ $totalBooks }} Judul</span>
        <span class="stat-meta">{{ $totalStock }} total eksemplar fisik</span>
      </div>
      <div class="stat-icon-wrapper icon-blue">
        <i class="fa-solid fa-book-bookmark"></i>
      </div>
    </div>

    <!-- Card 2: Buku Sedang Dipinjam -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Sedang Dipinjam</span>
        <span class="stat-value">{{ $activeLoans }}</span>
        <span class="stat-meta">Buku aktif berada di anggota</span>
      </div>
      <div class="stat-icon-wrapper" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
        <i class="fa-solid fa-hand-holding-hand"></i>
      </div>
    </div>

    <!-- Card 3: Peminjaman Hari Ini -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Peminjaman Hari Ini</span>
        <span class="stat-value">{{ $todayLoans }}</span>
        <span class="stat-meta">Transaksi tercatat hari ini</span>
      </div>
      <div class="stat-icon-wrapper icon-teal">
        <i class="fa-solid fa-calendar-day"></i>
      </div>
    </div>

    <!-- Card 4: Menunggu Validasi Online -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Validasi Online</span>
        <span class="stat-value" style="color: {{ $pendingValidations > 0 ? '#f59e0b' : '#059669' }};">
          {{ $pendingValidations }}
        </span>
        <span class="stat-meta">{{ $pendingValidations > 0 ? 'Perlu tindakan verifikasi' : 'Tidak ada antrean validasi' }}</span>
      </div>
      <div class="stat-icon-wrapper" style="background: {{ $pendingValidations > 0 ? 'rgba(245, 158, 11, 0.12)' : 'rgba(5, 150, 105, 0.12)' }}; color: {{ $pendingValidations > 0 ? '#f59e0b' : '#059669' }};">
        <i class="fa-solid {{ $pendingValidations > 0 ? 'fa-inbox' : 'fa-circle-check' }}"></i>
      </div>
    </div>

  </div>

  <!-- Visual Analytics Grid (Bar Chart & Doughnut Chart) -->
  <div class="dashboard-grid-2" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px;">
    
    <!-- Chart 1: Tren Peminjaman & Pengembalian Mingguan -->
    <div class="card-neumorphic" style="padding: 24px 26px;">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <div>
          <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Aktivitas Peminjaman & Pengembalian</h3>
          <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Statistik sirkulasi buku mingguan (Senin - Minggu)</p>
        </div>
        <div style="display: flex; gap: 14px; font-size: 12px; font-weight: 600;">
          <div style="display: flex; align-items: center; gap: 6px;">
            <span style="width: 10px; height: 10px; border-radius: 2px; background: #1d4ed8;"></span>
            <span>Dipinjam</span>
          </div>
          <div style="display: flex; align-items: center; gap: 6px;">
            <span style="width: 10px; height: 10px; border-radius: 2px; background: #10b981;"></span>
            <span>Dikembalikan</span>
          </div>
        </div>
      </div>
      <div style="height: 270px; width: 100%;">
        <canvas id="chartWeeklyLoans"></canvas>
      </div>
    </div>

    <!-- Chart 2: Komposisi Kategori Buku -->
    <div class="card-neumorphic" style="padding: 24px 26px; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div class="card-header" style="margin-bottom: 14px;">
          <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Kategori Koleksi Buku</h3>
          <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Proporsi buku umum terdaftar</p>
        </div>
        <div style="height: 180px; width: 100%; position: relative;">
          <canvas id="chartBookCategory"></canvas>
        </div>
      </div>

      <!-- Breakdown list -->
      <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 14px; font-size: 12px;">
        @php
          $colors = ['#1d4ed8', '#0284c7', '#059669', '#d97706', '#7c3aed', '#ec4899'];
        @endphp
        @forelse($catLabels as $idx => $label)
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="display: flex; align-items: center; gap: 6px;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $colors[$idx % count($colors)] }};"></span> 
              {{ $label }}
            </span>
            <span style="font-weight: 700; color: var(--text-main);">{{ $catTotals[$idx] ?? 0 }} buku</span>
          </div>
        @empty
          <div style="color: var(--text-muted); text-align: center; font-size: 11px;">Belum ada kategori buku.</div>
        @endforelse
      </div>
    </div>

  </div>

  <!-- Recent Circulation Transactions Table -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <div>
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Peminjaman &amp; Pengembalian Terkini</h3>
        <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Aktivitas transaksi peminjaman terbaru anggota perpustakaan umum</p>
      </div>
      <a href="{{ route('pustakawan.loans') }}" class="btn-secondary-flat" style="text-decoration: none; font-size: 12.5px; padding: 8px 16px;">
        <span>Buka Menu Peminjaman</span>
        <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 8px;">
        <thead>
          <tr>
            <th style="width: 16%;">No. Transaksi</th>
            <th style="width: 24%;">Nama Anggota</th>
            <th style="width: 24%;">Judul Buku</th>
            <th style="width: 14%;">Tanggal Pinjam</th>
            <th style="width: 14%;">Batas Waktu</th>
            <th style="width: 14%;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentLoans as $loan)
            <tr>
              <td>
                <span style="font-family: monospace; font-weight: 700; color: #1d4ed8;">{{ $loan->trx_code }}</span>
                @if($loan->type === 'online')
                  <span style="font-size: 10px; background: rgba(37,99,235,0.1); color: #2563eb; padding: 2px 6px; border-radius: 6px; font-weight: 700; display: inline-block; margin-left: 4px;">Online</span>
                @endif
              </td>
              <td>
                <div style="font-weight: 700; color: var(--text-main);">{{ $loan->user ? $loan->user->name : 'Anggota Tamu' }}</div>
                @if($loan->user && $loan->user->phone)
                  <div style="font-size: 11.5px; color: var(--text-muted);"><i class="fa-brands fa-whatsapp"></i> {{ $loan->user->phone }}</div>
                @endif
              </td>
              <td>
                <div style="font-weight: 600;">{{ $loan->book ? $loan->book->title : 'Buku Dihapus' }}</div>
                @if($loan->book)
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $loan->book->category }}</div>
                @endif
              </td>
              <td>{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d M Y') }}</td>
              <td>
                @php
                  $isLate = ($loan->status === 'Dipinjam' || $loan->status === 'Terlambat') && now()->gt(\Carbon\Carbon::parse($loan->due_date));
                @endphp
                <span style="{{ $isLate ? 'color: #dc2626; font-weight: 700;' : '' }}">
                  {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                </span>
              </td>
              <td>
                @if($loan->status === 'Dipinjam')
                  <span class="loan-badge dipinjam"><i class="fa-solid fa-clock"></i> Dipinjam</span>
                @elseif($loan->status === 'Dikembalikan')
                  <span class="loan-badge kembali"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                @elseif($loan->status === 'Menunggu Validasi')
                  <span class="loan-badge pending" style="background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);"><i class="fa-solid fa-hourglass-half"></i> Menunggu Validasi</span>
                @elseif($loan->status === 'Disetujui')
                  <span class="loan-badge approved" style="background: rgba(5, 150, 105, 0.12); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);"><i class="fa-solid fa-box-open"></i> Siap Diambil</span>
                @elseif($loan->status === 'Ditolak')
                  <span class="loan-badge ditolak" style="background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);"><i class="fa-solid fa-ban"></i> Ditolak</span>
                @else
                  <span class="loan-badge terlambat"><i class="fa-solid fa-triangle-exclamation"></i> {{ $loan->status }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                Belum ada transaksi peminjaman tercatat.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Chart 1: Aktivitas Peminjaman & Pengembalian 7 Hari Terakhir
  const ctxWeeklyLoans = document.getElementById('chartWeeklyLoans');
  if (ctxWeeklyLoans) {
    const days = @json($days);
    const pinjamData = @json($weeklyLoans);
    const kembaliData = @json($weeklyReturns);

    new Chart(ctxWeeklyLoans, {
      type: 'bar',
      data: {
        labels: days,
        datasets: [
          {
            label: 'Buku Dipinjam',
            data: pinjamData,
            backgroundColor: '#1d4ed8',
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          },
          {
            label: 'Buku Dikembalikan',
            data: kembaliData,
            backgroundColor: '#10b981',
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 800, easing: 'easeOutQuart' },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: (ctx) => ` ${ctx.dataset.label}: ${ctx.raw} buku`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#64748b' }
          },
          y: {
            grid: { color: '#e2e8f0', drawBorder: false },
            ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#94a3b8', stepSize: 1 },
            beginAtZero: true
          }
        }
      }
    });
  }

  // Chart 2: Komposisi Kategori Koleksi Buku dari DB
  const ctxCategory = document.getElementById('chartBookCategory');
  if (ctxCategory) {
    const categories = @json($catLabels);
    const categoryTotals = @json($catTotals);
    const palette = ['#1d4ed8', '#0284c7', '#059669', '#d97706', '#7c3aed', '#ec4899'];

    new Chart(ctxCategory, {
      type: 'doughnut',
      data: {
        labels: categories,
        datasets: [{
          data: categoryTotals.length > 0 ? categoryTotals : [1],
          backgroundColor: palette.slice(0, categories.length),
          borderWidth: 0,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: (ctx) => ` ${ctx.label}: ${ctx.raw} judul`
            }
          }
        }
      }
    });
  }
});
</script>
@endpush
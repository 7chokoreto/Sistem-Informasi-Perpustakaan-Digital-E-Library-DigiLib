@extends('layouts.app')

@section('title', 'Dashboard Superadmin')

@section('content')
<!-- Topbar Header -->
<header class="topbar" style="justify-content: flex-end;">
  <div class="topbar-actions">
    <div class="user-profile-btn" style="cursor: default;">
      <img src="{{ auth()->user()->avatar ?: asset('assets/img/avatar-default.png') }}" 
           alt="{{ auth()->user()->name }}" class="user-avatar"
           onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80'">
      <div class="user-info">
        <span class="user-name">{{ auth()->user()->name }}</span>
        <span class="user-role" style="color: #7c3aed; font-weight: 700;">Superadmin</span>
      </div>
    </div>
  </div>
</header>

<div class="content-body">
  
  <!-- Page Title -->
  <div class="page-header" style="margin-bottom: 24px;">
    <div class="header-left">
      <span class="badge-role superadmin" style="margin-bottom: 6px;">
        <i class="fa-solid fa-shield-halved"></i> Panel Kontrol Utama
      </span>
      <h1>Dashboard Superadmin</h1>
      <p>Pantau pertumbuhan akun pengguna perpustakaan umum dan status operasional sistem secara real-time.</p>
    </div>
  </div>

  <!-- ================= STATS CARDS GRID ================= -->
  <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
    
    <!-- Card 1: Total Semua Akun -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Total Akun Pengguna</span>
        <span class="stat-value">{{ $totalUsers }}</span>
        <span class="stat-meta">Seluruh akun terdaftar di sistem</span>
      </div>
      <div class="stat-icon-wrapper icon-blue">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>

    <!-- Card 2: Total Member Umum -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Member (Anggota)</span>
        <span class="stat-value">{{ $totalMembers }}</span>
        <span class="stat-meta">Seluruh akun member terdaftar</span>
      </div>
      <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
        <i class="fa-solid fa-id-card"></i>
      </div>
    </div>

    <!-- Card 3: Total Pustakawan -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Pustakawan (Petugas)</span>
        <span class="stat-value">{{ $totalPustakawan }}</span>
        <span class="stat-meta">Petugas pelayanan &amp; buku</span>
      </div>
      <div class="stat-icon-wrapper icon-teal">
        <i class="fa-solid fa-user-tie"></i>
      </div>
    </div>

    <!-- Card 4: Status Akun Ditangguhkan -->
    <div class="stat-card">
      <div class="stat-info">
        <span class="stat-title">Status Ditangguhkan</span>
        <span class="stat-value" style="color: {{ $suspendedUsers > 0 ? '#dc2626' : '#059669' }};">
          {{ $suspendedUsers }} Akun
        </span>
        <span class="stat-meta">{{ $totalUsers - $suspendedUsers }} akun aktif normal</span>
      </div>
      <div class="stat-icon-wrapper {{ $suspendedUsers > 0 ? 'icon-red' : 'icon-teal' }}">
        <i class="fa-solid {{ $suspendedUsers > 0 ? 'fa-ban' : 'fa-circle-check' }}"></i>
      </div>
    </div>

  </div>

  <!-- ================= CHARTS SECTION ================= -->
  <div class="dashboard-grid-2" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px;">
    
    <!-- Chart 1: Grafik Pertumbuhan Pendaftaran Akun -->
    <div class="card-neumorphic" style="padding: 24px 26px;">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <div class="card-header-left">
          <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Pertumbuhan Pendaftaran Akun</h3>
          <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Jumlah pendaftaran Member umum dan Pustakawan baru</p>
        </div>
      </div>

      <!-- Custom Legend -->
      <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); font-weight: 600;">
          <span style="width: 10px; height: 10px; border-radius: 2px; background: #2563eb;"></span>
          <span>Member Umum Baru</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); font-weight: 600;">
          <span style="width: 10px; height: 10px; border-radius: 2px; background: #0f8a5f;"></span>
          <span>Pustakawan Baru</span>
        </div>
      </div>

      <!-- Chart Canvas Container -->
      <div style="height: 270px; width: 100%;">
        <canvas id="accountGrowthChart"></canvas>
      </div>
    </div>

    <!-- Chart 2: Status Kesehatan Akun -->
    <div class="card-neumorphic" style="padding: 24px 26px; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div class="card-header" style="margin-bottom: 14px;">
          <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Komposisi Status Akun</h3>
          <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Perbandingan akun aktif dan yang ditangguhkan</p>
        </div>

        <div style="height: 190px; width: 100%; position: relative;">
          <canvas id="accountStatusChart"></canvas>
        </div>
      </div>

      <!-- Detailed breakdown list -->
      <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
          <span style="display: flex; align-items: center; gap: 8px; color: var(--text-main); font-weight: 600;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Akun Aktif
          </span>
          <span style="font-weight: 700; color: #059669;">{{ $totalUsers - $suspendedUsers }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
          <span style="display: flex; align-items: center; gap: 8px; color: var(--text-main); font-weight: 600;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span> Ditangguhkan
          </span>
          <span style="font-weight: 700; color: #dc2626;">{{ $suspendedUsers }}</span>
        </div>
      </div>
    </div>

  </div>

  <!-- ================= RECENT ACCOUNTS TABLE ================= -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <div>
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">Pendaftaran Akun Terkini</h3>
        <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Daftar akun pengguna perpustakaan umum yang baru terdaftar</p>
      </div>
      <a href="{{ route('superadmin.accounts') }}" style="font-size: 13px; font-weight: 700; color: #1d4ed8; text-decoration: none;">
        Buka Manajemen Akun &rarr;
      </a>
    </div>

    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 6px;">
        <thead>
          <tr>
            <th style="width: 32%;">Nama &amp; Email Pengguna</th>
            <th style="width: 22%;">No. Telepon</th>
            <th style="width: 18%;">Peran Akun</th>
            <th style="width: 14%;">Status</th>
            <th style="width: 14%;">Tanggal Daftar</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentUsers as $acc)
            <tr>
              <td>
                <div class="user-cell">
                  <div style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($acc->avatar)
                      <img src="{{ $acc->avatar }}" alt="{{ $acc->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                      <i class="fa-solid fa-user" style="color: #94a3b8; font-size: 14px;"></i>
                    @endif
                  </div>
                  <div class="user-cell-meta" style="margin-left: 10px;">
                    <h4 style="font-size: 13.5px; font-weight: 700;">{{ $acc->name }}</h4>
                    <span style="font-size: 11px; color: var(--text-muted);">{{ $acc->email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">{{ $acc->phone ?: '-' }}</span>
              </td>
              <td>
                <span class="role-badge {{ $acc->role === 'pustakawan' ? 'pustakawan' : ($acc->role === 'superadmin' ? 'superadmin' : 'member') }}" style="font-size: 11px;">
                  {{ ucfirst($acc->role) }}
                </span>
              </td>
              <td>
                <span class="status-indicator {{ $acc->status === 'active' ? 'active' : 'inactive' }}" style="font-size: 12px; font-weight: 700;">
                  <span class="status-dot"></span>
                  {{ $acc->status === 'active' ? 'Aktif' : 'Ditangguhkan' }}
                </span>
              </td>
              <td>
                <span style="font-size: 12px; color: var(--text-muted);">{{ $acc->created_at->format('d M Y') }}</span>
              </td>
            </tr>
          @endforeach
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
  // Chart 1: Pertumbuhan Akun
  const ctxGrowth = document.getElementById('accountGrowthChart');
  if (ctxGrowth) {
    new Chart(ctxGrowth.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov'],
        datasets: [
          {
            label: 'Member Baru',
            data: [120, 190, 280, 390, 520, {{ $totalMembers }}],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.08)',
            fill: true,
            tension: 0.35,
            borderWidth: 2.5
          },
          {
            label: 'Pustakawan Baru',
            data: [2, 3, 5, 8, 12, {{ $totalPustakawan }}],
            borderColor: '#0f8a5f',
            backgroundColor: 'rgba(15, 138, 95, 0.08)',
            fill: true,
            tension: 0.35,
            borderWidth: 2.5
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { grid: { color: 'rgba(166, 180, 200, 0.2)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // Chart 2: Komposisi Status Akun
  const ctxStatus = document.getElementById('accountStatusChart');
  if (ctxStatus) {
    new Chart(ctxStatus.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Aktif', 'Ditangguhkan'],
        datasets: [{
          data: [{{ $totalUsers - $suspendedUsers }}, {{ $suspendedUsers }}],
          backgroundColor: ['#10b981', '#ef4444'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '72%'
      }
    });
  }
});
</script>
@endpush

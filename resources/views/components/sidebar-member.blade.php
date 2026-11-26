<aside class="sidebar" id="sidebar">
  <div>
    <div class="brand-wrapper">
      <div class="brand-identity">
        <div class="brand-icon">
          <i class="fa-solid fa-book-open-reader"></i>
        </div>
        <div class="brand-text">
          <h2>DigiLib</h2>
          <span>Anggota Umum</span>
        </div>
      </div>
      <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Sembunyikan Sidebar">
        <i class="fa-solid fa-angles-left"></i>
      </button>
    </div>

    <nav class="nav-menu">
      <!-- 1. Beranda -->
      <a href="{{ route('member.dashboard') }}" class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i>
        <span>Beranda</span>
      </a>

      <!-- 2. Daftar Koleksi -->
      <a href="{{ route('member.koleksi') }}" class="nav-link {{ request()->routeIs('member.koleksi*') ? 'active' : '' }}">
        <i class="fa-solid fa-book-bookmark"></i>
        <span>Daftar Koleksi</span>
      </a>

      <!-- 3. Buku Favorit -->
      <a href="{{ route('member.favorit') }}" class="nav-link {{ request()->routeIs('member.favorit*') ? 'active' : '' }}">
        <i class="fa-solid fa-bookmark"></i>
        <span>Buku Favorit</span>
      </a>

      <!-- 4. Keranjang -->
      @php
        $cartBadgeCount = auth()->check() ? auth()->user()->cartItems()->count() : 0;
      @endphp
      <a href="{{ route('member.keranjang') }}" class="nav-link {{ request()->routeIs('member.keranjang*') ? 'active' : '' }}" style="position: relative;">
        <i class="fa-solid fa-bag-shopping"></i>
        <span>Keranjang</span>
        @if($cartBadgeCount > 0)
          <span style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 800; min-width: 20px; height: 20px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; padding: 0 5px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);">
            {{ $cartBadgeCount }}
          </span>
        @endif
      </a>

      <!-- 5. Aktifitas (Riwayat Peminjaman & Transaksi) -->
      <a href="{{ route('member.aktifitas') }}" class="nav-link {{ request()->routeIs('member.aktifitas*') ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Aktifitas</span>
      </a>

      <!-- 6. Akun Saya -->
      <a href="{{ route('member.akun') }}" class="nav-link {{ request()->routeIs('member.akun*') ? 'active' : '' }}">
        <i class="fa-solid fa-circle-user"></i>
        <span>Akun Saya</span>
      </a>

      <button type="button" class="nav-link-logout" id="sidebarLogoutBtn">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Keluar</span>
      </button>
    </nav>
  </div>
</aside>

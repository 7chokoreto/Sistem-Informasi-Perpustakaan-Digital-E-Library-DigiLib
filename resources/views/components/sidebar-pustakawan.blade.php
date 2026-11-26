<aside class="sidebar" id="sidebar">
  <div>
    <div class="brand-wrapper">
      <div class="brand-identity">
        <div class="brand-icon pustakawan-brand">
          <i class="fa-solid fa-book-open"></i>
        </div>
        <div class="brand-text">
          <h2>DigiLib</h2>
          <span>Pustakawan</span>
        </div>
      </div>
      <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Sembunyikan Sidebar">
        <i class="fa-solid fa-angles-left"></i>
      </button>
    </div>

    <nav class="nav-menu">
      <a href="{{ route('pustakawan.dashboard') }}" class="nav-link {{ request()->routeIs('pustakawan.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('pustakawan.books') }}" class="nav-link {{ request()->routeIs('pustakawan.books*') ? 'active' : '' }}">
        <i class="fa-solid fa-book"></i>
        <span>Katalog Buku</span>
      </a>
      <a href="{{ route('pustakawan.loans') }}" class="nav-link {{ request()->routeIs('pustakawan.loans*') ? 'active' : '' }}">
        <i class="fa-solid fa-arrow-right-arrow-left"></i>
        <span>Peminjaman</span>
      </a>
      <a href="{{ route('pustakawan.members') }}" class="nav-link {{ request()->routeIs('pustakawan.members*') ? 'active' : '' }}">
        <i class="fa-solid fa-address-book"></i>
        <span>Data Anggota</span>
      </a>

      <button type="button" class="nav-link-logout" id="sidebarLogoutBtn">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Keluar</span>
      </button>
    </nav>
  </div>
</aside>

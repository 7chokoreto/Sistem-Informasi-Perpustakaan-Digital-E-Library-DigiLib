<aside class="sidebar" id="sidebar">
  <div>
    <div class="brand-wrapper">
      <div class="brand-identity">
        <div class="brand-icon">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="brand-text">
          <h2>DigiLib</h2>
          <span>Superadmin</span>
        </div>
      </div>
      <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Sembunyikan Sidebar">
        <i class="fa-solid fa-angles-left"></i>
      </button>
    </div>

    <nav class="nav-menu">
      <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('superadmin.accounts') }}" class="nav-link {{ request()->routeIs('superadmin.accounts*') ? 'active' : '' }}">
        <i class="fa-solid fa-users-gear"></i>
        <span>Manajemen Akun</span>
      </a>

      <button type="button" class="nav-link-logout" id="sidebarLogoutBtn">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Keluar</span>
      </button>
    </nav>
  </div>
</aside>

@extends('layouts.app')

@section('title', 'Manajemen Akun')

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
  
  <!-- Page Title & Header Actions -->
  <div style="margin-bottom: 26px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex;">
      <i class="fa-solid fa-user-shield"></i> Kelola Hak Akses Pengguna
    </span>
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 2px;">
      <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.15;">Manajemen Akun</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Kelola seluruh akun member perpustakaan umum dan pustakawan: tambah akun, ubah data, tangguhkan status, dan hapus akun.</p>
      </div>
      <button class="btn-pill btn-pill-primary" id="btnOpenAddModal" style="box-shadow: 4px 6px 16px var(--primary-glow); padding: 12px 22px; flex-shrink: 0; cursor: pointer;">
        <i class="fa-solid fa-user-plus"></i>
        <span>+ Tambah Akun Baru</span>
      </button>
    </div>
  </div>

  <!-- ================= TOOLBAR & FILTER SECTION ================= -->
  <div class="card-neumorphic" style="padding: 20px 24px; margin-bottom: 22px;">
    <form action="{{ route('superadmin.accounts') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      
      <!-- Filter Tabs Peran (Role) -->
      <div class="filter-tabs-container">
        <a href="{{ route('superadmin.accounts', ['role' => 'all', 'status' => request('status'), 'search' => request('search')]) }}" class="filter-tab {{ !request('role') || request('role') === 'all' ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-layer-group"></i>
          <span>Semua Peran</span>
        </a>
        <a href="{{ route('superadmin.accounts', ['role' => 'pustakawan', 'status' => request('status'), 'search' => request('search')]) }}" class="filter-tab {{ request('role') === 'pustakawan' ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-user-tie"></i>
          <span>Pustakawan</span>
        </a>
        <a href="{{ route('superadmin.accounts', ['role' => 'member', 'status' => request('status'), 'search' => request('search')]) }}" class="filter-tab {{ request('role') === 'member' ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-id-card"></i>
          <span>Member</span>
        </a>
      </div>

      <!-- Search Box & Filter Status Dropdown -->
      <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <!-- Status Dropdown -->
        <div class="input-inset-box select-wrapper" style="width: 170px;">
          <select name="status" class="form-input" style="padding: 9px 12px; font-size: 13px;" onchange="this.form.submit()">
            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
          </select>
        </div>

        <!-- Search Input -->
        <div class="input-inset-box" style="width: 240px;">
          <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, hp..." style="padding: 9px 12px 9px 36px; font-size: 13px;" onchange="this.form.submit()">
        </div>

        <div style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 9px 16px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-md); box-shadow: var(--shadow-flat-sm);">
          {{ $accounts->count() }} Akun
        </div>
      </div>

    </form>
  </div>

  <!-- ================= TABEL AKUN PENGGUNA ================= -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 8px;">
        <thead>
          <tr>
            <th style="width: 28%;">Nama &amp; Kontak</th>
            <th style="width: 16%;">Peran (Role)</th>
            <th style="width: 16%;">Status Akun</th>
            <th style="width: 18%;">Alasan Penangguhan</th>
            <th style="width: 10%;">Tgl Daftar</th>
            <th style="width: 12%; text-align: center;">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($accounts as $acc)
            <tr>
              <td>
                <div class="user-cell">
                  <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--shadow-flat-sm);">
                    @if($acc->avatar)
                      <img src="{{ $acc->avatar }}" alt="{{ $acc->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                      <i class="fa-solid fa-user" style="color: #94a3b8; font-size: 16px;"></i>
                    @endif
                  </div>
                  <div class="user-cell-meta" style="margin-left: 12px;">
                    <h4 style="font-size: 14px; font-weight: 700;">{{ $acc->name }}</h4>
                    <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-envelope"></i> {{ $acc->email }}</span>
                    @if($acc->phone)
                      <span style="font-size: 11px; color: var(--text-light); display: block;"><i class="fa-brands fa-whatsapp"></i> {{ $acc->phone }}</span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <span class="role-badge {{ $acc->role === 'pustakawan' ? 'pustakawan' : ($acc->role === 'superadmin' ? 'superadmin' : 'member') }}" style="font-size: 12px;">
                  <i class="fa-solid {{ $acc->role === 'pustakawan' ? 'fa-user-tie' : ($acc->role === 'superadmin' ? 'fa-shield-halved' : 'fa-id-card') }}"></i>
                  {{ ucfirst($acc->role) }}
                </span>
              </td>
              <td>
                <span class="status-indicator {{ $acc->status === 'active' ? 'active' : 'inactive' }}" style="font-weight: 700; font-size: 12px;">
                  <span class="status-dot"></span>
                  {{ $acc->status === 'active' ? 'Aktif' : 'Ditangguhkan' }}
                </span>
              </td>
              <td>
                @if($acc->status === 'suspended')
                  <span style="font-size: 11.5px; color: #dc2626; font-weight: 600; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $acc->suspension_reason }}">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $acc->suspension_reason ?: 'Tidak ada keterangan alasan.' }}
                  </span>
                @else
                  <span style="font-size: 12px; color: var(--text-muted);">-</span>
                @endif
              </td>
              <td>
                <span style="font-size: 12px; color: var(--text-muted);">{{ $acc->created_at->format('d M Y') }}</span>
              </td>
              <td style="text-align: center;">
                <div class="table-actions" style="justify-content: center; gap: 6px;">
                  
                  <!-- Tombol Edit -->
                  <button type="button" class="btn-action-icon btn-edit-account" 
                          data-id="{{ $acc->id }}" 
                          data-name="{{ $acc->name }}" 
                          data-email="{{ $acc->email }}" 
                          data-phone="{{ $acc->phone }}" 
                          data-role="{{ $acc->role }}" 
                          data-avatar="{{ $acc->avatar ?? '' }}"
                          title="Ubah Data Akun">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>

                  <!-- Tombol Tangguhkan / Aktifkan -->
                  @if($acc->role !== 'superadmin')
                    <button type="button" class="btn-action-icon btn-toggle-suspend" 
                            data-id="{{ $acc->id }}" 
                            data-name="{{ $acc->name }}" 
                            data-status="{{ $acc->status }}"
                            title="{{ $acc->status === 'active' ? 'Tangguhkan Akun Pengguna' : 'Aktifkan Kembali Akun' }}"
                            style="color: {{ $acc->status === 'active' ? '#dc2626' : '#059669' }};">
                      <i class="fa-solid {{ $acc->status === 'active' ? 'fa-ban' : 'fa-circle-check' }}"></i>
                    </button>
                  @endif

                  <!-- Tombol Hapus -->
                  @if($acc->id !== auth()->id())
                    <button type="button" class="btn-action-icon delete btn-delete-account" 
                            data-id="{{ $acc->id }}" 
                            data-name="{{ $acc->name }}" 
                            title="Hapus Akun Pengguna">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-users-slash" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 14px; font-weight: 600;">Tidak ada data akun yang sesuai filter.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ================= MODAL TAMBAH AKUN BARU ================= -->
<dialog class="modal-custom" id="modalAddAccount" style="max-width: 520px;">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-title-wrap">
        <div class="modal-icon-badge" style="color: #1d4ed8;">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
          <h3 class="modal-title">Tambah Akun Baru</h3>
          <p class="modal-subtitle">Daftarkan akun member perpustakaan umum atau pustakawan</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger">&times;</button>
    </div>

    <form action="{{ route('superadmin.accounts.store') }}" method="POST" enctype="multipart/form-data" id="formAddAccountAction">
      @csrf
      
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="text" name="name" class="form-input" placeholder="Contoh: Rian Pratama" required>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Nomor Telepon / WhatsApp</label>
        <div class="input-inset-box">
          <input type="tel" name="phone" class="form-input" placeholder="Contoh: 0812-3456-7890">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Alamat Email <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="email" name="email" class="form-input" placeholder="Contoh: user@digilib.id" required>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Kata Sandi Awal <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box" style="position: relative;">
          <input type="password" name="password" id="createAccountPassword" class="form-input" style="padding-right: 42px;" placeholder="Minimal 6 karakter" required>
          <i class="fa-solid fa-eye" onclick="togglePassword('createAccountPassword', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 15px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-light)'"></i>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Peran Pengguna (Role) <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box select-wrapper">
          <select name="role" id="addRole" class="form-input" required>
            <option value="member" selected>Member (Anggota Umum)</option>
            <option value="pustakawan">Pustakawan (Staf Pelayanan)</option>
          </select>
        </div>
      </div>

      <!-- Foto Profil Pustakawan -->
      <div id="addAvatarSection" style="margin-bottom: 22px;">
        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <span>Foto Profil (Pustakawan)</span>
          <span style="font-size: 11px; color: var(--text-muted); font-weight: normal;">Format JPG, PNG, WebP (Maks. 3MB)</span>
        </label>
        <div style="display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: var(--bg-main); border-radius: var(--radius-md); box-shadow: inset 1px 1px 3px rgba(166, 180, 200, 0.25), inset -1px -1px 3px rgba(255, 255, 255, 0.7);">
          <div style="width: 52px; height: 52px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--shadow-sm);">
            <img id="addAvatarPreview" src="{{ asset('assets/img/avatar-default.png') }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80'">
          </div>
          <div style="flex: 1;">
            <input type="file" name="avatar_file" id="addAvatarInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <div style="display: flex; gap: 8px; align-items: center;">
              <button type="button" onclick="document.getElementById('addAvatarInput').click()" class="btn-secondary-flat" style="padding: 6px 14px; font-size: 12px; font-weight: 600;">
                <i class="fa-solid fa-camera"></i> Pilih Foto
              </button>
              <button type="button" id="btnResetAddAvatar" style="display: none; padding: 6px 12px; font-size: 12px; color: #dc2626; background: rgba(220, 38, 38, 0.08); border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                <i class="fa-solid fa-xmark"></i> Batal
              </button>
            </div>
            <span id="addAvatarFilename" style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 4px;">Pilih foto jika role adalah Pustakawan</span>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="padding-top: 0; border-top: none;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 22px;">
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Simpan Akun</span>
        </button>
      </div>

    </form>
  </div>
</dialog>

<!-- ================= MODAL EDIT AKUN ================= -->
<dialog class="modal-custom" id="modalEditAccount" style="max-width: 520px;">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-title-wrap">
        <div class="modal-icon-badge" style="color: #1d4ed8;">
          <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
          <h3 class="modal-title">Ubah Data Akun</h3>
          <p class="modal-subtitle">Perbarui identitas dan hak akses akun</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger">&times;</button>
    </div>

    <form id="formEditAccountAction" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="text" name="name" id="editName" class="form-input" required>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Nomor Telepon / WhatsApp</label>
        <div class="input-inset-box">
          <input type="tel" name="phone" id="editPhone" class="form-input">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Alamat Email <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box">
          <input type="email" name="email" id="editEmail" class="form-input" required>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
        <div class="input-inset-box" style="position: relative;">
          <input type="password" name="password" id="editAccountPassword" class="form-input" style="padding-right: 42px;" placeholder="Opsional (minimal 6 karakter)">
          <i class="fa-solid fa-eye" onclick="togglePassword('editAccountPassword', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 15px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-light)'"></i>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Peran Pengguna (Role) <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box select-wrapper">
          <select name="role" id="editRole" class="form-input" required>
            <option value="member">Member (Anggota Umum)</option>
            <option value="pustakawan">Pustakawan (Staf Pelayanan)</option>
          </select>
        </div>
      </div>

      <!-- Kelola Foto Profil Pustakawan -->
      <div id="editAvatarSection" style="margin-bottom: 22px;">
        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <span>Foto Profil (Pustakawan)</span>
          <span style="font-size: 11px; color: var(--text-muted); font-weight: normal;">Format JPG, PNG, WebP (Maks. 3MB)</span>
        </label>
        <div style="display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: var(--bg-main); border-radius: var(--radius-md); box-shadow: inset 1px 1px 3px rgba(166, 180, 200, 0.25), inset -1px -1px 3px rgba(255, 255, 255, 0.7);">
          <div style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--shadow-sm);">
            <img id="editAvatarPreview" src="{{ asset('assets/img/avatar-default.png') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80'">
          </div>
          <div style="flex: 1;">
            <input type="file" name="avatar_file" id="editAvatarInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <input type="hidden" name="delete_avatar" id="editDeleteAvatarFlag" value="0">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
              <button type="button" onclick="document.getElementById('editAvatarInput').click()" class="btn-secondary-flat" style="padding: 6px 14px; font-size: 12px; font-weight: 600;">
                <i class="fa-solid fa-camera"></i> Ganti Foto
              </button>
              <button type="button" id="btnDeleteEditAvatar" class="btn-secondary-flat" style="padding: 6px 12px; font-size: 12px; color: #dc2626; border-color: rgba(220, 38, 38, 0.2); font-weight: 600;">
                <i class="fa-solid fa-trash-can"></i> Hapus Foto
              </button>
            </div>
            <span id="editAvatarStatusText" style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 4px;">
              Superadmin dapat menambah, mengubah, atau menghapus foto profil pustakawan.
            </span>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="padding-top: 0; border-top: none;">
        <button type="button" class="btn-secondary-flat modal-close-trigger">Batal</button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 22px;">
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Perbarui Data</span>
        </button>
      </div>

    </form>
  </div>
</dialog>

<!-- ================= MODAL TANGGUHKAN AKUN DENGAN ALASAN ================= -->
<dialog class="modal-custom" id="modalSuspendAccount" style="max-width: 480px;">
  <div class="modal-content" style="padding: 34px 28px;">
    
    <div style="text-align: center; margin-bottom: 20px;">
      <div class="modal-icon-badge danger" id="suspendIconBadge" style="width: 56px; height: 56px; font-size: 24px; margin: 0 auto 16px auto;">
        <i class="fa-solid fa-ban" id="suspendIcon"></i>
      </div>
      <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;" id="suspendModalTitle">
        Tangguhkan Akun Pengguna?
      </h3>
      <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;" id="suspendModalDesc">
        Apakah Anda yakin ingin menangguhkan status akun <b id="suspendTargetName">-</b>?
      </p>
    </div>

    <form id="formSuspendAction" method="POST">
      @csrf
      @method('PATCH')

      <div id="suspendReasonFormArea" style="margin-bottom: 20px;">
        <label class="form-label" style="text-align: left; display: block;">Pilih Alasan Penangguhan <span style="color: #dc2626;">*</span></label>
        <div class="input-inset-box select-wrapper" style="margin-bottom: 10px;">
          <select id="presetSuspendReason" class="form-input">
            <option value="Keterlambatan pengembalian buku lebih dari 30 hari.">Keterlambatan pengembalian buku lebih dari 30 hari</option>
            <option value="Kerusakan fisik pada koleksi buku perpustakaan yang belum diganti.">Kerusakan fisik buku perpustakaan</option>
            <option value="Pelanggaran ketentuan tata tertib dan sanksi administratif perpustakaan.">Pelanggaran tata tertib umum</option>
            <option value="custom">Alasan Lainnya (Tulis Manual)...</option>
          </select>
        </div>

        <div class="input-inset-box" id="customReasonArea" style="display: none;">
          <textarea name="suspension_reason" id="suspensionReasonInput" class="form-input" rows="2" placeholder="Tuliskan keterangan detail alasan penangguhan..."></textarea>
        </div>
      </div>

      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-danger-action" id="btnConfirmSuspend" style="min-width: 150px; justify-content: center;">
          <i class="fa-solid fa-ban"></i>
          <span id="btnSuspendText">Ya, Tangguhkan</span>
        </button>
      </div>

    </form>

  </div>
</dialog>

<!-- ================= MODAL HAPUS AKUN ================= -->
<dialog class="modal-custom" id="modalDeleteAccount" style="max-width: 440px;">
  <div class="modal-content" style="text-align: center; padding: 34px 28px;">
    
    <div class="modal-icon-badge danger" style="width: 56px; height: 56px; font-size: 24px; margin: 0 auto 16px auto;">
      <i class="fa-solid fa-trash-can"></i>
    </div>

    <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Hapus Akun Pengguna?</h3>
    <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5; margin-bottom: 22px;">
      Apakah Anda yakin ingin menghapus akun <b id="deleteTargetName">-</b> secara permanen dari sistem?
    </p>

    <form id="formDeleteAction" method="POST">
      @csrf
      @method('DELETE')
      <div style="display: flex; justify-content: center; gap: 14px;">
        <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
        <button type="submit" class="btn-danger-action" style="min-width: 130px; justify-content: center; background: #dc2626; color: #ffffff !important; border: none;">
          <i class="fa-solid fa-trash-can" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Hapus Akun</span>
        </button>
      </div>
    </form>

  </div>
</dialog>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const defaultAvatar = "{{ asset('assets/img/avatar-default.png') }}";

  // Modal Tambah Akun
  const modalAdd = document.getElementById('modalAddAccount');
  const btnOpenAdd = document.getElementById('btnOpenAddModal');
  const addRole = document.getElementById('addRole');
  const addAvatarSection = document.getElementById('addAvatarSection');
  const addAvatarInput = document.getElementById('addAvatarInput');
  const addAvatarPreview = document.getElementById('addAvatarPreview');
  const btnResetAddAvatar = document.getElementById('btnResetAddAvatar');
  const addAvatarFilename = document.getElementById('addAvatarFilename');
  
  if (btnOpenAdd && modalAdd) {
    btnOpenAdd.addEventListener('click', () => {
      // Reset input avatar saat modal tambah dibuka
      if (addAvatarInput) addAvatarInput.value = '';
      if (addAvatarPreview) addAvatarPreview.src = defaultAvatar;
      if (btnResetAddAvatar) btnResetAddAvatar.style.display = 'none';
      if (addAvatarFilename) addAvatarFilename.textContent = 'Pilih foto jika role adalah Pustakawan';
      if (addRole && addAvatarSection) {
        addAvatarSection.style.display = addRole.value === 'pustakawan' ? 'block' : 'none';
      }
      modalAdd.showModal();
    });
  }

  if (addRole && addAvatarSection) {
    addRole.addEventListener('change', () => {
      addAvatarSection.style.display = addRole.value === 'pustakawan' ? 'block' : 'none';
    });
  }

  if (addAvatarInput) {
    addAvatarInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
          if (addAvatarPreview) addAvatarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        if (addAvatarFilename) addAvatarFilename.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        if (btnResetAddAvatar) btnResetAddAvatar.style.display = 'inline-flex';
      }
    });
  }

  if (btnResetAddAvatar) {
    btnResetAddAvatar.addEventListener('click', () => {
      if (addAvatarInput) addAvatarInput.value = '';
      if (addAvatarPreview) addAvatarPreview.src = defaultAvatar;
      if (btnResetAddAvatar) btnResetAddAvatar.style.display = 'none';
      if (addAvatarFilename) addAvatarFilename.textContent = 'Pilih foto jika role adalah Pustakawan';
    });
  }

  // Modal Edit Akun
  const modalEdit = document.getElementById('modalEditAccount');
  const formEditAction = document.getElementById('formEditAccountAction');
  const editName = document.getElementById('editName');
  const editEmail = document.getElementById('editEmail');
  const editPhone = document.getElementById('editPhone');
  const editRole = document.getElementById('editRole');
  const editAvatarSection = document.getElementById('editAvatarSection');
  const editAvatarPreview = document.getElementById('editAvatarPreview');
  const editAvatarInput = document.getElementById('editAvatarInput');
  const editDeleteAvatarFlag = document.getElementById('editDeleteAvatarFlag');
  const btnDeleteEditAvatar = document.getElementById('btnDeleteEditAvatar');
  const editAvatarStatusText = document.getElementById('editAvatarStatusText');

  let currentOriginalAvatar = '';

  document.querySelectorAll('.btn-edit-account').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const role = btn.getAttribute('data-role');
      const avatar = btn.getAttribute('data-avatar');

      formEditAction.action = `/superadmin/manajemen-akun/${id}`;
      editName.value = btn.getAttribute('data-name');
      editEmail.value = btn.getAttribute('data-email');
      editPhone.value = btn.getAttribute('data-phone') || '';
      editRole.value = role;

      // Handle avatar pustakawan
      currentOriginalAvatar = avatar || '';
      if (editDeleteAvatarFlag) editDeleteAvatarFlag.value = '0';
      if (editAvatarInput) editAvatarInput.value = '';

      if (editAvatarSection) {
        editAvatarSection.style.display = role === 'pustakawan' ? 'block' : 'none';
      }

      if (avatar && avatar.trim() !== '') {
        if (editAvatarPreview) editAvatarPreview.src = avatar;
        if (btnDeleteEditAvatar) btnDeleteEditAvatar.style.display = 'inline-flex';
        if (editAvatarStatusText) editAvatarStatusText.textContent = 'Foto profil aktif saat ini. Anda dapat mengganti atau menghapusnya.';
      } else {
        if (editAvatarPreview) editAvatarPreview.src = defaultAvatar;
        if (btnDeleteEditAvatar) btnDeleteEditAvatar.style.display = 'none';
        if (editAvatarStatusText) editAvatarStatusText.textContent = 'Belum ada foto profil kustom. Klik "Ganti Foto" untuk mengunggah.';
      }

      modalEdit.showModal();
    });
  });

  if (editRole && editAvatarSection) {
    editRole.addEventListener('change', () => {
      editAvatarSection.style.display = editRole.value === 'pustakawan' ? 'block' : 'none';
    });
  }

  if (editAvatarInput) {
    editAvatarInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
          if (editAvatarPreview) editAvatarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        if (editDeleteAvatarFlag) editDeleteAvatarFlag.value = '0';
        if (btnDeleteEditAvatar) btnDeleteEditAvatar.style.display = 'inline-flex';
        if (editAvatarStatusText) {
          editAvatarStatusText.innerHTML = `<span style="color: #059669; font-weight: 600;"><i class="fa-solid fa-check"></i> Foto baru dipilih: ${file.name}</span>`;
        }
      }
    });
  }

  if (btnDeleteEditAvatar) {
    btnDeleteEditAvatar.addEventListener('click', () => {
      if (editDeleteAvatarFlag) editDeleteAvatarFlag.value = '1';
      if (editAvatarInput) editAvatarInput.value = '';
      if (editAvatarPreview) editAvatarPreview.src = defaultAvatar;
      btnDeleteEditAvatar.style.display = 'none';
      if (editAvatarStatusText) {
        editAvatarStatusText.innerHTML = `<span style="color: #dc2626; font-weight: 600;"><i class="fa-solid fa-trash-can"></i> Foto profil akan dihapus saat data disimpan.</span>`;
      }
    });
  }

  // Suspend Modal Handling
  const modalSuspend = document.getElementById('modalSuspendAccount');
  const formSuspend = document.getElementById('formSuspendAction');
  const suspendTargetName = document.getElementById('suspendTargetName');
  const suspendModalTitle = document.getElementById('suspendModalTitle');
  const suspendModalDesc = document.getElementById('suspendModalDesc');
  const suspendReasonFormArea = document.getElementById('suspendReasonFormArea');
  const presetReason = document.getElementById('presetSuspendReason');
  const customReasonArea = document.getElementById('customReasonArea');
  const reasonInput = document.getElementById('suspensionReasonInput');
  const btnConfirmSuspend = document.getElementById('btnConfirmSuspend');
  const btnSuspendText = document.getElementById('btnSuspendText');
  const suspendIcon = document.getElementById('suspendIcon');

  document.querySelectorAll('.btn-toggle-suspend').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      const status = btn.getAttribute('data-status');

      formSuspend.action = `/superadmin/manajemen-akun/${id}/toggle-suspend`;
      suspendTargetName.textContent = name;

      if (status === 'active') {
        suspendModalTitle.textContent = 'Tangguhkan Akun Pengguna?';
        suspendModalDesc.innerHTML = `Apakah Anda yakin ingin menangguhkan status akun <b>${name}</b>?`;
        suspendReasonFormArea.style.display = 'block';
        reasonInput.value = presetReason.value;
        btnSuspendText.textContent = 'Ya, Tangguhkan';
        btnConfirmSuspend.className = 'btn-danger-action';
        btnConfirmSuspend.style.background = '#dc2626';
        btnConfirmSuspend.style.color = '#ffffff';
        suspendIcon.className = 'fa-solid fa-ban';
      } else {
        suspendModalTitle.textContent = 'Aktifkan Kembali Akun?';
        suspendModalDesc.innerHTML = `Apakah Anda yakin ingin memulihkan dan mengaktifkan kembali akun <b>${name}</b>?`;
        suspendReasonFormArea.style.display = 'none';
        btnSuspendText.textContent = 'Ya, Aktifkan';
        btnConfirmSuspend.className = 'btn-pill btn-pill-success';
        btnConfirmSuspend.style.background = '#059669';
        btnConfirmSuspend.style.color = '#ffffff';
        suspendIcon.className = 'fa-solid fa-circle-check';
      }

      modalSuspend.showModal();
    });
  });

  if (presetReason) {
    presetReason.addEventListener('change', () => {
      if (presetReason.value === 'custom') {
        customReasonArea.style.display = 'block';
        reasonInput.value = '';
        reasonInput.required = true;
      } else {
        customReasonArea.style.display = 'none';
        reasonInput.value = presetReason.value;
        reasonInput.required = false;
      }
    });
  }

  // Delete Modal Handling
  const modalDelete = document.getElementById('modalDeleteAccount');
  const formDelete = document.getElementById('formDeleteAction');
  const deleteTargetName = document.getElementById('deleteTargetName');

  document.querySelectorAll('.btn-delete-account').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      formDelete.action = `/superadmin/manajemen-akun/${id}`;
      deleteTargetName.textContent = name;
      modalDelete.showModal();
    });
  });
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Data Anggota Perpustakaan')

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
  
  <!-- Header Title -->
  <div style="margin-bottom: 26px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
      <i class="fa-solid fa-users"></i> Direktori Member
    </span>
    <div style="margin-top: 2px;">
      <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.15;">Data Anggota Perpustakaan</h1>
      <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Daftar anggota masyarakat umum terdaftar, status keaktifan, dan rekam peminjaman buku.</p>
    </div>
  </div>

  <!-- ================= TOOLBAR & FILTER SECTION ================= -->
  <div class="card-neumorphic" style="padding: 20px 24px; margin-bottom: 22px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      
      <!-- Search Box -->
      <div class="input-inset-box" style="width: 320px;">
        <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
        <input type="text" id="memberSearchInput" placeholder="Cari nama, email, nomor HP..." style="padding: 10px 14px 10px 38px; font-size: 13px;">
      </div>

      <!-- Status Filter & Count -->
      <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div style="position: relative;">
          <select id="memberStatusFilter" style="padding: 9px 36px 9px 14px; border-radius: var(--radius-md); background: var(--bg-main); box-shadow: var(--shadow-flat-sm); font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; border: var(--border-subtle); appearance: none; -webkit-appearance: none;">
            <option value="all">Semua Status Akun</option>
            <option value="active">Status: Aktif</option>
            <option value="suspended">Status: Ditangguhkan</option>
          </select>
          <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 11px; color: var(--text-muted); pointer-events: none;"></i>
        </div>

        <div style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 9px 16px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-md); box-shadow: var(--shadow-flat-sm);" id="totalMemberCount">
          {{ count($members) }} Anggota
        </div>
      </div>

    </div>
  </div>

  <!-- ================= MEMBER TABLE CARD ================= -->
  <div class="card-neumorphic" style="padding: 24px 26px;">
    <div class="table-responsive">
      <table class="custom-table" style="border-spacing: 0 8px;">
        <thead>
          <tr>
            <th style="width: 14%;">ID Member</th>
            <th style="width: 28%;">Nama &amp; Email Anggota</th>
            <th style="width: 18%;">Kontak WhatsApp</th>
            <th style="width: 14%;">Bergabung</th>
            <th style="width: 12%;">Total Pinjam</th>
            <th style="width: 14%;">Status Akun</th>
            <th style="width: 10%; text-align: center;">Riwayat</th>
          </tr>
        </thead>
        <tbody id="memberTableBody">
          @forelse($members as $m)
            @php
              $loansData = $m->loans->map(function($loan) {
                  return [
                      'trx' => $loan->trx_code,
                      'book' => $loan->book ? $loan->book->title : 'Buku',
                      'date' => \Carbon\Carbon::parse($loan->borrow_date)->format('d M Y'),
                      'due' => \Carbon\Carbon::parse($loan->due_date)->format('d M Y'),
                      'status' => $loan->status,
                  ];
              });
            @endphp
            <tr class="member-row" 
                data-name="{{ strtolower($m->name) }}"
                data-email="{{ strtolower($m->email) }}"
                data-phone="{{ strtolower($m->phone ?: '') }}"
                data-status="{{ $m->status }}">
              <td>
                <span style="font-family: monospace; font-weight: 700; color: #1d4ed8;">MBR-{{ str_pad($m->id, 3, '0', STR_PAD_LEFT) }}</span>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--shadow-flat-sm);">
                    @if($m->avatar)
                      <img src="{{ $m->avatar }}" alt="{{ $m->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                      <i class="fa-solid fa-user" style="color: #94a3b8; font-size: 15px;"></i>
                    @endif
                  </div>
                  <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ $m->name }}</h4>
                    <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-envelope"></i> {{ $m->email }}</span>
                  </div>
                </div>
              </td>
              <td>
                @if($m->phone)
                  <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">
                    <i class="fa-brands fa-whatsapp" style="color: #059669; margin-right: 4px;"></i> {{ $m->phone }}
                  </span>
                @else
                  <span style="font-size: 12px; color: var(--text-muted);">-</span>
                @endif
              </td>
              <td>
                <span style="font-size: 12.5px; color: var(--text-muted);">
                  {{ $m->created_at ? $m->created_at->format('d M Y') : '10 Okt 2026' }}
                </span>
              </td>
              <td>
                <span style="font-size: 13px; font-weight: 700; color: #1d4ed8; padding: 4px 10px; background: rgba(29, 78, 216, 0.08); border-radius: var(--radius-sm);">
                  {{ $m->loans_count }} buku
                </span>
              </td>
              <td>
                @if($m->status === 'active')
                  <span class="status-indicator active" style="font-weight: 700; font-size: 12px;">
                    <span class="status-dot"></span>
                    Aktif
                  </span>
                @else
                  <span class="status-indicator inactive" style="font-weight: 700; font-size: 12px;" title="{{ $m->suspension_reason }}">
                    <span class="status-dot"></span>
                    Ditangguhkan
                  </span>
                @endif
              </td>
              <td style="text-align: center;">
                <button type="button" class="btn-icon-action btn-view-history" 
                        data-name="{{ $m->name }}"
                        data-loans='@json($loansData)'
                        title="Lihat Riwayat Peminjaman">
                  <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-users" style="font-size: 32px; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                Belum ada anggota terdaftar di perpustakaan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ================= MODAL RIWAYAT PEMINJAMAN MEMBER ================= -->
<dialog class="modal-custom" id="modalMemberHistory" style="max-width: 540px;">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-header-left">
        <div class="modal-icon-badge">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="modal-title-wrap">
          <h3>Riwayat Peminjaman Buku</h3>
          <p>Catatan koleksi buku yang dipinjam oleh <b id="historyMemberName" style="color: #1d4ed8;">-</b></p>
        </div>
      </div>
      <button type="button" class="modal-close-btn modal-close-trigger" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="modal-form-grid" style="padding: 12px 0;">
      <div id="historyListContainer" style="display: flex; flex-direction: column; gap: 10px; max-height: 340px; overflow-y: auto; padding-right: 4px;">
        <!-- Populated dynamically by JS -->
      </div>
    </div>

    <div class="modal-footer" style="margin-top: 20px; display: flex; justify-content: flex-end;">
      <button type="button" class="btn-pill btn-pill-primary modal-close-trigger" style="padding: 10px 24px;">
        <span>Tutup</span>
      </button>
    </div>
  </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalHistory = document.getElementById('modalMemberHistory');
  const historyName = document.getElementById('historyMemberName');
  const historyContainer = document.getElementById('historyListContainer');

  document.querySelectorAll('.btn-view-history').forEach(btn => {
    btn.addEventListener('click', () => {
      const name = btn.getAttribute('data-name');
      let loans = [];
      try {
        loans = JSON.parse(btn.getAttribute('data-loans') || '[]');
      } catch (e) {
        loans = [];
      }

      historyName.textContent = name;
      historyContainer.innerHTML = '';

      if (loans.length === 0) {
        historyContainer.innerHTML = `
          <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
            <i class="fa-solid fa-inbox" style="font-size: 24px; opacity: 0.4; margin-bottom: 8px; display: block;"></i>
            Belum ada rekam transaksi peminjaman untuk anggota ini.
          </div>
        `;
      } else {
        loans.forEach(loan => {
          let badgeClass = 'dipinjam';
          if (loan.status === 'Dikembalikan') badgeClass = 'kembali';
          else if (loan.status === 'Terlambat') badgeClass = 'terlambat';

          const card = document.createElement('div');
          card.style.cssText = 'padding: 12px 14px; border-radius: var(--radius-md); background: var(--bg-main); box-shadow: var(--shadow-flat-sm); display: flex; justify-content: space-between; align-items: center; gap: 12px;';
          card.innerHTML = `
            <div>
              <div style="font-weight: 700; font-size: 13.5px; color: var(--text-main);">${loan.book}</div>
              <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                <span style="font-family: monospace; color: #1d4ed8; font-weight: 700;">${loan.trx}</span> &bull; Pinjam: ${loan.date} (Tempo: ${loan.due})
              </div>
            </div>
            <div>
              <span class="loan-badge ${badgeClass}" style="font-size: 11px;">${loan.status}</span>
            </div>
          `;
          historyContainer.appendChild(card);
        });
      }

      modalHistory?.showModal();
    });
  });

  // Close triggers
  document.querySelectorAll('.modal-close-trigger').forEach(trigger => {
    trigger.addEventListener('click', () => {
      modalHistory?.close();
    });
  });

  // Client-side search and status filter
  const searchInput = document.getElementById('memberSearchInput');
  const statusFilter = document.getElementById('memberStatusFilter');
  const rows = document.querySelectorAll('.member-row');
  const countEl = document.getElementById('totalMemberCount');

  function filterMembers() {
    const q = (searchInput?.value || '').trim().toLowerCase();
    const st = statusFilter?.value || 'all';
    let visibleCount = 0;

    rows.forEach(row => {
      const name = row.getAttribute('data-name');
      const email = row.getAttribute('data-email');
      const phone = row.getAttribute('data-phone');
      const status = row.getAttribute('data-status');

      const matchesSearch = !q || name.includes(q) || email.includes(q) || phone.includes(q);
      const matchesStatus = st === 'all' || status === st;

      if (matchesSearch && matchesStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (countEl) {
      countEl.textContent = `${visibleCount} Anggota`;
    }
  }

  searchInput?.addEventListener('input', filterMembers);
  statusFilter?.addEventListener('change', filterMembers);
});
</script>
@endpush
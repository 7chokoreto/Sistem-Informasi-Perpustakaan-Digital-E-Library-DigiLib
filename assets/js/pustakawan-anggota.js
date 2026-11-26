/**
 * DigiLib - Pustakawan Data Anggota Scripts
 * Public Library Member Directory & Borrowing History Modal
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mock Data Anggota Umum Perpustakaan
  const members = [
    {
      id: 'MBR-2026-001',
      nama: 'Rina Wulandari',
      telepon: '0812-8877-6655',
      email: 'rina.wulandari@gmail.com',
      tglDaftar: '10 Feb 2026',
      bukuDipinjam: 1,
      status: 'Aktif',
      riwayat: [
        { judul: 'Laut Bercerita', tgl: '20 Okt 2026', status: 'Terlambat' },
        { judul: 'Laskar Pelangi', tgl: '05 Sep 2026', status: 'Selesai' }
      ]
    },
    {
      id: 'MBR-2026-002',
      nama: 'Dimas Prasetyo',
      telepon: '0857-1234-9988',
      email: 'dimas.prasetyo@yahoo.com',
      tglDaftar: '24 Mar 2026',
      bukuDipinjam: 1,
      status: 'Aktif',
      riwayat: [
        { judul: 'Filosofi Teras', tgl: '28 Okt 2026', status: 'Dipinjam' }
      ]
    },
    {
      id: 'MBR-2026-003',
      nama: 'Dewi Lestari',
      telepon: '0813-4455-6677',
      email: 'dewi.lestari@gmail.com',
      tglDaftar: '05 Apr 2026',
      bukuDipinjam: 1,
      status: 'Aktif',
      riwayat: [
        { judul: 'Atomic Habits', tgl: '29 Okt 2026', status: 'Dipinjam' },
        { judul: 'Sapiens', tgl: '14 Agu 2026', status: 'Selesai' }
      ]
    },
    {
      id: 'MBR-2026-004',
      nama: 'Budi Santoso',
      telepon: '0811-2233-4455',
      email: 'budi.santoso@gmail.com',
      tglDaftar: '18 Mei 2026',
      bukuDipinjam: 0,
      status: 'Aktif',
      riwayat: [
        { judul: 'Laskar Pelangi', tgl: '15 Okt 2026', status: 'Selesai' }
      ]
    },
    {
      id: 'MBR-2026-005',
      nama: 'Siti Rahma',
      telepon: '0878-9900-1122',
      email: 'siti.rahma@outlook.com',
      tglDaftar: '01 Jun 2026',
      bukuDipinjam: 0,
      status: 'Aktif',
      riwayat: [
        { judul: 'Ensiklopedia Sains Populer', tgl: '10 Okt 2026', status: 'Selesai' }
      ]
    },
    {
      id: 'MBR-2026-006',
      nama: 'Andi Pratama',
      telepon: '0852-6677-8899',
      email: 'andi.pratama@gmail.com',
      tglDaftar: '15 Jan 2026',
      bukuDipinjam: 0,
      status: 'Ditangguhkan',
      riwayat: [
        { judul: 'Petualangan Detektif Cilik', tgl: '12 Jul 2026', status: 'Selesai' }
      ]
    }
  ];

  // DOM Elements
  const memberTableBody = document.getElementById('memberTableBody');
  const memberSearchInput = document.getElementById('memberSearchInput');
  const memberStatusFilter = document.getElementById('memberStatusFilter');
  const totalMemberCountEl = document.getElementById('totalMemberCount');

  // Modal Riwayat
  const modalHistory = document.getElementById('modalMemberHistory');
  const historyMemberName = document.getElementById('historyMemberName');
  const historyListContainer = document.getElementById('historyListContainer');

  // Render Member Table
  const renderMembers = () => {
    if (!memberTableBody) return;

    const searchTerm = (memberSearchInput ? memberSearchInput.value : '').toLowerCase().trim();
    const filterStatus = memberStatusFilter ? memberStatusFilter.value : 'all';

    const filtered = members.filter(m => {
      const matchSearch = m.nama.toLowerCase().includes(searchTerm) ||
                          m.id.toLowerCase().includes(searchTerm) ||
                          m.telepon.includes(searchTerm) ||
                          m.email.toLowerCase().includes(searchTerm);

      const matchStatus = filterStatus === 'all' || m.status === filterStatus;

      return matchSearch && matchStatus;
    });

    if (totalMemberCountEl) {
      totalMemberCountEl.textContent = `${filtered.length} Anggota`;
    }

    if (filtered.length === 0) {
      memberTableBody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-users" style="font-size: 36px; opacity: 0.35; margin-bottom: 12px; display: block;"></i>
            <p style="font-size: 14px; font-weight: 600;">Tidak ditemukan anggota dengan kriteria tersebut.</p>
          </td>
        </tr>
      `;
      return;
    }

    memberTableBody.innerHTML = filtered.map(m => {
      const isSuspended = m.status === 'Ditangguhkan';
      return `
        <tr>
          <td>
            <span style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #1d4ed8;">${m.id}</span>
          </td>
          <td>
            <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">${m.nama}</div>
            <div style="font-size: 12px; color: var(--text-muted);">${m.email}</div>
          </td>
          <td>
            <span style="font-size: 13px; font-weight: 600; color: #334155;">
              <i class="fa-brands fa-whatsapp" style="color: #059669;"></i> ${m.telepon}
            </span>
          </td>
          <td>
            <span style="font-size: 13px; color: var(--text-muted);">${m.tglDaftar}</span>
          </td>
          <td>
            <span class="stock-indicator ${m.bukuDipinjam > 0 ? 'available' : ''}" style="font-size: 13px;">
              ${m.bukuDipinjam} Buku
            </span>
          </td>
          <td>
            <span class="badge-status ${isSuspended ? 'suspended' : 'active'}">
              <i class="fa-solid fa-${isSuspended ? 'circle-xmark' : 'check'}"></i>
              ${m.status}
            </span>
          </td>
          <td>
            <div class="table-actions">
              <button class="btn-table-action btn-history-trigger" data-id="${m.id}" title="Lihat Riwayat Peminjaman">
                <i class="fa-solid fa-clock-rotate-left"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    // Attach history click
    document.querySelectorAll('.btn-history-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const m = members.find(x => x.id === id);
        if (m && modalHistory) {
          if (historyMemberName) historyMemberName.textContent = m.nama;
          if (historyListContainer) {
            if (m.riwayat.length === 0) {
              historyListContainer.innerHTML = `
                <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
                  Belum ada catatan peminjaman untuk anggota ini.
                </div>
              `;
            } else {
              historyListContainer.innerHTML = m.riwayat.map(r => {
                const isOverdue = r.status === 'Terlambat';
                const isOngoing = r.status === 'Dipinjam';
                let color = isOverdue ? '#dc2626' : (isOngoing ? '#1d4ed8' : '#059669');
                return `
                  <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; border-radius: var(--radius-sm); background: var(--bg-main); box-shadow: var(--shadow-flat-sm); margin-bottom: 8px;">
                    <div>
                      <div style="font-weight: 700; font-size: 13.5px; color: var(--text-main);">${r.judul}</div>
                      <div style="font-size: 11.5px; color: var(--text-muted);">Tanggal Pinjam: ${r.tgl}</div>
                    </div>
                    <span style="font-size: 12px; font-weight: 700; color: ${color}; padding: 4px 10px; border-radius: var(--radius-pill); background: rgba(0,0,0,0.03);">
                      ${r.status}
                    </span>
                  </div>
                `;
              }).join('');
            }
          }
          modalHistory.showModal();
        }
      });
    });
  };

  // Search and Filter Listeners
  if (memberSearchInput) memberSearchInput.addEventListener('input', renderMembers);
  if (memberStatusFilter) memberStatusFilter.addEventListener('change', renderMembers);

  // Initial Render
  renderMembers();
});

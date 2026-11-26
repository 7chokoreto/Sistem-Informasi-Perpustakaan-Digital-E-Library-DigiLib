/**
 * DigiLib - Member Dashboard Interactive Script
 * Loads current member loans from localStorage (shared with Pustakawan), updates stats, and renders recent loans table
 */

document.addEventListener('DOMContentLoaded', () => {
  const user = getDigiLibCurrentUser();
  
  // Update info email di profile card
  const emailEl = document.getElementById('memberDashboardEmail');
  if (emailEl) emailEl.textContent = user.email;

  // Status Keanggotaan Card
  const statAccountStatusText = document.getElementById('statAccountStatusText');
  const statAccountStatusDesc = document.getElementById('statAccountStatusDesc');
  if (statAccountStatusText && statAccountStatusDesc) {
    if (user.status === 'suspended') {
      statAccountStatusText.textContent = 'Ditangguhkan';
      statAccountStatusText.style.color = '#dc2626';
      statAccountStatusDesc.textContent = 'Akses peminjaman dibekukan';
    } else {
      statAccountStatusText.textContent = 'Aktif';
      statAccountStatusText.style.color = '#059669';
      statAccountStatusDesc.textContent = 'Bebas meminjam koleksi buku';
    }
  }

  // Load Loans from localStorage
  let allLoans = [];
  try {
    const raw = localStorage.getItem('digilib_loans');
    if (raw) allLoans = JSON.parse(raw);
  } catch (e) {
    console.error('Failed to load loans', e);
  }

  // Filter pinjaman milik user ini (berdasarkan nama atau jika data demo kita cocokkan atau fallback)
  const myLoans = allLoans.filter(l => {
    return l.namaMember.toLowerCase() === user.name.toLowerCase() ||
           (user.phone && l.waMember === user.phone);
  });

  // Jika belum ada pinjaman milik user (misal user baru login), tampilkan beberapa loan terkait atau mock data
  const relevantLoans = myLoans.length > 0 ? myLoans : allLoans.slice(0, 3);

  // Hitung Statistik
  const activeLoansCount = relevantLoans.filter(l => l.status === 'Dipinjam' || l.status === 'Terlambat').length;
  const pendingValidationCount = relevantLoans.filter(l => l.status === 'Menunggu Validasi').length;
  const completedLoansCount = relevantLoans.filter(l => l.status === 'Dikembalikan').length;

  const statActiveLoansEl = document.getElementById('statActiveLoans');
  const statPendingValidationEl = document.getElementById('statPendingValidation');
  const statCompletedLoansEl = document.getElementById('statCompletedLoans');

  if (statActiveLoansEl) statActiveLoansEl.textContent = activeLoansCount;
  if (statPendingValidationEl) statPendingValidationEl.textContent = pendingValidationCount;
  if (statCompletedLoansEl) statCompletedLoansEl.textContent = completedLoansCount;

  // Render Tabel Peminjaman Terkini
  const tableBody = document.getElementById('memberRecentLoansBody');
  if (tableBody) {
    if (relevantLoans.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="4" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-book-open" style="font-size: 32px; opacity: 0.35; margin-bottom: 10px; display: block;"></i>
            <p style="font-size: 13.5px; font-weight: 600;">Belum ada riwayat peminjaman.</p>
            <a href="pinjam-online.html" style="font-size: 13px; color: #1d4ed8; font-weight: 700; margin-top: 6px; display: inline-block;">Ajukan Pinjam Sekarang &rarr;</a>
          </td>
        </tr>
      `;
    } else {
      tableBody.innerHTML = relevantLoans.map(l => {
        let badgeStyle = '';
        let icon = 'clock';
        let label = l.status;

        if (l.status === 'Dipinjam') {
          badgeStyle = 'background: rgba(29, 78, 216, 0.08); color: #1d4ed8; border: 1px solid rgba(29, 78, 216, 0.2);';
          icon = 'clock';
        } else if (l.status === 'Menunggu Validasi') {
          badgeStyle = 'background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);';
          icon = 'inbox';
        } else if (l.status === 'Disetujui') {
          badgeStyle = 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.25);';
          icon = 'circle-check';
          label = 'Siap Diambil';
        } else if (l.status === 'Dikembalikan') {
          badgeStyle = 'background: rgba(16, 185, 129, 0.08); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);';
          icon = 'check-double';
        } else if (l.status === 'Terlambat') {
          badgeStyle = 'background: rgba(239, 68, 68, 0.08); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2);';
          icon = 'triangle-exclamation';
        } else if (l.status === 'Ditolak') {
          badgeStyle = 'background: rgba(239, 68, 68, 0.08); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2);';
          icon = 'circle-xmark';
        }

        return `
          <tr>
            <td>
              <span style="font-family: monospace; font-size: 12px; font-weight: 700; color: #1d4ed8;">${l.id}</span>
              ${l.tipe === 'online' ? '<span style="display: block; font-size: 10px; color: #f59e0b; font-weight: 700; margin-top: 2px;"><i class="fa-solid fa-wifi"></i> Online</span>' : ''}
            </td>
            <td>
              <div style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">${l.judulBuku}</div>
              <div style="font-size: 11px; color: var(--text-muted);">${l.kategoriBuku}</div>
            </td>
            <td>
              <div style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">${l.jatuhTempo || '-'}</div>
              <div style="font-size: 11px; color: var(--text-muted);">${l.status === 'Dikembalikan' ? 'Kembali: ' + (l.tglKembali || '-') : (l.status === 'Menunggu Validasi' ? 'Perkiraan' : 'Batas Tempo')}</div>
            </td>
            <td style="text-align: center;">
              <span class="loan-badge" style="${badgeStyle}">
                <i class="fa-solid fa-${icon}"></i> ${label}
              </span>
            </td>
          </tr>
        `;
      }).join('');
    }
  }
});

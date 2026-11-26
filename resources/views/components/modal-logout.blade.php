<!-- ================= MODAL LOGOUT CONFIRMATION (NEUMORPHIC) ================= -->
<dialog class="modal-custom" id="modalLogoutConfirm" style="max-width: 440px;">
  <div class="modal-content" style="text-align: center; padding: 34px 28px;">
    
    <div class="modal-icon-badge danger" style="width: 56px; height: 56px; font-size: 24px; margin: 0 auto 16px auto;">
      <i class="fa-solid fa-arrow-right-from-bracket"></i>
    </div>

    <h3 style="font-size: 19px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">Keluar dari Akun?</h3>
    <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5; margin-bottom: 24px;">
      Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem DigiLib?
    </p>

    <div style="display: flex; justify-content: center; gap: 14px;">
      <button type="button" class="btn-secondary-flat modal-close-trigger" style="min-width: 100px; justify-content: center;">Batal</button>
      <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
        @csrf
        <button type="submit" class="btn-danger-action" style="min-width: 130px; justify-content: center; background: #dc2626; color: #ffffff !important; border: none;">
          <i class="fa-solid fa-arrow-right-from-bracket" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Ya, Keluar</span>
        </button>
      </form>
    </div>

  </div>
</dialog>

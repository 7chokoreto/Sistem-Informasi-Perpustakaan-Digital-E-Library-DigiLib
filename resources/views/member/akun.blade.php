@extends('layouts.app')

@section('title', 'Profil & Akun Saya')

@section('content')
<div class="content-body" style="padding-top: 32px;">
  
  <!-- Page Title Header -->
  <div style="margin-bottom: 24px;">
    <span class="badge-role superadmin" style="margin-bottom: 8px; display: inline-flex; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; border-color: rgba(29, 78, 216, 0.2);">
      <i class="fa-solid fa-user-gear"></i> Manajemen Mandiri
    </span>
    <div>
      <h1 style="font-size: 28px; font-weight: 800; color: var(--text-main); line-height: 1.2;">Profil &amp; Akun Saya</h1>
      <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">Kelola identitas, kontak telepon, email, foto profil, dan kata sandi tanpa perlu menghubungi Superadmin.</p>
    </div>
  </div>

  <!-- ================= STATUS AKUN CARD (AKTIF / DITANGGUHKAN) ================= -->
  <div class="card-neumorphic" style="padding: 24px; margin-bottom: 24px;">
    @if($user->isSuspended())
      <div style="display: flex; align-items: flex-start; gap: 18px; flex-wrap: wrap;">
        <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(220, 38, 38, 0.12); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
          <i class="fa-solid fa-ban"></i>
        </div>
        <div style="flex-grow: 1;">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
            <h2 style="font-size: 19px; font-weight: 800; color: #dc2626;">Status Keanggotaan: Ditangguhkan</h2>
            <span class="member-status-pill suspended" style="font-size: 11px;">
              <i class="fa-solid fa-lock"></i> Dibekukan
            </span>
          </div>
          <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;">
            Akun Anda saat ini ditangguhkan oleh <b>Superadmin</b>. Anda tetap dapat memperbarui profil dan melihat riwayat, namun akses peminjaman buku dibatasi.
          </p>

          <div style="background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: var(--radius-md); padding: 14px 18px; margin-top: 14px;">
            <div style="font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fa-solid fa-circle-exclamation"></i> Alasan Penangguhan dari Superadmin:
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--text-main); line-height: 1.5;">
              "{{ $user->suspension_reason ?: 'Pelanggaran ketentuan masa pengembalian buku dan tata tertib umum perpustakaan.' }}"
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
              Untuk mengaktifkan kembali akun Anda, silakan selesaikan pengembalian buku di perpustakaan atau hubungi bagian administrasi.
            </div>
          </div>
        </div>
      </div>
    @else
      <div style="display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(5, 150, 105, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
              <h2 style="font-size: 19px; font-weight: 800; color: #059669;">Status Keanggotaan: Aktif</h2>
              <span class="member-status-pill active" style="font-size: 11px;">
                <i class="fa-solid fa-shield-check"></i> Normal
              </span>
            </div>
            <p style="font-size: 13.5px; color: var(--text-muted);">
              Akun Anda dalam kondisi prima. Anda berhak meminjam buku fisik maupun mengajukan pinjaman online.
            </p>
          </div>
        </div>
        <div style="font-size: 13px; color: var(--text-muted);">
          Terdaftar Sejak: <b style="color: var(--text-main);">{{ $user->created_at->format('d M Y') }}</b>
        </div>
      </div>
    @endif
  </div>

  <!-- ================= GRID TWO-COLUMN: EDIT PROFIL & GANTI PASSWORD ================= -->
  <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px; align-items: start;">
    
    <!-- Column 1: Form Edit Informasi Profil -->
    <div class="card-neumorphic" style="padding: 28px;">
      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 22px;">
        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(29, 78, 216, 0.1); color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 18px;">
          <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
          <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main);">Informasi Pribadi</h2>
          <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Perubahan akan langsung tersimpan dan terlihat oleh Superadmin.</p>
        </div>
      </div>

      <form action="{{ route('member.akun.profile') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Hidden input for remove avatar action -->
        <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">

        <!-- Avatar Section (Upload Foto / Avatar Kosong) -->
        <div class="form-group" style="margin-bottom: 22px;">
          <label class="form-label">Foto Profil</label>
          <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <!-- Avatar Container -->
            <div style="width: 78px; height: 78px; border-radius: 50%; overflow: hidden; box-shadow: var(--shadow-flat); border: 2.5px solid white; flex-shrink: 0; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
              <img id="avatarPreviewCurrent" 
                   src="{{ $user->avatar ?: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 120' fill='%2394a3b8'><rect width='100%25' height='100%25' fill='%23e2e8f0'/><path d='M60 62c11.046 0 20-8.954 20-20s-8.954-20-20-20-20 8.954-20 20 8.954 20 20 20zm0 10c-15.56 0-40 7.82-40 23.44V102h80v-6.56C100 79.82 75.56 72 60 72z'/></svg>" }}" 
                   alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <!-- Upload & Delete Buttons -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
              <input type="file" name="avatar_file" id="photoFileInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;">
              <div style="display: flex; gap: 10px; align-items: center;">
                <button type="button" class="btn-pill btn-pill-primary" id="btnTriggerUploadPhoto" style="padding: 9px 18px; font-size: 12.5px; cursor: pointer;">
                  <i class="fa-solid fa-camera"></i>
                  <span>Unggah Foto</span>
                </button>
                <button type="button" class="btn-secondary-flat" id="btnRemovePhoto" style="padding: 9px 16px; font-size: 12.5px; color: #dc2626; cursor: pointer;">
                  <i class="fa-solid fa-trash-can"></i>
                  <span>Hapus Foto</span>
                </button>
              </div>
              <span style="font-size: 11.5px; color: var(--text-muted);">
                Format JPG, PNG, atau WEBP (Maks 5MB). Jika belum diunggah, profil menggunakan avatar kosong.
              </span>
            </div>
          </div>
        </div>

        <!-- Nama Lengkap -->
        <div class="form-group" style="margin-bottom: 18px;">
          <label class="form-label" for="profileName">Nama Lengkap <span style="color: #dc2626;">*</span></label>
          <div class="input-inset-box">
            <i class="fa-solid fa-user input-icon-left"></i>
            <input type="text" name="name" id="profileName" value="{{ old('name', $user->name) }}" class="form-input" style="padding-left: 38px;" required>
          </div>
        </div>

        <!-- Alamat Email -->
        <div class="form-group" style="margin-bottom: 18px;">
          <label class="form-label" for="profileEmail">Alamat Email <span style="color: #dc2626;">*</span></label>
          <div class="input-inset-box">
            <i class="fa-solid fa-envelope input-icon-left"></i>
            <input type="email" name="email" id="profileEmail" value="{{ old('email', $user->email) }}" class="form-input" style="padding-left: 38px;" required>
          </div>
        </div>

        <!-- Nomor Telepon / WhatsApp -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label class="form-label" for="profilePhone">Nomor WhatsApp / Telepon <span style="color: #dc2626;">*</span></label>
          <div class="input-inset-box">
            <i class="fa-brands fa-whatsapp input-icon-left"></i>
            <input type="tel" name="phone" id="profilePhone" value="{{ old('phone', $user->phone) }}" class="form-input" style="padding-left: 38px;" required>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 12px 24px; width: 100%; justify-content: center;">
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Simpan Perubahan Profil</span>
        </button>

      </form>
    </div>

    <!-- Column 2: Form Ganti Kata Sandi -->
    <div class="card-neumorphic" style="padding: 28px;">
      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 22px;">
        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(5, 150, 105, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 18px;">
          <i class="fa-solid fa-key"></i>
        </div>
        <div>
          <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main);">Keamanan Kata Sandi</h2>
          <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Perbarui kata sandi login secara berkala.</p>
        </div>
      </div>

      <form action="{{ route('member.akun.password') }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Kata Sandi Saat Ini -->
        <div class="form-group" style="margin-bottom: 18px;">
          <label class="form-label" for="pwdCurrent">Kata Sandi Saat Ini</label>
          <div class="input-inset-box">
            <i class="fa-solid fa-lock input-icon-left"></i>
            <input type="password" name="current_password" id="pwdCurrent" class="form-input" style="padding-left: 38px; padding-right: 42px;" placeholder="Masukkan kata sandi lama" required>
            <i class="fa-solid fa-eye" onclick="togglePassword('pwdCurrent', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 15px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-light)'"></i>
          </div>
        </div>

        <!-- Kata Sandi Baru -->
        <div class="form-group" style="margin-bottom: 18px;">
          <label class="form-label" for="pwdNew">Kata Sandi Baru</label>
          <div class="input-inset-box">
            <i class="fa-solid fa-shield-halved input-icon-left"></i>
            <input type="password" name="password" id="pwdNew" class="form-input" style="padding-left: 38px; padding-right: 42px;" placeholder="Minimal 6 karakter" required>
            <i class="fa-solid fa-eye" onclick="togglePassword('pwdNew', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 15px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-light)'"></i>
          </div>
        </div>

        <!-- Konfirmasi Kata Sandi Baru -->
        <div class="form-group" style="margin-bottom: 24px;">
          <label class="form-label" for="pwdConfirm">Konfirmasi Kata Sandi Baru</label>
          <div class="input-inset-box">
            <i class="fa-solid fa-check-double input-icon-left"></i>
            <input type="password" name="password_confirmation" id="pwdConfirm" class="form-input" style="padding-left: 38px; padding-right: 42px;" placeholder="Ulangi kata sandi baru" required>
            <i class="fa-solid fa-eye" onclick="togglePassword('pwdConfirm', this)" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 15px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-light)'"></i>
          </div>
        </div>

        <!-- Submit Password Button -->
        <button type="submit" class="btn-pill btn-pill-success" style="padding: 12px 24px; width: 100%; justify-content: center; background: #059669; color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);">
          <i class="fa-solid fa-lock" style="color: #ffffff !important;"></i>
          <span style="color: #ffffff !important; font-weight: 700;">Perbarui Kata Sandi</span>
        </button>

      </form>
    </div>

  </div>

</div>
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
  const DEFAULT_EMPTY = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 120' fill='%2394a3b8'><rect width='100%25' height='100%25' fill='%23e2e8f0'/><path d='M60 62c11.046 0 20-8.954 20-20s-8.954-20-20-20-20 8.954-20 20 8.954 20 20 20zm0 10c-15.56 0-40 7.82-40 23.44V102h80v-6.56C100 79.82 75.56 72 60 72z'/></svg>";
  
  const avatarPreviewCurrent = document.getElementById('avatarPreviewCurrent');
  const removeAvatarInput = document.getElementById('removeAvatarInput');
  const photoFileInput = document.getElementById('photoFileInput');
  const btnTriggerUploadPhoto = document.getElementById('btnTriggerUploadPhoto');
  const btnRemovePhoto = document.getElementById('btnRemovePhoto');

  if (btnTriggerUploadPhoto && photoFileInput) {
    btnTriggerUploadPhoto.addEventListener('click', () => {
      photoFileInput.click();
    });
  }

  if (photoFileInput) {
    photoFileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran foto terlalu besar. Maksimal 5 MB.');
        photoFileInput.value = '';
        return;
      }

      avatarPreviewCurrent.src = URL.createObjectURL(file);
      if (removeAvatarInput) removeAvatarInput.value = '0';
    });
  }

  if (btnRemovePhoto) {
    btnRemovePhoto.addEventListener('click', () => {
      avatarPreviewCurrent.src = DEFAULT_EMPTY;
      if (photoFileInput) photoFileInput.value = '';
      if (removeAvatarInput) removeAvatarInput.value = '1';
    });
  }
});
</script>
@endpush

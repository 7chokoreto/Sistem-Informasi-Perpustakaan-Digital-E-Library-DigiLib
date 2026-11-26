@extends('layouts.app')

@section('title', 'Keranjang Peminjaman')

@section('content')
<div class="page-container" style="padding-top: 32px; padding-bottom: 90px;">

  <!-- Header Banner -->
  <div class="card-neumorphic" style="padding: 24px 28px; margin-bottom: 26px;">
    <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
      <i class="fa-solid fa-bag-shopping" style="color: var(--primary); margin-right: 8px;"></i>
      Keranjang Peminjaman
    </h1>
    <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
      Yuk selesaikan peminjaman buku kamu untuk membaca di perpustakaan atau dibawa pulang.
    </p>
  </div>

  @if($cartItems->isEmpty())
    <!-- Empty State (Persis Image 4) -->
    <div class="card-neumorphic" style="padding: 70px 24px; text-align: center; border-radius: 32px;">
      
      <!-- Cart Empty Icon -->
      <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(29, 78, 216, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 44px; margin: 0 auto 24px auto; box-shadow: var(--shadow-sm);">
        <i class="fa-solid fa-cart-shopping"></i>
      </div>

      <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
        Ups, keranjang kamu kosong
      </h2>
      <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 28px;">
        Yuk tambahkan buku favorit kamu ke keranjang untuk mulai meminjam.
      </p>

      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="padding: 14px 32px; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-open"></i>
        <span>Cari buku sekarang</span>
      </a>

    </div>

  @else
    <!-- Cart Items List (Persis Image 9) -->
    <form id="cartCheckoutForm" action="{{ route('member.keranjang.checkout') }}" method="POST">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 18px; margin-bottom: 36px;">
        @foreach($cartItems as $item)
          @php $b = $item->book; @endphp
          <div class="card-neumorphic" style="padding: 22px; border-radius: 20px; display: grid; grid-template-columns: 100px 1fr auto; gap: 24px; align-items: center;">
            
            <!-- Book Cover -->
            <div style="width: 100px; height: 135px; border-radius: 12px; overflow: hidden; background: #e2e8f0; box-shadow: var(--shadow-sm);">
              @if($b && $b->cover)
                <img src="{{ $b->cover }}" alt="{{ $b->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #94a3b8; color: #fff;">
                  <i class="fa-solid fa-book" style="font-size: 28px;"></i>
                </div>
              @endif
            </div>

            <!-- Book Info & Notes Input -->
            <div>
              <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">
                {{ $b ? $b->title : 'Buku' }}
              </h3>
              <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 6px;">
                {{ $b ? $b->author : '-' }}
              </p>
              
              <div style="font-size: 12px; color: var(--primary); font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-location-dot"></i>
                <span>Lokasi Asal: {{ $item->origin_location }}</span>
              </div>

              <!-- Notes Input (Persis Image 9) -->
              <div style="max-width: 500px;">
                <input 
                  type="text" 
                  name="cart_notes[{{ $item->id }}]" 
                  value="{{ $item->notes }}" 
                  placeholder="Tambahkan catatan khusus untuk buku ini (opsional)..." 
                  class="form-input" 
                  style="width: 100%; height: 42px; border-radius: 10px; font-size: 12.5px; padding: 0 14px;"
                >
              </div>
            </div>

            <!-- Remove Button Form -->
            <div>
              <button 
                type="button" 
                onclick="document.getElementById('formRemoveItem{{ $item->id }}').submit()" 
                class="btn-secondary-flat" 
                style="width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #dc2626;" 
                title="Hapus dari Keranjang"
              >
                <i class="fa-regular fa-trash-can" style="font-size: 17px;"></i>
              </button>
            </div>

          </div>
        @endforeach
      </div>

      <!-- Sticky / Fixed Bottom Action Bar (Persis Image 9) -->
      <div style="position: fixed; bottom: 0; left: 0; right: 0; background: rgba(240, 243, 248, 0.95); backdrop-filter: blur(10px); border-top: 1px solid rgba(166, 180, 200, 0.3); padding: 16px 32px; z-index: 99; display: flex; justify-content: flex-end; align-items: center; gap: 16px; box-shadow: 0 -8px 24px rgba(0,0,0,0.06);">
        <div style="margin-right: auto; font-size: 13.5px; color: var(--text-muted);">
          Total Buku di Keranjang: <strong style="color: var(--text-main);">{{ $cartItems->count() }} Judul</strong>
        </div>

        <a href="{{ route('member.koleksi') }}" class="btn-secondary-flat" style="padding: 12px 24px; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-plus"></i>
          <span>Tambah Buku</span>
        </a>

        <button type="button" onclick="document.getElementById('modalCheckout').showModal()" class="btn-pill btn-pill-primary" style="padding: 12px 32px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
          <i class="fa-solid fa-bag-shopping"></i>
          <span>Pinjam ({{ $cartItems->count() }})</span>
        </button>
      </div>

      <!-- Modal: Checkout Peminjaman (Persis Image 10) -->
      <dialog id="modalCheckout" class="modal-custom" style="max-width: 540px;">
        <div class="modal-content" style="padding: 28px 32px;">
          
          <div class="modal-header" style="margin-bottom: 16px;">
            <div class="modal-header-left">
              <div class="modal-icon-badge" style="color: #059669;">
                <i class="fa-solid fa-bag-shopping"></i>
              </div>
              <div class="modal-title-wrap">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0;">
                  Pinjam buku ini?
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin: 2px 0 0 0;">
                  Pilih lokasi pengambilan dan konfirmasi peminjaman
                </p>
              </div>
            </div>
            <button type="button" onclick="document.getElementById('modalCheckout').close()" class="modal-close-btn" title="Tutup">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          @php
            $defaultOrigin = $cartItems->first()?->origin_location;
            $selectedAddress = $pickupLocations[0]['address'];
            foreach($pickupLocations as $ploc) {
              if ($ploc['name'] === $defaultOrigin) {
                $selectedAddress = $ploc['address'];
                break;
              }
            }
          @endphp

          <!-- Dropdown Lokasi Pengambilan (Mengikuti form tambah buku pustakawan) -->
          <div style="margin-bottom: 16px;">
            <label class="input-label-clean">Lokasi Pengambilan (Cabang Perpustakaan) <span class="required">*</span></label>
            <div class="input-inset-box select-wrapper">
              <i class="fa-solid fa-location-dot input-icon-left"></i>
              <select name="pickup_location" id="pickupLocationSelect" onchange="updatePickupAddress()" required>
                @foreach($pickupLocations as $loc)
                  <option value="{{ $loc['name'] }}" data-address="{{ $loc['address'] }}" {{ ($defaultOrigin === $loc['name']) ? 'selected' : '' }}>
                    {{ $loc['name'] }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Alamat Pengambilan Info Card (Persis Image 10) -->
          <div style="margin-bottom: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">
              Alamat Pengambilan
            </div>
            <div id="pickupAddressBox" style="background: rgba(254, 242, 242, 0.8); border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; font-size: 12.5px; color: #991b1b; line-height: 1.5;">
              {{ $selectedAddress }}
            </div>
          </div>

          <!-- Metode Pengambilan (Persis Image 10) -->
          <div style="margin-bottom: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">
              Metode Pengambilan <span style="color: #dc2626;">*</span>
            </div>
            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
              <input type="radio" name="pickup_method" value="Ambil Sendiri" checked style="accent-color: var(--primary);">
              <span>Ambil Sendiri di Meja Resepsionis</span>
            </label>
          </div>

          <!-- Ketentuan Peminjaman Box (Persis Image 10) -->
          <div style="background: rgba(241, 245, 249, 0.7); border: 1px solid rgba(166, 180, 200, 0.25); border-radius: 14px; padding: 14px 18px; margin-bottom: 18px; font-size: 12.5px; color: var(--text-muted); line-height: 1.6;">
            <ul style="margin: 0; padding-left: 18px;">
              <li>Maks. Peminjaman: <strong>14 Hari</strong></li>
              <li>Proses penyiapan buku dalam waktu <strong>2x24 jam</strong></li>
              <li>Pengambilan buku dapat dilakukan setelah status transaksi menjadi <strong>SIAP DIAMBIL</strong></li>
            </ul>
          </div>

          <!-- Checkbox Persetujuan (Persis Image 10) -->
          <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 12px; color: var(--text-main); cursor: pointer; line-height: 1.4;">
              <input type="checkbox" name="agree_terms" value="1" required style="accent-color: var(--primary); margin-top: 2px;">
              <span>Dengan ini saya menyetujui <strong>Syarat &amp; Ketentuan Peminjaman Buku</strong> yang berlaku di perpustakaan.</span>
            </label>
          </div>

          <!-- Action Buttons -->
          <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="document.getElementById('modalCheckout').close()" class="btn-secondary-flat" style="padding: 10px 20px; font-size: 13px;">
              Batal
            </button>
            <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 26px; font-size: 13.5px;">
              Pinjam Buku
            </button>
          </div>

        </div>
      </dialog>

    </form>

    <!-- Hidden Separate Remove Forms for Each Item -->
    @foreach($cartItems as $item)
      <form id="formRemoveItem{{ $item->id }}" action="{{ route('member.keranjang.remove', $item->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
      </form>
    @endforeach

  @endif

  <!-- Section: Rekomendasi Buku Lainnya -->
  @if($recommendedBooks->isNotEmpty())
    <div style="margin-top: 50px;">
      <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
        Buku Rekomendasi Lainnya
      </h2>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 18px;">
        @foreach($recommendedBooks as $rb)
          <a href="{{ route('member.buku.detail', $rb->id) }}" class="card-neumorphic" style="padding: 14px; border-radius: 16px; text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 100%; height: 220px; border-radius: 10px; overflow: hidden; background: #e2e8f0; margin-bottom: 10px;">
              @if($rb->cover)
                <img src="{{ $rb->cover }}" alt="{{ $rb->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #94a3b8; color: #fff;">
                  <i class="fa-solid fa-book" style="font-size: 32px;"></i>
                </div>
              @endif
            </div>
            <h4 style="font-size: 13.5px; font-weight: 800; color: var(--text-main); line-height: 1.3; height: 34px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 4px;">
              {{ $rb->title }}
            </h4>
            <p style="font-size: 12px; color: var(--text-muted); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              {{ $rb->author }}
            </p>
          </a>
        @endforeach
      </div>
    </div>
  @endif

</div>

@push('scripts')
<script>
function updatePickupAddress() {
  const select = document.getElementById('pickupLocationSelect');
  const selectedOption = select.options[select.selectedIndex];
  const address = selectedOption.getAttribute('data-address');
  document.getElementById('pickupAddressBox').textContent = address;
}
</script>
@endpush
@endsection

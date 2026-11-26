@extends('layouts.app')

@section('title', 'Daftar Koleksi Buku')

@section('content')
<div class="page-container" style="padding-top: 32px;">

  <!-- Header Banner -->
  <div class="card-neumorphic" style="padding: 24px 28px; margin-bottom: 26px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
        <i class="fa-solid fa-book-bookmark" style="color: var(--primary); margin-right: 8px;"></i>
        Daftar Koleksi Buku
      </h1>
      <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
        Jelajahi dan temukan ribuan koleksi buku fisik perpustakaan digital kami.
      </p>
    </div>

    <!-- Quick Stats Pill -->
    <div style="display: flex; gap: 12px; align-items: center;">
      <a href="{{ route('member.keranjang') }}" class="btn-secondary-flat" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px;">
        <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i>
        <span>Keranjang</span>
        @if(count($cartBookIds) > 0)
          <span style="background: #ef4444; color: #fff; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 10px;">{{ count($cartBookIds) }}</span>
        @endif
      </a>
      <a href="{{ route('member.favorit') }}" class="btn-secondary-flat" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px;">
        <i class="fa-solid fa-bookmark" style="color: #f59e0b;"></i>
        <span>Buku Favorit ({{ count($favoriteBookIds) }})</span>
      </a>
    </div>
  </div>

  <!-- Search & Filter Card -->
  <div class="card-neumorphic" style="padding: 20px 24px; margin-bottom: 26px;">
    <form action="{{ route('member.koleksi') }}" method="GET" style="display: flex; flex-direction: column; gap: 16px;">
      
      <!-- Search Input Bar -->
      <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px; position: relative;">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 15px;"></i>
          <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Cari berdasarkan judul buku, pengarang, atau nomor ISBN..." 
            class="form-input" 
            style="width: 100%; height: 48px; border-radius: 14px; padding-left: 44px; padding-right: 16px; font-size: 13.5px;"
          >
        </div>
        <button type="submit" class="btn-pill btn-pill-primary" style="height: 48px; padding: 0 24px; border-radius: 14px;">
          <i class="fa-solid fa-filter"></i>
          <span>Cari Koleksi</span>
        </button>
        @if(request('search') || (request('category') && request('category') !== 'all'))
          <a href="{{ route('member.koleksi') }}" class="btn-secondary-flat" style="height: 48px; display: inline-flex; align-items: center; justify-content: center; padding: 0 18px; border-radius: 14px; text-decoration: none; font-size: 13px;">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Reset</span>
          </a>
        @endif
      </div>

      <!-- Category Filter Pills -->
      <div style="display: flex; align-items: center; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-right: 4px; white-space: nowrap;">
          <i class="fa-solid fa-tags"></i> Kategori:
        </span>
        <a href="{{ route('member.koleksi', array_merge(request()->query(), ['category' => 'all'])) }}" 
           class="{{ (!request('category') || request('category') === 'all') ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
           style="padding: 6px 14px; font-size: 12px; border-radius: 20px; text-decoration: none; white-space: nowrap;">
          Semua
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('member.koleksi', array_merge(request()->query(), ['category' => $cat->name])) }}" 
             class="{{ request('category') === $cat->name ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
             style="padding: 6px 14px; font-size: 12px; border-radius: 20px; text-decoration: none; white-space: nowrap;">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>

    </form>
  </div>

  <!-- Book Grid List -->
  @if($books->isEmpty())
    <div class="card-neumorphic" style="padding: 60px 20px; text-align: center; margin-bottom: 30px;">
      <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(29, 78, 216, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 18px auto;">
        <i class="fa-solid fa-book-open"></i>
      </div>
      <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Buku Tidak Ditemukan</h3>
      <p style="font-size: 13.5px; color: var(--text-muted); max-width: 420px; margin: 0 auto 20px auto;">
        Tidak ada buku yang cocok dengan kata kunci pencarian Anda. Coba periksa ejaan atau gunakan filter kategori lainnya.
      </p>
      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-rotate-left"></i>
        <span>Tampilkan Semua Buku</span>
      </a>
    </div>
  @else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 22px; margin-bottom: 32px;">
      @foreach($books as $book)
        @php
          $isFav = in_array($book->id, $favoriteBookIds);
          $inCart = in_array($book->id, $cartBookIds);
        @endphp
        <div class="card-neumorphic" style="padding: 16px; display: flex; flex-direction: column; justify-content: space-between; position: relative; border-radius: 20px; transition: transform 0.2s ease;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
          
          <div>
            <!-- Book Cover Container -->
            <div style="position: relative; width: 100%; height: 260px; border-radius: 14px; overflow: hidden; margin-bottom: 14px; background: #e2e8f0; box-shadow: var(--shadow-sm);">
              @if($book->cover)
                <img src="{{ $book->cover }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #cbd5e1, #94a3b8); color: #ffffff; padding: 12px; text-align: center;">
                  <i class="fa-solid fa-book" style="font-size: 40px; margin-bottom: 8px; opacity: 0.85;"></i>
                  <span style="font-size: 12px; font-weight: 700; line-height: 1.3;">{{ Str::limit($book->title, 35) }}</span>
                </div>
              @endif

              <!-- Stock Availability Badge -->
              <div style="position: absolute; top: 10px; left: 10px;">
                @if($book->stock > 0)
                  <span style="background: rgba(5, 150, 105, 0.9); backdrop-filter: blur(4px); color: #ffffff; font-size: 10.5px; font-weight: 800; padding: 4px 8px; border-radius: 8px;">
                    Tersedia ({{ $book->stock }})
                  </span>
                @else
                  <span style="background: rgba(220, 38, 38, 0.9); backdrop-filter: blur(4px); color: #ffffff; font-size: 10.5px; font-weight: 800; padding: 4px 8px; border-radius: 8px;">
                    Stok Habis
                  </span>
                @endif
              </div>

              <!-- Favorite Toggle Button -->
              <form action="{{ route('member.favorit.toggle', $book->id) }}" method="POST" style="position: absolute; top: 10px; right: 10px; margin: 0;">
                @csrf
                <button type="submit" title="{{ $isFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(4px); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.15); color: {{ $isFav ? '#dc2626' : '#94a3b8' }}; transition: all 0.2s ease;">
                  <i class="{{ $isFav ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark' }}" style="font-size: 14px;"></i>
                </button>
              </form>
            </div>

            <!-- Category Pill -->
            <div style="margin-bottom: 6px;">
              <span style="background: rgba(29, 78, 216, 0.08); color: var(--primary); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; display: inline-block;">
                {{ $book->category ?? ($book->categoryRelation->name ?? 'Umum') }}
              </span>
            </div>

            <!-- Book Title -->
            <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; line-height: 1.35; height: 38px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
              <a href="{{ route('member.buku.detail', $book->id) }}" style="text-decoration: none; color: inherit;">
                {{ $book->title }}
              </a>
            </h3>

            <!-- Author -->
            <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              {{ $book->author }}
            </p>
          </div>

          <!-- Actions -->
          <div style="display: flex; gap: 8px;">
            <a href="{{ route('member.buku.detail', $book->id) }}" class="btn-pill btn-pill-primary" style="flex: 1; height: 38px; font-size: 12px; display: flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; padding: 0 10px; border-radius: 10px;">
              <i class="fa-solid fa-eye"></i>
              <span>Detail</span>
            </a>

            @if($book->stock > 0)
              @if($inCart)
                <a href="{{ route('member.keranjang') }}" class="btn-secondary-flat" style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; color: #059669;" title="Sudah di Keranjang">
                  <i class="fa-solid fa-check"></i>
                </a>
              @else
                <button type="button" onclick="openLocationModal({{ $book->id }}, '{{ addslashes($book->title) }}', '{{ addslashes($book->location ?? '') }}')" class="btn-secondary-flat" style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px;" title="Tambah ke Keranjang">
                  <i class="fa-solid fa-cart-plus" style="color: var(--primary);"></i>
                </button>
              @endif
            @endif
          </div>

        </div>
      @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px; display: flex; justify-content: center;">
      {{ $books->links() }}
    </div>
  @endif

</div>

<!-- Modal: Pilih Lokasi Asal Buku (Persis Image 7) -->
<dialog id="modalPilihLokasi" class="modal-custom" style="max-width: 520px;">
  <div class="modal-content" style="padding: 28px 30px;">
    
    <div class="modal-header" style="margin-bottom: 16px;">
      <div class="modal-header-left">
        <div class="modal-icon-badge" style="color: var(--primary);">
          <i class="fa-solid fa-location-dot"></i>
        </div>
        <div class="modal-title-wrap">
          <h3 style="font-size: 17px; font-weight: 800; color: var(--text-main); margin: 0;">
            Pilih Lokasi Asal Buku
          </h3>
          <p style="font-size: 12.5px; color: var(--text-muted); margin: 2px 0 0 0;" id="modalBookTitleTarget">
            Pilih cabang ketersediaan fisik buku
          </p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('modalPilihLokasi').close()" class="modal-close-btn" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form action="{{ route('member.keranjang.add') }}" method="POST" id="formAddCartModal">
      @csrf
      <input type="hidden" name="book_id" id="modalBookIdInput" value="">

      <!-- Pilihan Lokasi Cards -->
      <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px; max-height: 280px; overflow-y: auto; padding-right: 4px;">
        @php
          $branchList = $locations ?? [
            'Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)',
            'Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)',
            'Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kuningan)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)',
            'Perpustakaan Pusat DigiLib',
          ];
        @endphp
        @foreach($branchList as $index => $loc)
          <label class="location-option" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 14px; background: var(--bg-main); box-shadow: inset 1px 1px 3px rgba(166, 180, 200, 0.25), inset -1px -1px 3px rgba(255, 255, 255, 0.6); border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 12px;">
              <input type="radio" name="origin_location" value="{{ $loc }}" {{ $index === 0 ? 'checked' : '' }} style="accent-color: var(--primary); width: 16px; height: 16px;">
              <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">{{ $loc }}</span>
            </div>
            <i class="fa-solid fa-arrow-right" style="color: var(--text-light); font-size: 12px;"></i>
          </label>
        @endforeach
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" onclick="document.getElementById('modalPilihLokasi').close()" class="btn-secondary-flat" style="padding: 10px 20px; font-size: 13px;">
          Batal
        </button>
        <button type="submit" class="btn-pill btn-pill-primary" style="padding: 10px 24px; font-size: 13.5px;">
          <i class="fa-solid fa-cart-plus"></i>
          <span>Masukkan ke Keranjang</span>
        </button>
      </div>

    </form>
  </div>
</dialog>

<!-- Modal: Sukses Masuk Keranjang (Persis Image 8) -->
@if(session('cart_success'))
<dialog id="modalCartSuccess" class="modal-custom" open style="max-width: 440px;">
  <div class="modal-content" style="padding: 32px 28px; text-align: center;">
    
    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(5, 150, 105, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 16px auto; box-shadow: 2px 2px 6px rgba(166, 180, 200, 0.3);">
      <i class="fa-solid fa-circle-check"></i>
    </div>

    <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
      Berhasil Masuk Keranjang
    </h3>
    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 22px;">
      {{ session('cart_success') }}
    </p>

    <div style="display: flex; gap: 10px; justify-content: center;">
      <button type="button" onclick="document.getElementById('modalCartSuccess').close()" class="btn-secondary-flat" style="padding: 10px 18px; font-size: 13px;">
        Lanjut Cari Buku
      </button>
      <a href="{{ route('member.keranjang') }}" class="btn-pill btn-pill-primary" style="padding: 10px 22px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-bag-shopping"></i>
        <span>Buka Keranjang</span>
      </a>
    </div>

  </div>
</dialog>
@endif

@push('scripts')
<script>
function openLocationModal(bookId, bookTitle, bookLocation = '') {
  document.getElementById('modalBookIdInput').value = bookId;
  document.getElementById('modalBookTitleTarget').innerHTML = `Pilih cabang ketersediaan fisik buku <strong>${bookTitle}</strong>:`;
  if (bookLocation) {
    const radio = document.querySelector(`input[name="origin_location"][value="${bookLocation}"]`);
    if (radio) {
      radio.checked = true;
    }
  }
  document.getElementById('modalPilihLokasi').showModal();
}
</script>
@endpush
@endsection

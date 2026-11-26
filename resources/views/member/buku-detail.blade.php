@extends('layouts.app')

@section('title', $book->title . ' - Detail Buku')

@section('content')
<div class="page-container" style="padding-top: 32px;">

  <!-- Back button -->
  <div style="margin-bottom: 20px;">
    <a href="{{ route('member.koleksi') }}" class="btn-secondary-flat" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; padding: 8px 16px;">
      <i class="fa-solid fa-arrow-left"></i>
      <span>Kembali ke Daftar Koleksi</span>
    </a>
  </div>

  <!-- Main Detail Card -->
  <div class="card-neumorphic" style="padding: 32px; margin-bottom: 32px;">
    
    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 40px; align-items: start;">
      
      <!-- Left Column: Cover & Primary Buttons -->
      <div>
        <!-- Cover -->
        <div style="width: 100%; height: 420px; border-radius: 20px; overflow: hidden; background: #e2e8f0; box-shadow: var(--shadow-sm); margin-bottom: 20px;">
          @if($book->cover)
            <img src="{{ $book->cover }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
          @else
            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #cbd5e1, #94a3b8); color: #ffffff; padding: 20px; text-align: center;">
              <i class="fa-solid fa-book" style="font-size: 64px; margin-bottom: 14px; opacity: 0.85;"></i>
              <span style="font-size: 16px; font-weight: 800; line-height: 1.3;">{{ $book->title }}</span>
            </div>
          @endif
        </div>

        <!-- Action Buttons (Persis Image 1) -->
        <div style="display: flex; flex-direction: column; gap: 10px;">
          
          <!-- Tombol Pinjam Buku Ini (Primary) -->
          @if($book->stock > 0)
            @if($isInCart)
              <a href="{{ route('member.keranjang') }}" class="btn-pill btn-pill-success" style="height: 46px; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; font-size: 14px; background: #059669; color: #ffffff !important; border: none;">
                <i class="fa-solid fa-check" style="color: #ffffff !important;"></i>
                <span style="color: #ffffff !important; font-weight: 700;">Sudah di Keranjang (Buka)</span>
              </a>
            @else
              <button type="button" onclick="document.getElementById('modalPilihLokasi').showModal()" class="btn-pill btn-pill-primary" style="height: 46px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; cursor: pointer;">
                <i class="fa-solid fa-plus"></i>
                <span>Pinjam buku ini</span>
              </button>
            @endif
          @else
            <button type="button" disabled class="btn-secondary-flat" style="height: 46px; opacity: 0.6; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 8px;">
              <i class="fa-solid fa-ban"></i>
              <span>Stok Sedang Habis</span>
            </button>
          @endif

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <!-- Favorit Button -->
            <form action="{{ route('member.favorit.toggle', $book->id) }}" method="POST" style="margin: 0;">
              @csrf
              <button type="submit" class="btn-secondary-flat" style="width: 100%; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; color: {{ $isFavorited ? '#dc2626' : 'var(--text-main)' }};">
                <i class="{{ $isFavorited ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark' }}" style="color: {{ $isFavorited ? '#dc2626' : '#f59e0b' }};"></i>
                <span>{{ $isFavorited ? 'Favorit Saya' : 'Ke Favorit' }}</span>
              </button>
            </form>

            <!-- Bagikan Button -->
            <button type="button" onclick="document.getElementById('modalShareBook').showModal()" class="btn-secondary-flat" style="height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; cursor: pointer;">
              <i class="fa-solid fa-share-nodes" style="color: var(--primary);"></i>
              <span>Bagikan</span>
            </button>
          </div>

        </div>

      </div>

      <!-- Right Column: Detail Metadata, Cabang, Sinopsis -->
      <div>
        
        <!-- Header Info -->
        <div style="margin-bottom: 20px;">
          <h1 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 6px; line-height: 1.3;">
            {{ $book->title }}
          </h1>
          <p style="font-size: 14.5px; color: var(--text-muted); margin-bottom: 12px;">
            Penulis: <strong style="color: var(--text-main);">{{ $book->author }}</strong>
          </p>
          <span style="background: rgba(29, 78, 216, 0.08); color: var(--primary); font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 8px; display: inline-block;">
            {{ $book->category ?? ($book->categoryRelation->name ?? 'Umum') }}
          </span>
        </div>

        <!-- Metadata Table (Persis Image 1 & User Request) -->
        <div style="background: rgba(241, 245, 249, 0.6); border-radius: 16px; padding: 18px 22px; margin-bottom: 26px; font-size: 13px; line-height: 1.8;">
          <table style="width: 100%; border-collapse: collapse;">
            @if(!empty($book->edition))
            <tr>
              <td style="width: 140px; color: var(--text-muted); font-weight: 600;">Edisi</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->edition }}</td>
            </tr>
            @endif
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">Penerbit</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->publisher ?: 'DigiLib Media' }}, {{ $book->publication_year ?: date('Y') }}</td>
            </tr>
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">Deskripsi Fisik</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->physical_description ?: 'viii + 240 hlm. : Ilustrasi ; 21 cm.' }}</td>
            </tr>
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">ISBN</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->isbn ?: '978-602-000-000-0' }}</td>
            </tr>
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">Subjek</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->category ?: 'Fiksi & Sastra' }}</td>
            </tr>
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">Bahasa</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->language ?: 'Indonesia' }}</td>
            </tr>
            <tr>
              <td style="color: var(--text-muted); font-weight: 600;">Nomor Rak / Call</td>
              <td style="color: var(--text-main); font-weight: 700;">{{ $book->shelf ?: 'J-02-251' }}</td>
            </tr>
          </table>
        </div>

        <!-- Section: Tersedia di (Persis Image 1 & 2) -->
        <div style="margin-bottom: 26px;">
          <h3 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
            <span>Tersedia di:</span>
          </h3>

          <div class="card-neumorphic" style="padding: 16px 20px; border-radius: 14px;">
            <div style="font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
              {{ $book->location ?: 'Perpustakaan Jakarta Timur - Jatinegara' }}
            </div>
            <div style="display: flex; gap: 14px; font-size: 12.5px; flex-wrap: wrap;">
              <span style="color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-circle-check"></i> Dapat Dipinjam: {{ $book->stock }}
              </span>
              <span style="color: var(--text-muted); font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-book-reader"></i> Baca di Tempat: {{ $book->read_in_place_stock ?? 2 }}
              </span>
              <span style="color: var(--text-muted); font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-clock"></i> Sedang Dipinjam: {{ $book->loans()->where('status', 'Dipinjam')->count() }}
              </span>
            </div>
          </div>
        </div>

        <!-- Section: Deskripsi -->
        <div>
          <h3 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
            Deskripsi
          </h3>
          <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.7; margin: 0;">
            {{ $book->synopsis ?: 'Buku ini menyajikan wawasan mendalam mengenai topik terkait, disusun dengan gaya bahasa yang mudah dipahami serta dilengkapi dengan contoh praktis dan studi kasus aktual yang relevan untuk pembaca modern.' }}
          </p>
        </div>

      </div>

    </div>

  </div>

  <!-- Buku Rekomendasi Lainnya (Persis Image 1) -->
  @if($otherBooks->isNotEmpty())
    <div style="margin-top: 40px;">
      <h2 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
        Buku Rekomendasi Lainnya
      </h2>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 18px;">
        @foreach($otherBooks as $ob)
          <a href="{{ route('member.buku.detail', $ob->id) }}" class="card-neumorphic" style="padding: 12px; border-radius: 16px; text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 100%; height: 210px; border-radius: 10px; overflow: hidden; background: #e2e8f0; margin-bottom: 10px;">
              @if($ob->cover)
                <img src="{{ $ob->cover }}" alt="{{ $ob->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #94a3b8; color: #fff;">
                  <i class="fa-solid fa-book" style="font-size: 28px;"></i>
                </div>
              @endif
            </div>
            <div style="font-size: 10.5px; font-weight: 700; color: var(--primary); margin-bottom: 3px;">
              {{ $ob->category ?: 'Umum' }}
            </div>
            <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); line-height: 1.3; height: 34px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 4px;">
              {{ $ob->title }}
            </h4>
            <p style="font-size: 11.5px; color: var(--text-muted); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              {{ $ob->author }}
            </p>
          </a>
        @endforeach
      </div>
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
          <p style="font-size: 12.5px; color: var(--text-muted); margin: 2px 0 0 0;">
            Ketersediaan fisik buku <strong>{{ $book->title }}</strong>
          </p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('modalPilihLokasi').close()" class="modal-close-btn" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form action="{{ route('member.keranjang.add') }}" method="POST">
      @csrf
      <input type="hidden" name="book_id" value="{{ $book->id }}">

      <!-- Pilihan Lokasi Cards -->
      <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px; max-height: 300px; overflow-y: auto; padding-right: 4px;">
        @foreach($locations as $index => $loc)
          @php
            $isMainBranch = ($loc === ($book->location ?: $locations[0]));
          @endphp
          <label class="location-option" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 14px; background: var(--bg-main); box-shadow: inset 1px 1px 3px rgba(166, 180, 200, 0.25), inset -1px -1px 3px rgba(255, 255, 255, 0.6); border: 2px solid {{ $isMainBranch ? 'var(--primary)' : 'transparent' }}; cursor: pointer; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 12px;">
              <input type="radio" name="origin_location" value="{{ $loc }}" {{ $isMainBranch ? 'checked' : '' }} style="accent-color: var(--primary); width: 16px; height: 16px;">
              <div>
                <span style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block;">{{ $loc }}</span>
                @if($loc === $book->location)
                  <span style="font-size: 11px; font-weight: 700; color: #059669; display: inline-block; margin-top: 2px;">
                    <i class="fa-solid fa-check-circle"></i> Stok Tersedia: {{ $book->stock }} eks.
                  </span>
                @else
                  <span style="font-size: 11px; color: var(--text-muted); display: inline-block; margin-top: 2px;">
                    Transfer dari cabang ini
                  </span>
                @endif
              </div>
            </div>
            <i class="fa-solid fa-arrow-right" style="color: {{ $isMainBranch ? 'var(--primary)' : 'var(--text-light)' }}; font-size: 12px;"></i>
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

<!-- Modal: Bagikan Buku Interaktif (WA, IG, X, Salin Tautan) -->
<dialog id="modalShareBook" class="modal-custom" style="max-width: 480px;">
  <div class="modal-content" style="padding: 28px 26px;">
    
    <div class="modal-header" style="margin-bottom: 16px;">
      <div class="modal-header-left">
        <div class="modal-icon-badge" style="color: var(--primary);">
          <i class="fa-solid fa-share-nodes"></i>
        </div>
        <div class="modal-title-wrap">
          <h3 style="font-size: 17px; font-weight: 800; color: var(--text-main); margin: 0;">
            Bagikan Buku Ini
          </h3>
          <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0 0;">
            Bagikan ke teman atau media sosial
          </p>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('modalShareBook').close()" class="modal-close-btn" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
      Bagikan tautan buku <strong>"{{ $book->title }}"</strong> karya {{ $book->author }}:
    </p>

    <!-- Social Media Buttons Grid -->
    @php
      $shareUrl = url()->current();
      $shareText = "Yuk baca buku \"" . $book->title . "\" karya " . $book->author . " di DigiLib: " . $shareUrl;
    @endphp
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; text-align: center;">
      
      <!-- WhatsApp -->
      <a href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-flat" style="padding: 14px 6px; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: var(--text-main);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(37, 211, 102, 0.15); color: #25d366; display: flex; align-items: center; justify-content: center; font-size: 20px;">
          <i class="fa-brands fa-whatsapp"></i>
        </div>
        <span style="font-size: 11.5px; font-weight: 700;">WhatsApp</span>
      </a>

      <!-- X / Twitter -->
      <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($shareText) }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-flat" style="padding: 14px 6px; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: var(--text-main);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(0, 0, 0, 0.08); color: #0f172a; display: flex; align-items: center; justify-content: center; font-size: 18px;">
          <i class="fa-brands fa-x-twitter"></i>
        </div>
        <span style="font-size: 11.5px; font-weight: 700;">X (Twitter)</span>
      </a>

      <!-- Telegram -->
      <a href="https://t.me/share/url?url={{ rawurlencode($shareUrl) }}&text={{ rawurlencode($book->title) }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-flat" style="padding: 14px 6px; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: var(--text-main);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(0, 136, 204, 0.12); color: #0088cc; display: flex; align-items: center; justify-content: center; font-size: 20px;">
          <i class="fa-brands fa-telegram"></i>
        </div>
        <span style="font-size: 11.5px; font-weight: 700;">Telegram</span>
      </a>

      <!-- Facebook -->
      <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-flat" style="padding: 14px 6px; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: var(--text-main);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(24, 119, 242, 0.12); color: #1877f2; display: flex; align-items: center; justify-content: center; font-size: 20px;">
          <i class="fa-brands fa-facebook-f"></i>
        </div>
        <span style="font-size: 11.5px; font-weight: 700;">Facebook</span>
      </a>

    </div>

    <!-- Salin Tautan Section -->
    <div style="background: rgba(241, 245, 249, 0.7); border: 1px solid rgba(166, 180, 200, 0.25); border-radius: 14px; padding: 6px 6px 6px 14px; display: flex; align-items: center; gap: 10px;">
      <input type="text" id="shareUrlInput" value="{{ $shareUrl }}" readonly style="flex: 1; border: none; background: transparent; font-size: 12.5px; color: var(--text-muted); outline: none;">
      <button type="button" onclick="copyShareUrl()" id="btnCopyLink" class="btn-pill btn-pill-primary" style="padding: 8px 18px; font-size: 12.5px; white-space: nowrap; cursor: pointer;">
        <i class="fa-regular fa-copy"></i>
        <span id="btnCopyText">Salin Tautan</span>
      </button>
    </div>

  </div>
</dialog>

@push('scripts')
<script>
function copyShareUrl() {
  const input = document.getElementById('shareUrlInput');
  input.select();
  input.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(input.value);

  const btn = document.getElementById('btnCopyLink');
  const text = document.getElementById('btnCopyText');
  
  btn.style.background = '#059669';
  text.textContent = 'Tersalin! ✔';

  setTimeout(() => {
    btn.style.background = '';
    text.textContent = 'Salin Tautan';
  }, 2500);
}
</script>
@endpush

@endsection

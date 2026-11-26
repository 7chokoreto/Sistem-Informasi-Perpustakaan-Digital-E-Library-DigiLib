@extends('layouts.app')

@section('title', 'Buku Favorit')

@section('content')
<div class="page-container" style="padding-top: 32px;">

  <!-- Header Banner -->
  <div class="card-neumorphic" style="padding: 24px 28px; margin-bottom: 26px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
        <i class="fa-solid fa-bookmark" style="color: #f59e0b; margin-right: 8px;"></i>
        Buku Favorit Saya
      </h1>
      <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
        Kumpulan buku yang telah Anda simpan ke dalam daftar bacaan favorit.
      </p>
    </div>

    <div>
      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px;">
        <i class="fa-solid fa-plus"></i>
        <span>Jelajahi Koleksi</span>
      </a>
    </div>
  </div>

  @if($favorites->isEmpty())
    <!-- Empty State (Persis Image 3) -->
    <div class="card-neumorphic" style="padding: 70px 24px; text-align: center; border-radius: 32px;">
      
      <!-- Heart/Bookmark Empty Icon -->
      <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 44px; margin: 0 auto 24px auto; box-shadow: var(--shadow-sm);">
        <i class="fa-regular fa-bookmark"></i>
      </div>

      <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
        Ups, kamu belum punya buku favorit
      </h2>
      <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 28px;">
        Ayo mulai cari buku yang ingin dipinjam dan simpan ke daftar favorit Anda.
      </p>

      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="padding: 14px 32px; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-open"></i>
        <span>Cari buku sekarang</span>
      </a>

    </div>

  @else
    <!-- Favorites Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 22px; margin-bottom: 32px;">
      @foreach($favorites as $fav)
        @php 
          $book = $fav->book; 
          $inCart = in_array($book->id, $cartBookIds);
        @endphp
        <div class="card-neumorphic" style="padding: 16px; display: flex; flex-direction: column; justify-content: space-between; border-radius: 20px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
          
          <div>
            <!-- Book Cover -->
            <div style="position: relative; width: 100%; height: 260px; border-radius: 14px; overflow: hidden; margin-bottom: 14px; background: #e2e8f0; box-shadow: var(--shadow-sm);">
              @if($book->cover)
                <img src="{{ $book->cover }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #cbd5e1, #94a3b8); color: #ffffff; padding: 12px; text-align: center;">
                  <i class="fa-solid fa-book" style="font-size: 40px; margin-bottom: 8px;"></i>
                  <span style="font-size: 12px; font-weight: 700;">{{ Str::limit($book->title, 35) }}</span>
                </div>
              @endif

              <!-- Stock Badge -->
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

              <!-- Unfavorite Form -->
              <form action="{{ route('member.favorit.toggle', $book->id) }}" method="POST" style="position: absolute; top: 10px; right: 10px; margin: 0;">
                @csrf
                <button type="submit" title="Hapus dari Favorit" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255, 255, 255, 0.9); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.15); color: #dc2626;">
                  <i class="fa-solid fa-bookmark" style="font-size: 14px;"></i>
                </button>
              </form>
            </div>

            <!-- Category -->
            <div style="margin-bottom: 6px;">
              <span style="background: rgba(29, 78, 216, 0.08); color: var(--primary); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; display: inline-block;">
                {{ $book->category ?: 'Umum' }}
              </span>
            </div>

            <!-- Title -->
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
            <a href="{{ route('member.buku.detail', $book->id) }}" class="btn-pill btn-pill-primary" style="flex: 1; height: 38px; font-size: 12px; display: flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; border-radius: 10px;">
              <i class="fa-solid fa-eye"></i>
              <span>Detail</span>
            </a>

            @if($book->stock > 0)
              @if($inCart)
                <a href="{{ route('member.keranjang') }}" class="btn-secondary-flat" style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; color: #059669;" title="Sudah di Keranjang">
                  <i class="fa-solid fa-check"></i>
                </a>
              @else
                <form action="{{ route('member.keranjang.add') }}" method="POST" style="margin: 0;">
                  @csrf
                  <input type="hidden" name="book_id" value="{{ $book->id }}">
                  <input type="hidden" name="origin_location" value="Perpustakaan Jakarta Timur - Jatinegara">
                  <button type="submit" class="btn-secondary-flat" style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px;" title="Tambah ke Keranjang">
                    <i class="fa-solid fa-cart-plus" style="color: var(--primary);"></i>
                  </button>
                </form>
              @endif
            @endif
          </div>

        </div>
      @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px; display: flex; justify-content: center;">
      {{ $favorites->links() }}
    </div>
  @endif

</div>
@endsection

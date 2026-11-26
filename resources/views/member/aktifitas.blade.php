@extends('layouts.app')

@section('title', 'Aktifitas & Riwayat Peminjaman')

@section('content')
<div class="page-container" style="padding-top: 32px;">

  <!-- Header Banner -->
  <div class="card-neumorphic" style="padding: 24px 28px; margin-bottom: 26px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i>
        Aktifitas Peminjaman
      </h1>
      <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
        Pantau status pengajuan buku, jadwal pengambilan fisik, dan riwayat peminjaman Anda.
      </p>
    </div>

    <div>
      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px;">
        <i class="fa-solid fa-plus"></i>
        <span>Pinjam Buku Baru</span>
      </a>
    </div>
  </div>

  <!-- Filter Status Tabs -->
  <div class="card-neumorphic" style="padding: 14px 20px; margin-bottom: 26px; display: flex; gap: 10px; overflow-x: auto;">
    <a href="{{ route('member.aktifitas', ['status' => 'all']) }}" 
       class="{{ (!request('status') || request('status') === 'all') ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
       style="padding: 8px 18px; font-size: 13px; text-decoration: none; border-radius: 20px; white-space: nowrap;">
      Semua ({{ $loans->total() }})
    </a>
    <a href="{{ route('member.aktifitas', ['status' => 'berjalan']) }}" 
       class="{{ request('status') === 'berjalan' ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
       style="padding: 8px 18px; font-size: 13px; text-decoration: none; border-radius: 20px; white-space: nowrap;">
      Sedang Berjalan ({{ $totalActive }})
    </a>
    <a href="{{ route('member.aktifitas', ['status' => 'selesai']) }}" 
       class="{{ request('status') === 'selesai' ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
       style="padding: 8px 18px; font-size: 13px; text-decoration: none; border-radius: 20px; white-space: nowrap;">
      Selesai Dikembalikan ({{ $totalCompleted }})
    </a>
    <a href="{{ route('member.aktifitas', ['status' => 'ditolak']) }}" 
       class="{{ request('status') === 'ditolak' ? 'btn-pill btn-pill-primary' : 'btn-secondary-flat' }}" 
       style="padding: 8px 18px; font-size: 13px; text-decoration: none; border-radius: 20px; white-space: nowrap;">
      Ditolak ({{ $totalRejected }})
    </a>
  </div>

  @if($loans->isEmpty())
    <!-- Empty State (Persis Image 5 & 6) -->
    <div class="card-neumorphic" style="padding: 70px 24px; text-align: center; border-radius: 32px;">
      
      <!-- Transaction Empty Icon -->
      <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(29, 78, 216, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 44px; margin: 0 auto 24px auto; box-shadow: var(--shadow-sm);">
        <i class="fa-solid fa-receipt"></i>
      </div>

      <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
        Ups, kamu belum memiliki riwayat transaksi
      </h2>
      <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 28px;">
        Ayo transaksi sekarang, nikmati kemudahan peminjaman buku dimana saja dan kapan saja.
      </p>

      <a href="{{ route('member.koleksi') }}" class="btn-pill btn-pill-primary" style="padding: 14px 32px; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-open"></i>
        <span>Cari buku sekarang</span>
      </a>

    </div>

  @else
    <!-- Transaction History List -->
    <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 32px;">
      @foreach($loans as $loan)
        @php $book = $loan->book; @endphp
        <div class="card-neumorphic" style="padding: 24px; border-radius: 22px;">
          
          <!-- Top Card Meta: Kode & Status -->
          <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(166, 180, 200, 0.25); padding-bottom: 14px; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="font-family: monospace; font-size: 14px; font-weight: 800; color: var(--primary); background: rgba(29, 78, 216, 0.08); padding: 4px 10px; border-radius: 8px;">
                {{ $loan->trx_code }}
              </span>
              <span style="font-size: 12.5px; color: var(--text-muted);">
                Diajukan pada {{ \Carbon\Carbon::parse($loan->borrow_date)->translatedFormat('d F Y') }}
              </span>
            </div>

            <!-- Status Pill -->
            <div>
              @if($loan->status === 'Menunggu Validasi')
                <span style="background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-hourglass-half"></i> Menunggu Validasi
                </span>
              @elseif($loan->status === 'Disetujui' || $loan->status === 'Siap Diambil')
                <span style="background: rgba(37, 99, 235, 0.12); color: #1d4ed8; border: 1px solid rgba(37, 99, 235, 0.3); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-box-archive"></i> Siap Diambil di Resepsionis
                </span>
              @elseif($loan->status === 'Dipinjam')
                <span style="background: rgba(5, 150, 105, 0.12); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-book-reader"></i> Sedang Dipinjam
                </span>
              @elseif($loan->status === 'Dikembalikan')
                <span style="background: rgba(100, 116, 139, 0.12); color: #475569; border: 1px solid rgba(100, 116, 139, 0.3); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-circle-check"></i> Selesai Dikembalikan
                </span>
              @elseif($loan->status === 'Ditolak')
                <span style="background: rgba(220, 38, 38, 0.12); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.3); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-circle-xmark"></i> Ditolak
                </span>
              @else
                <span style="background: rgba(100, 116, 139, 0.1); color: var(--text-main); font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px;">
                  {{ $loan->status }}
                </span>
              @endif
            </div>
          </div>

          <!-- Card Content Body -->
          <div style="display: grid; grid-template-columns: 85px 1fr auto; gap: 20px; align-items: start;">
            
            <!-- Book Cover -->
            <div style="width: 85px; height: 115px; border-radius: 10px; overflow: hidden; background: #e2e8f0; box-shadow: var(--shadow-sm);">
              @if($book && $book->cover)
                <img src="{{ $book->cover }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #94a3b8; color: #fff;">
                  <i class="fa-solid fa-book" style="font-size: 26px;"></i>
                </div>
              @endif
            </div>

            <!-- Details -->
            <div>
              <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">
                {{ $book ? $book->title : 'Buku Dihapus' }}
              </h3>
              <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
                Penulis: {{ $book ? $book->author : '-' }} &bull; Kategori: {{ $book ? ($book->category ?: 'Umum') : '-' }}
              </p>

              <!-- Locations Info -->
              <div style="display: flex; flex-direction: column; gap: 4px; font-size: 12.5px; color: var(--text-muted);">
                <div>
                  <i class="fa-solid fa-map-pin" style="color: #ef4444; width: 16px;"></i>
                  Lokasi Pengambilan: <strong style="color: var(--text-main);">{{ $loan->pickup_location ?: 'Perpustakaan Pusat DigiLib' }}</strong> (Metode: {{ $loan->pickup_method ?: 'Ambil Sendiri' }})
                </div>
                @if($loan->origin_location)
                  <div>
                    <i class="fa-solid fa-building-columns" style="color: var(--primary); width: 16px;"></i>
                    Lokasi Asal Buku: <span>{{ $loan->origin_location }}</span>
                  </div>
                @endif
              </div>

              <!-- Notes -->
              @if($loan->notes)
                <div style="margin-top: 10px; font-size: 12px; color: var(--text-muted); background: rgba(241, 245, 249, 0.6); padding: 8px 12px; border-radius: 8px;">
                  <i class="fa-regular fa-note-sticky" style="color: var(--primary); margin-right: 4px;"></i>
                  Catatan Peminjam: "{{ $loan->notes }}"
                </div>
              @endif

              <!-- Rejection Reason -->
              @if($loan->status === 'Ditolak' && $loan->rejection_reason)
                <div style="margin-top: 10px; font-size: 12px; color: #dc2626; background: rgba(220, 38, 38, 0.08); border-left: 3px solid #dc2626; padding: 8px 12px; border-radius: 0 8px 8px 0;">
                  <i class="fa-solid fa-circle-exclamation" style="margin-right: 4px;"></i>
                  Alasan Penolakan: "{{ $loan->rejection_reason }}"
                </div>
              @endif
            </div>

            <!-- Due Date & Duration Info -->
            <div style="text-align: right; min-width: 160px;">
              <div style="font-size: 11.5px; color: var(--text-muted); margin-bottom: 2px;">
                Batas Pengembalian (14 Hari)
              </div>
              <div style="font-size: 14px; font-weight: 800; color: #dc2626; margin-bottom: 10px;">
                {{ \Carbon\Carbon::parse($loan->due_date)->translatedFormat('d F Y') }}
              </div>

              @if($book)
                <a href="{{ route('member.buku.detail', $book->id) }}" class="btn-secondary-flat" style="font-size: 12px; padding: 6px 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-eye"></i>
                  <span>Lihat Buku</span>
                </a>
              @endif
            </div>

          </div>

        </div>
      @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px; display: flex; justify-content: center;">
      {{ $loans->links() }}
    </div>
  @endif

</div>
@endsection

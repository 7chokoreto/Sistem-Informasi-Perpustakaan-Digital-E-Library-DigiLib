<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;

class MemberController extends Controller
{
    /**
     * 1. Beranda Member (Dashboard)
     */
    public function dashboard()
    {
        $user = auth()->user();

        $activeLoansCount = Loan::where('user_id', $user->id)
            ->whereIn('status', ['Dipinjam', 'Terlambat'])
            ->count();

        $pendingValidationCount = Loan::where('user_id', $user->id)
            ->where('status', 'Menunggu Validasi')
            ->count();

        $completedLoansCount = Loan::where('user_id', $user->id)
            ->where('status', 'Dikembalikan')
            ->count();

        $favoritesCount = Favorite::where('user_id', $user->id)->count();
        $cartCount = CartItem::where('user_id', $user->id)->count();

        $recentLoans = Loan::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recommendedBooks = Book::latest()->take(8)->get();

        return view('member.dashboard', compact(
            'user',
            'activeLoansCount',
            'pendingValidationCount',
            'completedLoansCount',
            'favoritesCount',
            'cartCount',
            'recentLoans',
            'recommendedBooks'
        ));
    }

    /**
     * 2. Daftar Koleksi Buku
     */
    public function koleksi(Request $request)
    {
        $user = auth()->user();

        $query = Book::query();

        // Filter Kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search Bar (Judul, Pengarang, ISBN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(16)->withQueryString();
        $categories = Category::all();
        $favoriteBookIds = Favorite::where('user_id', $user->id)->pluck('book_id')->toArray();
        $cartBookIds = CartItem::where('user_id', $user->id)->pluck('book_id')->toArray();

        $locations = [
            'Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)',
            'Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)',
            'Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kuningan)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)',
            'Perpustakaan Pusat DigiLib',
        ];

        return view('member.koleksi', compact('user', 'books', 'categories', 'favoriteBookIds', 'cartBookIds', 'locations'));
    }

    /**
     * Detail Buku Lengkap (seperti referensi Image 1)
     */
    public function detailBuku($id)
    {
        $user = auth()->user();
        $book = Book::findOrFail($id);

        $isFavorited = Favorite::where('user_id', $user->id)->where('book_id', $book->id)->exists();
        $isInCart = CartItem::where('user_id', $user->id)->where('book_id', $book->id)->exists();

        // Rekomendasi buku lainnya
        $otherBooks = Book::where('id', '!=', $book->id)->latest()->take(8)->get();

        // Daftar cabang resmi perpustakaan DigiLib
        $defaultLocations = [
            'Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)',
            'Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)',
            'Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kuningan)',
            'Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)',
            'Perpustakaan Pusat DigiLib',
        ];

        // Jika buku memiliki lokasi spesifik, letakkan di urutan paling atas
        $bookLocation = $book->location ?: 'Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)';
        $locations = array_values(array_unique(array_merge([$bookLocation], $defaultLocations)));

        return view('member.buku-detail', compact('user', 'book', 'isFavorited', 'isInCart', 'otherBooks', 'locations'));
    }

    /**
     * 3. Buku Favorit
     */
    public function favorit()
    {
        $user = auth()->user();
        $favorites = Favorite::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        $cartBookIds = CartItem::where('user_id', $user->id)->pluck('book_id')->toArray();

        return view('member.favorit', compact('user', 'favorites', 'cartBookIds'));
    }

    /**
     * Toggle Buku Favorit (Add/Remove)
     */
    public function toggleFavorite($id, Request $request)
    {
        $user = auth()->user();
        $book = Book::findOrFail($id);

        $favorite = Favorite::where('user_id', $user->id)->where('book_id', $book->id)->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = "Buku '{$book->title}' dihapus dari Buku Favorit.";
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
            $status = 'added';
            $message = "Buku '{$book->title}' berhasil ditambahkan ke Buku Favorit!";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'count' => Favorite::where('user_id', $user->id)->count(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * 4. Keranjang Peminjaman
     */
    public function keranjang()
    {
        $user = auth()->user();
        $cartItems = CartItem::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $recommendedBooks = Book::latest()->take(4)->get();

        $pickupLocations = [
            [
                'name' => 'Perpustakaan DigiLib - Jakarta Timur (Utan Kayu)',
                'address' => 'Jl. Utan Kayu Raya No. 45, Matraman, Jakarta Timur 13120',
            ],
            [
                'name' => 'Perpustakaan DigiLib - Kab. Tangerang (Tigaraksa)',
                'address' => 'Kawasan Pusat Pemerintahan Kab. Tangerang, Jl. Somawinata No. 12, Tigaraksa 15720',
            ],
            [
                'name' => 'Perpustakaan DigiLib - Tangerang Selatan (Emerald Bintaro)',
                'address' => 'Kawasan Bintaro Jaya Sektor 9, Jl. Boulevard Bintaro Jaya No. 8, Tangerang Selatan 15229',
            ],
            [
                'name' => 'Perpustakaan DigiLib - Jakarta Selatan (Kuningan)',
                'address' => 'Gedung Nyi Ageng Serang Lt. 3, Jl. H.R. Rasuna Said Kav. C-22, Kuningan, Jakarta Selatan 12940',
            ],
            [
                'name' => 'Perpustakaan DigiLib - Jakarta Selatan (Kebayoran)',
                'address' => 'Jl. Gandaria Tengah V No. 2, Kebayoran Baru, Jakarta Selatan 12130',
            ],
        ];

        return view('member.keranjang', compact('user', 'cartItems', 'recommendedBooks', 'pickupLocations'));
    }

    /**
     * Masukkan Buku ke Keranjang Peminjaman
     */
    public function addToCart(Request $request)
    {
        $user = auth()->user();

        if ($user->status === 'suspended') {
            $reason = $user->suspension_reason ?: 'Pelanggaran tata tertib perpustakaan.';
            return redirect()->back()->with('error', "Akses Ditolak: Akun Anda sedang ditangguhkan. Alasan: \"{$reason}\"");
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'origin_location' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->stock < 1) {
            return redirect()->back()->with('error', 'Maaf, stok buku ini sedang habis dipinjam.');
        }

        // Cek apakah sudah ada di keranjang
        $existing = CartItem::where('user_id', $user->id)->where('book_id', $book->id)->first();
        if ($existing) {
            return redirect()->back()->with('info', 'Buku ini sudah berada di dalam keranjang peminjaman Anda.');
        }

        CartItem::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'origin_location' => $request->origin_location ?: 'Perpustakaan Jakarta Timur - Jatinegara',
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('cart_success', "Buku berhasil dimasukkan ke daftar peminjaman.");
    }

    /**
     * Hapus Buku dari Keranjang
     */
    public function removeFromCart($id)
    {
        $user = auth()->user();
        CartItem::where('user_id', $user->id)->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus dari keranjang peminjaman.');
    }

    /**
     * Checkout Peminjaman dari Keranjang (Modal Checkout)
     */
    public function checkoutCart(Request $request)
    {
        $user = auth()->user();

        if ($user->status === 'suspended') {
            return redirect()->back()->with('error', 'Akses Ditolak: Akun Anda sedang ditangguhkan.');
        }

        $request->validate([
            'pickup_location' => 'required|string',
            'pickup_method' => 'required|string',
            'agree_terms' => 'accepted',
            'cart_notes' => 'nullable|array',
        ], [
            'agree_terms.accepted' => 'Anda harus menyetujui syarat & ketentuan peminjaman buku.',
        ]);

        $cartItems = CartItem::with('book')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('member.keranjang')->with('error', 'Keranjang peminjaman Anda masih kosong.');
        }

        $now = Carbon::now();
        $dueDate = $now->copy()->addDays(14); // Standar 14 hari sesuai referensi

        foreach ($cartItems as $index => $item) {
            $book = $item->book;
            if (!$book || $book->stock < 1) {
                continue;
            }

            $trxCode = 'TRX-' . $now->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $itemNote = $request->input("cart_notes.{$item->id}", $item->notes);

            Loan::create([
                'trx_code' => $trxCode,
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => $now->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'Menunggu Validasi',
                'type' => 'online',
                'duration_days' => 14,
                'origin_location' => $item->origin_location,
                'pickup_location' => $request->pickup_location,
                'pickup_method' => $request->pickup_method,
                'notes' => $itemNote,
            ]);
        }

        // Kosongkan keranjang setelah checkout sukses
        CartItem::where('user_id', $user->id)->delete();

        return redirect()->route('member.aktifitas')->with('success', 'Peminjaman berhasil diajukan! Buku Anda sedang dipersiapkan oleh petugas perpustakaan.');
    }

    /**
     * 5. Aktifitas (Riwayat Peminjaman & Status Transaksi)
     */
    public function aktifitas(Request $request)
    {
        $user = auth()->user();

        $query = Loan::with('book')->where('user_id', $user->id);

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'berjalan') {
                $query->whereIn('status', ['Menunggu Validasi', 'Disetujui', 'Dipinjam']);
            } elseif ($request->status === 'selesai') {
                $query->where('status', 'Dikembalikan');
            } elseif ($request->status === 'ditolak') {
                $query->where('status', 'Ditolak');
            } else {
                $query->where('status', $request->status);
            }
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        $totalActive = Loan::where('user_id', $user->id)->whereIn('status', ['Menunggu Validasi', 'Disetujui', 'Dipinjam'])->count();
        $totalCompleted = Loan::where('user_id', $user->id)->where('status', 'Dikembalikan')->count();
        $totalRejected = Loan::where('user_id', $user->id)->where('status', 'Ditolak')->count();

        return view('member.aktifitas', compact('user', 'loans', 'totalActive', 'totalCompleted', 'totalRejected'));
    }

    /**
     * Legacy Pinjam Online Support
     */
    public function requestLoan(Request $request)
    {
        return $this->addToCart($request);
    }

    /**
     * 6. Akun Saya & Update Profil
     */
    public function akun()
    {
        $user = auth()->user();
        return view('member.akun', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $uploadDir = public_path('uploads/avatars');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);

            $data['avatar'] = asset('uploads/avatars/' . $filename);
        } elseif ($request->input('remove_avatar') === '1') {
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('member.akun')->with('success', 'Profil Anda berhasil diperbarui dan tersinkronisasi ke sistem.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Kata sandi saat ini tidak sesuai.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('member.akun')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Models\Category;

class PustakawanController extends Controller
{
    public function dashboard()
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $activeLoans = Loan::where('status', 'Dipinjam')->count();
        $todayLoans = Loan::whereDate('created_at', today())->count();
        $pendingValidations = Loan::where('status', 'Menunggu Validasi')->count();

        $recentLoans = Loan::with(['user', 'book'])->latest()->take(5)->get();

        // 1. Aktivitas Peminjaman 7 Hari Terakhir
        $days = [];
        $weeklyLoans = [];
        $weeklyReturns = [];
        
        $dayNames = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $englishDay = $date->format('l');
            $days[] = $dayNames[$englishDay] ?? $englishDay;
            $weeklyLoans[] = Loan::whereDate('borrow_date', $date->toDateString())->count();
            $weeklyReturns[] = Loan::whereDate('return_date', $date->toDateString())->count();
        }

        // 2. Proporsi Kategori Koleksi Buku dari DB
        $categories = Category::withCount('books')->get();
        $catLabels = $categories->pluck('name')->toArray();
        $catTotals = $categories->pluck('books_count')->toArray();

        return view('pustakawan.dashboard', compact(
            'totalBooks',
            'totalStock',
            'activeLoans',
            'todayLoans',
            'pendingValidations',
            'recentLoans',
            'days',
            'weeklyLoans',
            'weeklyReturns',
            'catLabels',
            'catTotals'
        ));
    }

    public function books(Request $request)
    {
        $query = Book::with('categoryRelation');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('book_code', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('pustakawan.buku', compact('books', 'categories'));
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'read_in_place_stock' => 'nullable|integer|min:0',
            'shelf' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:100',
            'publication_date' => 'nullable|date',
            'publication_year' => 'nullable|integer',
            'edition' => 'nullable|string|max:100',
            'physical_description' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'synopsis' => 'nullable|string',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $categoryName = $request->category;
        $categoryId = $request->category_id;
        if ($categoryId && !$categoryName) {
            $cat = Category::find($categoryId);
            if ($cat) $categoryName = $cat->name;
        } elseif ($categoryName && !$categoryId) {
            $cat = Category::where('name', $categoryName)->first();
            if ($cat) $categoryId = $cat->id;
        }

        $bookCode = 'BK-' . str_pad(Book::count() + 1, 3, '0', STR_PAD_LEFT);

        // Handle Cover File Upload
        $coverUrl = 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&auto=format&fit=crop&q=80';
        if ($request->hasFile('cover_file')) {
            $file = $request->file('cover_file');
            $uploadDir = public_path('uploads/books');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'cover_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $coverUrl = asset('uploads/books/' . $filename);
        }

        // Handle Publication Year from Date Picker
        $publicationYear = date('Y');
        if ($request->filled('publication_date')) {
            $publicationYear = \Carbon\Carbon::parse($request->publication_date)->year;
        } elseif ($request->filled('publication_year')) {
            $publicationYear = (int)$request->publication_year;
        }

        Book::create([
            'book_code' => $bookCode,
            'category_id' => $categoryId,
            'title' => $request->title,
            'author' => $request->author,
            'category' => $categoryName ?: 'Umum',
            'publisher' => $request->publisher ?: 'DigiLib Media',
            'publication_year' => $publicationYear,
            'edition' => $request->edition ?: 'Cetakan Pertama',
            'physical_description' => $request->physical_description ?: 'viii + 240 hlm. : Ilustrasi ; 21 cm.',
            'language' => $request->language ?: 'Indonesia',
            'isbn' => $request->isbn ?: '978-602-000-000-0',
            'shelf' => $request->shelf ?: 'J-02-251',
            'location' => $request->location ?: 'Perpustakaan Jakarta Timur - Jatinegara',
            'stock' => $request->stock,
            'read_in_place_stock' => $request->read_in_place_stock ?? 2,
            'synopsis' => $request->synopsis,
            'cover' => $coverUrl,
        ]);

        return redirect()->route('pustakawan.books')->with('success', "Buku baru \"{$request->title}\" ({$bookCode}) berhasil ditambahkan ke katalog.");
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'read_in_place_stock' => 'nullable|integer|min:0',
            'shelf' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:100',
            'publication_date' => 'nullable|date',
            'publication_year' => 'nullable|integer',
            'edition' => 'nullable|string|max:100',
            'physical_description' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'synopsis' => 'nullable|string',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $categoryName = $request->category;
        $categoryId = $request->category_id;
        if ($categoryId) {
            $cat = Category::find($categoryId);
            if ($cat) $categoryName = $cat->name;
        } elseif ($categoryName) {
            $cat = Category::where('name', $categoryName)->first();
            if ($cat) $categoryId = $cat->id;
        }

        // Handle Cover File Upload for Edit
        $coverUrl = $book->cover;
        if ($request->hasFile('cover_file')) {
            $file = $request->file('cover_file');
            $uploadDir = public_path('uploads/books');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'cover_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $coverUrl = asset('uploads/books/' . $filename);
        }

        // Handle Publication Year from Date Picker
        $publicationYear = $book->publication_year ?: date('Y');
        if ($request->filled('publication_date')) {
            $publicationYear = \Carbon\Carbon::parse($request->publication_date)->year;
        } elseif ($request->filled('publication_year')) {
            $publicationYear = (int)$request->publication_year;
        }

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'category_id' => $categoryId,
            'category' => $categoryName ?: $book->category,
            'stock' => $request->stock,
            'read_in_place_stock' => $request->read_in_place_stock ?? $book->read_in_place_stock,
            'shelf' => $request->shelf ?: $book->shelf,
            'location' => $request->location ?: $book->location,
            'isbn' => $request->isbn ?: $book->isbn,
            'publisher' => $request->publisher ?: $book->publisher,
            'publication_year' => $publicationYear,
            'edition' => $request->edition ?: $book->edition,
            'physical_description' => $request->physical_description ?: $book->physical_description,
            'language' => $request->language ?: $book->language,
            'synopsis' => $request->synopsis,
            'cover' => $coverUrl,
        ]);

        return redirect()->route('pustakawan.books')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $title = $book->title;
        $book->delete();

        return redirect()->route('pustakawan.books')->with('success', "Buku \"{$title}\" berhasil dihapus dari katalog.");
    }

    public function loans(Request $request)
    {
        $query = Loan::with(['user', 'book']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trx_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $loans = $query->latest()->get();
        $pendingCount = Loan::where('status', 'Menunggu Validasi')->count();
        $members = User::where('role', 'member')->where('status', 'active')->orderBy('name')->get();
        $availableBooks = Book::where('stock', '>', 0)->orderBy('title')->get();

        return view('pustakawan.peminjaman', compact('loans', 'pendingCount', 'members', 'availableBooks'));
    }

    public function storeLoan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:30',
        ]);

        $book = Book::findOrFail($request->book_id);
        if ($book->stock < 1) {
            return redirect()->back()->with('error', 'Stok buku ini sedang habis.');
        }

        $user = User::findOrFail($request->user_id);
        if ($user->status === 'suspended') {
            return redirect()->back()->with('error', "Peminjaman ditolak: Akun anggota {$user->name} sedang ditangguhkan.");
        }

        $borrowDate = \Carbon\Carbon::parse($request->borrow_date);
        $dueDate = $borrowDate->copy()->addDays((int)$request->duration_days);

        $trxCode = 'TRX-' . date('Y') . '-' . str_pad(Loan::count() + 101, 3, '0', STR_PAD_LEFT);

        Loan::create([
            'trx_code' => $trxCode,
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrow_date' => $borrowDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'Dipinjam',
            'type' => 'offline',
            'duration_days' => $request->duration_days,
        ]);

        $book->decrement('stock');

        return redirect()->route('pustakawan.loans')->with('success', "Peminjaman {$trxCode} berhasil dicatat.");
    }

    public function approveOnlineLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'Disetujui']);

        return redirect()->back()->with('success', "Pengajuan pinjam online {$loan->trx_code} berhasil disetujui. Siap diambil anggota di meja pelayanan.");
    }

    public function rejectOnlineLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $reason = $request->rejection_reason ?: 'Stok buku fisik sedang tidak tersedia atau dalam perbaikan.';
        
        $loan->update([
            'status' => 'Ditolak',
            'rejection_reason' => $reason,
        ]);

        return redirect()->back()->with('success', "Pengajuan pinjam {$loan->trx_code} telah ditolak dengan alasan tersimpan.");
    }

    public function handOverBook($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'Dipinjam']);

        if ($loan->book && $loan->book->stock > 0) {
            $loan->book->decrement('stock');
        }

        return redirect()->back()->with('success', "Buku fisik pada transaksi {$loan->trx_code} telah diserahkan. Status: Dipinjam.");
    }

    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        $today = now()->toDateString();
        
        // Cek denda
        $fine = 0;
        if (now()->gt(\Carbon\Carbon::parse($loan->due_date))) {
            $lateDays = now()->diffInDays(\Carbon\Carbon::parse($loan->due_date));
            $fine = $lateDays * 1000;
        }

        $loan->update([
            'status' => 'Dikembalikan',
            'return_date' => $today,
            'fine' => $fine,
        ]);

        if ($loan->book) {
            $loan->book->increment('stock');
        }

        $fineMsg = $fine > 0 ? " Denda keterlambatan: Rp " . number_format($fine, 0, ',', '.') : "";
        return redirect()->back()->with('success', "Buku pada transaksi {$loan->trx_code} berhasil diterima kembali.{$fineMsg}");
    }

    public function members(Request $request)
    {
        $members = User::where('role', 'member')->with(['loans.book'])->withCount('loans')->latest()->get();
        return view('pustakawan.anggota', compact('members'));
    }
}

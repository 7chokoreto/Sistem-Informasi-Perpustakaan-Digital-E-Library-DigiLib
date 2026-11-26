<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthAndRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('DigiLib');
    }

    public function test_superadmin_can_login_and_access_dashboard()
    {
        $superadmin = User::where('role', 'superadmin')->first();
        $this->assertNotNull($superadmin);

        $response = $this->actingAs($superadmin)->get('/superadmin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Superadmin');

        $accountsResponse = $this->actingAs($superadmin)->get('/superadmin/manajemen-akun');
        $accountsResponse->assertStatus(200);
        $accountsResponse->assertSee('Manajemen Akun');
    }

    public function test_pustakawan_can_access_pustakawan_pages()
    {
        $pustakawan = User::where('role', 'pustakawan')->first();
        $this->assertNotNull($pustakawan);

        $response = $this->actingAs($pustakawan)->get('/pustakawan/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pustakawan');

        $booksResponse = $this->actingAs($pustakawan)->get('/pustakawan/buku');
        $booksResponse->assertStatus(200);
        $booksResponse->assertSee('Katalog Buku Perpustakaan');

        $loansResponse = $this->actingAs($pustakawan)->get('/pustakawan/peminjaman');
        $loansResponse->assertStatus(200);
        $loansResponse->assertSee('Layanan Sirkulasi');

        $membersResponse = $this->actingAs($pustakawan)->get('/pustakawan/anggota');
        $membersResponse->assertStatus(200);
        $membersResponse->assertSee('Data Anggota Perpustakaan');
    }

    public function test_member_can_access_member_pages()
    {
        $member = User::where('role', 'member')->where('email', 'osassisus@gmail.com')->first();
        $this->assertNotNull($member);

        $response = $this->actingAs($member)->get('/member/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Naoto Hachioji');

        $koleksiResponse = $this->actingAs($member)->get('/member/koleksi');
        $koleksiResponse->assertStatus(200);
        $koleksiResponse->assertSee('Daftar Koleksi Buku');

        $favoritResponse = $this->actingAs($member)->get('/member/favorit');
        $favoritResponse->assertStatus(200);
        $favoritResponse->assertSee('Buku Favorit Saya');

        $keranjangResponse = $this->actingAs($member)->get('/member/keranjang');
        $keranjangResponse->assertStatus(200);
        $keranjangResponse->assertSee('Keranjang Peminjaman');

        $aktifitasResponse = $this->actingAs($member)->get('/member/aktifitas');
        $aktifitasResponse->assertStatus(200);
        $aktifitasResponse->assertSee('Aktifitas Peminjaman');

        $akunResponse = $this->actingAs($member)->get('/member/akun');
        $akunResponse->assertStatus(200);
        $akunResponse->assertSee('Profil &amp; Akun Saya', false);
    }

    public function test_role_middleware_blocks_unauthorized_access()
    {
        $member = User::where('role', 'member')->first();
        
        // Member tries to access Superadmin dashboard -> redirected to Member dashboard
        $response = $this->actingAs($member)->get('/superadmin/dashboard');
        $response->assertRedirect(route('member.dashboard'));

        // Member tries to access Pustakawan dashboard -> redirected to Member dashboard
        $response = $this->actingAs($member)->get('/pustakawan/dashboard');
        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_visitor_can_register_new_member_account_into_database()
    {
        $response = $this->post('/register', [
            'name' => 'Kurnia Ramadhan',
            'email' => 'kurnia@example.com',
            'phone' => '0812-3344-5566',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'kurnia@example.com',
            'role' => 'member',
            'status' => 'active',
        ]);
    }

    public function test_superadmin_can_create_suspend_and_delete_account_in_database()
    {
        $superadmin = User::where('role', 'superadmin')->first();

        // 1. Tambah Akun
        $createResponse = $this->actingAs($superadmin)->post('/superadmin/manajemen-akun', [
            'name' => 'Staf Baru',
            'email' => 'stafbaru@digilib.id',
            'password' => 'password123',
            'role' => 'pustakawan',
            'phone' => '0812-7777-8888',
        ]);
        $createResponse->assertRedirect(route('superadmin.accounts'));
        $this->assertDatabaseHas('users', ['email' => 'stafbaru@digilib.id', 'role' => 'pustakawan']);

        $user = User::where('email', 'stafbaru@digilib.id')->first();

        // 2. Suspend Akun dengan Alasan
        $suspendResponse = $this->actingAs($superadmin)->patch("/superadmin/manajemen-akun/{$user->id}/toggle-suspend", [
            'suspension_reason' => 'Evaluasi disiplin kerja.',
        ]);
        $suspendResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'suspended',
            'suspension_reason' => 'Evaluasi disiplin kerja.',
        ]);

        // 3. Unsuspend
        $this->actingAs($superadmin)->patch("/superadmin/manajemen-akun/{$user->id}/toggle-suspend");
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'active',
            'suspension_reason' => null,
        ]);

        // 4. Hapus Akun
        $this->actingAs($superadmin)->delete("/superadmin/manajemen-akun/{$user->id}");
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_pustakawan_can_create_update_and_delete_book_in_database()
    {
        $pustakawan = User::where('role', 'pustakawan')->first();

        // 1. Tambah Buku Baru
        $addResponse = $this->actingAs($pustakawan)->post('/pustakawan/buku', [
            'title' => 'Struktur Data & Algoritma',
            'author' => 'Dr. Rinaldi Munir',
            'category' => 'Sains & Teknologi',
            'shelf' => 'Rak D-02',
            'stock' => 10,
            'synopsis' => 'Buku teks fundamental ilmu komputer mengenai algoritma efisien.',
        ]);
        $addResponse->assertRedirect(route('pustakawan.books'));
        $this->assertDatabaseHas('books', [
            'title' => 'Struktur Data & Algoritma',
            'author' => 'Dr. Rinaldi Munir',
            'stock' => 10,
        ]);

        $book = Book::where('title', 'Struktur Data & Algoritma')->first();

        // 2. Update Buku
        $updateResponse = $this->actingAs($pustakawan)->put("/pustakawan/buku/{$book->id}", [
            'title' => 'Struktur Data & Algoritma (Edisi Revisi)',
            'author' => 'Dr. Rinaldi Munir',
            'category' => 'Sains & Teknologi',
            'shelf' => 'Rak D-03',
            'stock' => 12,
            'synopsis' => 'Revisi lengkap dengan studi kasus Python dan C++.',
        ]);
        $updateResponse->assertRedirect(route('pustakawan.books'));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Struktur Data & Algoritma (Edisi Revisi)',
            'stock' => 12,
        ]);

        // 3. Hapus Buku
        $this->actingAs($pustakawan)->delete("/pustakawan/buku/{$book->id}");
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_full_online_loan_workflow_interaction_in_database()
    {
        $member = User::where('email', 'osassisus@gmail.com')->first();
        $pustakawan = User::where('role', 'pustakawan')->first();
        
        $book = Book::create([
            'book_code' => 'BK-001',
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'category' => 'Fiksi & Sastra',
            'shelf' => 'Rak A-01',
            'stock' => 5,
        ]);
        $initialStock = $book->stock;

        // 1. Member Menambahkan Buku ke Keranjang lalu Checkout
        $addToCart = $this->actingAs($member)->post('/member/keranjang/add', [
            'book_id' => $book->id,
            'origin_location' => 'Perpustakaan Jakarta Timur - Jatinegara',
            'notes' => 'Tolong disiapkan ya.',
        ]);
        $addToCart->assertRedirect();

        $checkout = $this->actingAs($member)->post('/member/keranjang/checkout', [
            'pickup_location' => 'Perpustakaan Jakarta - Nyi Ageng Serang',
            'pickup_method' => 'Ambil Sendiri',
            'agree_terms' => 1,
        ]);
        $checkout->assertRedirect(route('member.aktifitas'));

        $loan = Loan::where('user_id', $member->id)
            ->where('book_id', $book->id)
            ->where('status', 'Menunggu Validasi')
            ->first();
        $this->assertNotNull($loan);
        $this->assertEquals('Perpustakaan Jakarta - Nyi Ageng Serang', $loan->pickup_location);

        // 2. Pustakawan Menyetujui Pengajuan Online
        $this->actingAs($pustakawan)->patch("/pustakawan/peminjaman/{$loan->id}/approve");
        $loan->refresh();
        $this->assertEquals('Disetujui', $loan->status);

        // 3. Pustakawan Menyerahkan Buku Fisik (Stok berkurang)
        $this->actingAs($pustakawan)->patch("/pustakawan/peminjaman/{$loan->id}/handover");
        $loan->refresh();
        $book->refresh();
        $this->assertEquals('Dipinjam', $loan->status);
        $this->assertEquals($initialStock - 1, $book->stock);

        // 4. Member Mengembalikan Buku (Stok bertambah kembali)
        $this->actingAs($pustakawan)->patch("/pustakawan/peminjaman/{$loan->id}/return");
        $loan->refresh();
        $book->refresh();
        $this->assertEquals('Dikembalikan', $loan->status);
        $this->assertNotNull($loan->return_date);
        $this->assertEquals($initialStock, $book->stock);
    }

    public function test_member_can_upload_profile_picture_without_memory_exhaustion()
    {
        $member = User::where('email', 'osassisus@gmail.com')->first();
        $file = \Illuminate\Http\UploadedFile::fake()->create('profile.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($member)->put('/member/akun/profile', [
            'name' => 'Naoto Hachioji Updated',
            'email' => 'osassisus@gmail.com',
            'phone' => '0857-1122-3344',
            'avatar_file' => $file,
        ]);

        $response->assertRedirect(route('member.akun'));
        $member->refresh();
        $this->assertEquals('Naoto Hachioji Updated', $member->name);
        $this->assertNotNull($member->avatar);
        $this->assertStringContainsString('uploads/avatars', $member->avatar);
    }

    public function test_forgot_password_otp_flow_works_end_to_end()
    {
        \Illuminate\Support\Facades\Mail::fake();

        // 1. Render halaman forgot password
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');

        // 2. Request OTP dengan email valid
        $postEmail = $this->post('/forgot-password', [
            'email' => 'osassisus@gmail.com',
        ]);
        $postEmail->assertRedirect(route('password.otp.show'));
        $postEmail->assertSessionHas('password_reset_email', 'osassisus@gmail.com');

        // Pastikan Mail terkirim
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ResetPasswordOtpMail::class);

        // Ambil OTP yang digenerate di database
        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', 'osassisus@gmail.com')->first();
        $this->assertNotNull($tokenRecord);
        $otp = $tokenRecord->token;

        // 3. Verifikasi OTP yang salah gagal
        $wrongOtp = $this->withSession(['password_reset_email' => 'osassisus@gmail.com'])->post('/verify-otp', [
            'otp' => '000000',
        ]);
        $wrongOtp->assertSessionHasErrors('otp');

        // 4. Verifikasi OTP yang benar berhasil
        $correctOtp = $this->withSession(['password_reset_email' => 'osassisus@gmail.com'])->post('/verify-otp', [
            'otp' => $otp,
        ]);
        $correctOtp->assertRedirect(route('password.reset.show'));
        $correctOtp->assertSessionHas('otp_verified_email', 'osassisus@gmail.com');

        // 5. Render halaman reset password
        $showReset = $this->withSession(['otp_verified_email' => 'osassisus@gmail.com'])->get('/reset-password');
        $showReset->assertStatus(200);
        $showReset->assertSee('Buat Kata Sandi Baru');

        // 6. Update password baru
        $updatePass = $this->withSession(['otp_verified_email' => 'osassisus@gmail.com'])->post('/reset-password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $updatePass->assertRedirect(route('login'));
        $updatePass->assertSessionHas('success');

        // Pastikan token dibersihkan dari database
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'osassisus@gmail.com',
        ]);

        // 7. Login dengan password baru berhasil
        $loginResponse = $this->post('/login', [
            'email' => 'osassisus@gmail.com',
            'password' => 'newpassword123',
        ]);
        $loginResponse->assertRedirect(route('member.dashboard'));
    }

    public function test_superadmin_can_create_update_and_delete_pustakawan_avatar()
    {
        $superadmin = User::where('role', 'superadmin')->first();
        $this->assertNotNull($superadmin);

        // 1. Tambah pustakawan dengan avatar
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('avatar_pustakawan.jpg', 150, 'image/jpeg');

        $createResponse = $this->actingAs($superadmin)->post(route('superadmin.accounts.store'), [
            'name' => 'Pustakawan Baru',
            'email' => 'pustakawan_baru@digilib.id',
            'password' => 'password123',
            'role' => 'pustakawan',
            'phone' => '081234567890',
            'avatar_file' => $fakeFile,
        ]);

        $createResponse->assertRedirect(route('superadmin.accounts'));
        $pustakawan = User::where('email', 'pustakawan_baru@digilib.id')->first();
        $this->assertNotNull($pustakawan);
        $this->assertNotNull($pustakawan->avatar);
        $this->assertStringContainsString('uploads/avatars/', $pustakawan->avatar);

        // 2. Ganti avatar pustakawan
        $newFakeFile = \Illuminate\Http\UploadedFile::fake()->create('avatar_baru.png', 200, 'image/png');
        $updateResponse = $this->actingAs($superadmin)->put(route('superadmin.accounts.update', $pustakawan->id), [
            'name' => 'Pustakawan Baru Updated',
            'email' => 'pustakawan_baru@digilib.id',
            'role' => 'pustakawan',
            'phone' => '081234567890',
            'avatar_file' => $newFakeFile,
        ]);

        $updateResponse->assertRedirect(route('superadmin.accounts'));
        $pustakawan->refresh();
        $this->assertEquals('Pustakawan Baru Updated', $pustakawan->name);
        $this->assertNotNull($pustakawan->avatar);

        // 3. Hapus avatar pustakawan (reset ke default)
        $deleteAvatarResponse = $this->actingAs($superadmin)->put(route('superadmin.accounts.update', $pustakawan->id), [
            'name' => 'Pustakawan Baru Updated',
            'email' => 'pustakawan_baru@digilib.id',
            'role' => 'pustakawan',
            'phone' => '081234567890',
            'delete_avatar' => '1',
        ]);

        $deleteAvatarResponse->assertRedirect(route('superadmin.accounts'));
        $pustakawan->refresh();
        $this->assertNull($pustakawan->avatar);
    }
}

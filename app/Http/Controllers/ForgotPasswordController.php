<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResetPasswordOtpMail;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan halaman permintaan lupa password (input email)
     */
    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.forgot-password');
    }

    /**
     * Proses kirim kode OTP ke email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Silakan masukkan alamat email akun Anda.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Alamat email ini tidak terdaftar dalam sistem DigiLib.',
            ])->withInput();
        }

        if ($user->status === 'suspended') {
            return back()->withErrors([
                'email' => 'Akun ini sedang ditangguhkan. Silakan hubungi petugas perpustakaan.',
            ])->withInput();
        }

        // Generate 6 digit angka acak
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Simpan token OTP ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        // Kirim email OTP
        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email OTP: ' . $e->getMessage());
        }

        // Catat di log sistem untuk memudahkan verifikasi/development
        Log::info("[DigiLib OTP] Kode OTP reset password untuk {$user->email} adalah: {$otp}");

        // Simpan email ke session untuk verifikasi step berikutnya
        session([
            'password_reset_email' => $user->email,
            'otp_sent_at' => now()->timestamp,
        ]);

        return redirect()->route('password.otp.show')->with('success', "Kode OTP 6 digit telah dikirim ke {$user->email}. Silakan cek email Anda.");
    }

    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerifyOtp()
    {
        $email = session('password_reset_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Silakan masukkan email Anda terlebih dahulu.',
            ]);
        }

        return view('auth.verify-otp', [
            'email' => $email,
        ]);
    }

    /**
     * Verifikasi kode OTP yang diinput pengguna
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berjumlah 6 digit angka.',
        ]);

        $email = session('password_reset_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi verifikasi Anda telah berakhir. Silakan ulangi proses.',
            ]);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'Kode OTP tidak ditemukan. Silakan minta kode baru.',
            ]);
        }

        // Cek kedaluwarsa 15 menit
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors([
                'otp' => 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim Ulang OTP".',
            ]);
        }

        // Cek kecocokan OTP
        if ($record->token !== $request->otp) {
            return back()->withErrors([
                'otp' => 'Kode OTP yang Anda masukkan salah. Periksa kembali email Anda.',
            ]);
        }

        // Berhasil verifikasi OTP
        session([
            'otp_verified_email' => $email,
        ]);

        return redirect()->route('password.reset.show')->with('success', 'Kode OTP berhasil diverifikasi! Silakan tentukan kata sandi baru Anda.');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resendOtp()
    {
        $email = session('password_reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request');
        }

        $otp = sprintf("%06d", mt_rand(100000, 999999));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim ulang email OTP: ' . $e->getMessage());
        }

        Log::info("[DigiLib OTP Resend] Kode OTP baru untuk {$user->email} adalah: {$otp}");

        session(['otp_sent_at' => now()->timestamp]);

        return back()->with('success', 'Kode OTP baru telah berhasil dikirim ke email Anda.');
    }

    /**
     * Tampilkan formulir reset kata sandi baru
     */
    public function showResetPassword()
    {
        $email = session('otp_verified_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Silakan lakukan verifikasi OTP terlebih dahulu.',
            ]);
        }

        return view('auth.reset-password', [
            'email' => $email,
        ]);
    }

    /**
     * Simpan kata sandi baru ke database
     */
    public function resetPassword(Request $request)
    {
        $email = session('otp_verified_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi reset password Anda tidak valid. Silakan ulangi proses.',
            ]);
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Hapus token OTP dari database
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Bersihkan session reset
        session()->forget(['password_reset_email', 'otp_verified_email', 'otp_sent_at']);

        return redirect()->route('login')->with('success', 'Kata sandi akun Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
    }
}

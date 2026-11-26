<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Loan;

class SuperadminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalPustakawan = User::where('role', 'pustakawan')->count();
        $totalMembers = User::where('role', 'member')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalPustakawan',
            'totalMembers',
            'suspendedUsers',
            'recentUsers'
        ));
    }

    public function accounts(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $accounts = $query->latest()->get();

        return view('superadmin.manajemen-akun', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:member,pustakawan',
            'phone' => 'nullable|string|max:20',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $avatarUrl = null;
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $uploadDir = public_path('uploads/avatars');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $avatarUrl = asset('uploads/avatars/' . $filename);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
            'phone' => $request->phone,
            'avatar' => $avatarUrl,
        ]);

        return redirect()->route('superadmin.accounts')->with('success', 'Akun baru berhasil ditambahkan.');
    }

    public function updateAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:member,pustakawan',
            'phone' => 'nullable|string|max:20',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'delete_avatar' => 'nullable',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Hapus foto profil jika diminta
        if ($request->input('delete_avatar') == '1') {
            if ($user->avatar && str_contains($user->avatar, 'uploads/avatars/')) {
                $filename = basename(parse_url($user->avatar, PHP_URL_PATH));
                $oldPath = public_path('uploads/avatars/' . $filename);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $data['avatar'] = null;
        }

        // Unggah foto profil baru jika ada
        if ($request->hasFile('avatar_file')) {
            if ($user->avatar && str_contains($user->avatar, 'uploads/avatars/')) {
                $filename = basename(parse_url($user->avatar, PHP_URL_PATH));
                $oldPath = public_path('uploads/avatars/' . $filename);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('avatar_file');
            $uploadDir = public_path('uploads/avatars');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['avatar'] = asset('uploads/avatars/' . $filename);
        }

        $user->update($data);

        return redirect()->route('superadmin.accounts')->with('success', 'Data akun berhasil diperbarui.');
    }

    public function toggleSuspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->status === 'active') {
            $request->validate([
                'suspension_reason' => 'required|string|max:500',
            ]);

            $user->update([
                'status' => 'suspended',
                'suspension_reason' => $request->suspension_reason,
            ]);

            $msg = "Akun {$user->name} berhasil ditangguhkan.";
        } else {
            $user->update([
                'status' => 'active',
                'suspension_reason' => null,
            ]);

            $msg = "Akun {$user->name} berhasil diaktifkan kembali.";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function deleteAccount($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('superadmin.accounts')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}

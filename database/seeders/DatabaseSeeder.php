<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories Master Data (Pilihan Kategori untuk Tambah Buku)
        Category::create([
            'name' => 'Fiksi & Sastra',
            'slug' => 'fiksi-sastra',
            'description' => 'Koleksi novel, sastra klasik, antologi puisi, dan karya fiksi modern.',
        ]);

        Category::create([
            'name' => 'Pengembangan Diri',
            'slug' => 'pengembangan-diri',
            'description' => 'Buku psikologi populer, motivasi, kepemimpinan, dan produktivitas.',
        ]);

        Category::create([
            'name' => 'Sains & Teknologi',
            'slug' => 'sains-teknologi',
            'description' => 'Ilmu pengetahuan alam, komputer, pemrograman, matematika, dan teknologi.',
        ]);

        Category::create([
            'name' => 'Sejarah & Budaya',
            'slug' => 'sejarah-budaya',
            'description' => 'Buku historiografi Nusantara, peradaban dunia, antropologi, dan tradisi lokal.',
        ]);

        Category::create([
            'name' => 'Anak & Remaja',
            'slug' => 'anak-remaja',
            'description' => 'Buku cerita bergambar, ensiklopedia cilik, dan bacaan edukatif generasi muda.',
        ]);

        // 2. Seed 3 Users Utama (Password: "password")
        // Superadmin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@digilib.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'status' => 'active',
            'phone' => '0812-9988-7766',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80',
        ]);

        // Pustakawan: Rifky Syahreza
        User::create([
            'name' => 'Rifky Syahreza',
            'email' => 'syahreza@digilib.id',
            'password' => Hash::make('password'),
            'role' => 'pustakawan',
            'status' => 'active',
            'phone' => '0812-3456-7890',
            'avatar' => null,
        ]);

        // Member: Naoto Hachioji
        User::create([
            'name' => 'Naoto Hachioji',
            'email' => 'osassisus@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
            'phone' => '0857-1122-3344',
            'avatar' => null,
        ]);

        // TIDAK ADA BUKU & PINJAMAN YANG DI-SEED (KOSONG / FRESH)
    }
}

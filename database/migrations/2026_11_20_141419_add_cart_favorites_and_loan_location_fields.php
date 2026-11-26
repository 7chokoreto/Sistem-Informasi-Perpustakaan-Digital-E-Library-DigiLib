<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Buku Favorit
        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['user_id', 'book_id']);
            });
        }

        // 2. Tabel Keranjang Peminjaman
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
                $table->string('origin_location')->default('Perpustakaan Pusat DigiLib');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'book_id']);
            });
        }

        // 3. Tambahkan kolom lokasi ke tabel loans
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'origin_location')) {
                $table->string('origin_location')->nullable()->after('type');
            }
            if (!Schema::hasColumn('loans', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('origin_location');
            }
            if (!Schema::hasColumn('loans', 'pickup_method')) {
                $table->string('pickup_method')->default('Ambil Sendiri')->after('pickup_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('favorites');

        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'pickup_method')) {
                $table->dropColumn('pickup_method');
            }
            if (Schema::hasColumn('loans', 'pickup_location')) {
                $table->dropColumn('pickup_location');
            }
            if (Schema::hasColumn('loans', 'origin_location')) {
                $table->dropColumn('origin_location');
            }
        });
    }
};

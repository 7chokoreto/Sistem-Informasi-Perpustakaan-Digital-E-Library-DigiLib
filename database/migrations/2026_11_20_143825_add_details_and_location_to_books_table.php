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
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'edition')) {
                $table->string('edition')->nullable()->default('Cetakan Pertama')->after('publisher');
            }
            if (!Schema::hasColumn('books', 'physical_description')) {
                $table->string('physical_description')->nullable()->default('viii + 240 hlm. : Ilustrasi ; 21 cm.')->after('edition');
            }
            if (!Schema::hasColumn('books', 'language')) {
                $table->string('language')->default('Indonesia')->after('physical_description');
            }
            if (!Schema::hasColumn('books', 'location')) {
                $table->string('location')->default('Perpustakaan Jakarta Timur - Jatinegara')->after('shelf');
            }
            if (!Schema::hasColumn('books', 'read_in_place_stock')) {
                $table->integer('read_in_place_stock')->default(2)->after('stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['edition', 'physical_description', 'language', 'location', 'read_in_place_stock']);
        });
    }
};

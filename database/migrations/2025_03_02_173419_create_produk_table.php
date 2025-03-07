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
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('kategori_produk_id')->nullable()->references('id_kategori_produk')->on('kategori_produk')->cascadeOnDelete();
            $table->string('kode_produk',10)->nullable();
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_varian');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};

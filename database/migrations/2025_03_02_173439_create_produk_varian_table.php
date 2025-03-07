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
        Schema::create('produk_varian', function (Blueprint $table) {
            $table->id('id_produk_varian');
            $table->foreignId('produk_id')->references('id_produk')->on('produk')->cascadeOnDelete();
            $table->string('nama_produk_varian')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('harga_beli',15,2)->nullable()->default(0);
            $table->decimal('harga_jual',15,2)->nullable()->default(0);
            $table->integer('min_stok')->nullable()->default(0);
            $table->integer('max_stok')->nullable()->default(null);
            $table->integer('stok_sekarang')->nullable()->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_varian');
    }
};

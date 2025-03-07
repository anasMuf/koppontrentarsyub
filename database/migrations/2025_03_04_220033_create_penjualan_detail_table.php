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
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id('id_penjualan_detail');
            $table->foreignId('penjualan_id')->references('id_penjualan')->on('penjualan')->cascadeOnDelete();
            $table->foreignId('produk_varian_id')->references('id_produk_varian')->on('produk_varian')->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->decimal('harga_satuan',15,2)->default(0)->comment('harga jual');
            $table->decimal('sub_total',15,2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};

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
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->id('id_pembelian_detail');
            $table->foreignId('pembelian_id')->references('id_pembelian')->on('pembelian')->cascadeOnDelete();
            $table->foreignId('produk_varian_id')->references('id_produk_varian')->on('produk_varian')->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->decimal('harga_satuan',15,2)->default(0)->comment('harga beli');
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
        Schema::dropIfExists('pembelian_detail');
    }
};

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
        Schema::create('pembelian_pembayaran', function (Blueprint $table) {
            $table->id('id_pembelian_pembayaran');
            $table->foreignId('pembelian_id')->references('id_pembelian')->on('pembelian')->cascadeOnDelete();
            $table->decimal('nominal',15,2)->default(0)->comment('nominal dibayar');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_pembayaran');
    }
};

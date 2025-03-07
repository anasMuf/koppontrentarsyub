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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->foreignId('anggota_id')->references('id_anggota')->on('anggota')->cascadeOnDelete();
            $table->datetime('tanggal_penjualan')->nullable();
            $table->string('no_struk',20)->nullable();
            $table->decimal('total_penjualan',15,2)->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->enum('status_penjualan',['proses','gagal','berhasil'])->default('proses');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};

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
        Schema::create('iuran_anggota', function (Blueprint $table) {
            $table->id('id_iuran_anggota');

            $table->foreignId('jenis_iuran_id')->references('id_jenis_iuran')->on('jenis_iuran')->cascadeOnDelete();
            $table->foreignId('anggota_id')->references('id_anggota')->on('anggota')->cascadeOnDelete();
            $table->decimal('nominal',15,2);
            $table->dateTime('tanggal_bayar')->nullable();
            $table->date('periode_iuran');
            $table->enum('status_pembayaran',['belum_bayar','sudah_bayar'])->default('belum_bayar');
            $table->enum('metode_pembayaran',['transfer','cash'])->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran_anggota');
    }
};

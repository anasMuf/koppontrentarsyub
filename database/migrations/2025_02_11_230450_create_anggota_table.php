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
        Schema::create('anggota', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->foreignId('pendaftaran_anggota_id')->references('id_pendaftaran_anggota')->on('pendaftaran_anggota')->cascadeOnDelete();
            $table->string('no_anggota');
            $table->string('nama_lengkap');
            $table->string('no_ktp');
            $table->string('pekerjaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->date('tgl_bergabung')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->enum('status_anggota',['aktif','nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};

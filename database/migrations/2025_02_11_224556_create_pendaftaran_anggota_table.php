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
        Schema::create('pendaftaran_anggota', function (Blueprint $table) {
            $table->id('id_pendaftaran_anggota');
            $table->string('no_formulir');
            $table->date('tgl_pendaftaran')->nullable();
            $table->string('nama_lengkap');
            $table->string('no_ktp');
            $table->string('pekerjaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->enum('status_pendaftaran',['proses','tolak','terima'])->default('proses');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->datetime('tanggal_penolakan')->nullable();
            $table->datetime('tanggal_persetujuan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_anggota');
    }
};

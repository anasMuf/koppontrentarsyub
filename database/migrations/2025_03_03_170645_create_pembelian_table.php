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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->foreignId('supplier_id')->references('id_supplier')->on('supplier')->cascadeOnDelete();
            $table->datetime('tanggal_pembelian')->nullable();
            $table->string('no_faktur',20)->nullable();
            $table->decimal('total_pembelian',15,2)->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->enum('status_pembelian',['proses','gagal','berhasil'])->default('proses');
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
        Schema::dropIfExists('pembelian');
    }
};

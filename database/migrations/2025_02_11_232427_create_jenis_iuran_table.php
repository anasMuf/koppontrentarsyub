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
        Schema::create('jenis_iuran', function (Blueprint $table) {
            $table->id('id_jenis_iuran');
            $table->string('jenis_iuran');
            $table->decimal('nominal',15,2);
            $table->boolean('wajib_dibayar')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_iuran');
    }
};

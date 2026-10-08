<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20);
            $table->date('tgl_izin');
            $table->string('status', 1); // 'i' untuk izin, 's' untuk sakit
            $table->text('keterangan')->nullable();
            $table->tinyInteger('status_approved')->default(0); // 0: Pending, 1: Disetujui, 2: Ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
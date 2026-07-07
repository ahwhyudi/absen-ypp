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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Otomatis tipe data sesuai dengan id di users, cascade delete)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->date('date'); // Pengganti tanggal
            
            // Absen Masuk
            $table->time('check_in')->nullable(); // Pengganti jam_masuk
            $table->decimal('latitude_in', 10, 7)->nullable(); // Pengganti lintang_masuk (Lebih presisi)
            $table->decimal('longitude_in', 11, 8)->nullable(); // Pengganti bujur_masuk
            $table->string('photo_in')->nullable(); // Pengganti jalur_foto_masuk (path foto)
            
            // Absen Pulang
            $table->time('check_out')->nullable(); // Pengganti jam_pulang
            $table->decimal('latitude_out', 10, 7)->nullable(); // Pengganti lintang_pulang
            $table->decimal('longitude_out', 11, 8)->nullable(); // Pengganti bujur_pulang
            $table->string('photo_out')->nullable(); // Pengganti jalur_foto_pulang
            
            // Tambahan jangka panjang: Untuk mempermudah rekap/filter laporan bulanan
            $table->enum('status_in', ['present', 'late', 'absent'])->default('present');
            $table->enum('status_out', ['early_leave', 'on_time'])->nullable();

            // --- TAMBAHAN BARU: Catatan & Keamanan ---
            $table->text('note_in')->nullable(); // Alasan telat masuk
            $table->text('note_out')->nullable(); // Alasan pulang cepat
            $table->ipAddress('ip_address')->nullable(); // Lacak IP (Anti nitip absen/joki)
            $table->text('user_agent')->nullable(); // Lacak Device/Browser yang dipakai
            // -----------------------------------------

            // Kumpulan index untuk optimasi query (Sesuai dengan index di native lu)
            $table->unique(['user_id', 'date']); // Menggantikan uniq_pengguna_tanggal
            $table->index('date'); // Menggantikan idx_tanggal
            
            // Otomatis membuat created_at dan updated_at (updated_at menggantikan idx_updated_at native lu)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
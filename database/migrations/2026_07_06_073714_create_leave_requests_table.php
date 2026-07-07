<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke user yang mengajukan izin
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Jenis izin (permission = Izin, sick = Sakit, leave = Cuti)
            $table->enum('type', ['permission', 'sick', 'leave']); 
            
            $table->date('start_date')->index(); // Menggantikan tanggal_mulai + index
            $table->date('end_date'); // Menggantikan tanggal_selesai
            
            $table->text('reason'); // Menggantikan keterangan
            
            // Path dokumen pendukung (Surat sakit/dokumen cuti)
            $table->string('document_path')->nullable(); 
            
            // Status pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            
            // --- TAMBAHAN UNTUK JANGKA PANJANG (AUDIT TRAIL) ---
            // Relasi ke user yang menyetujui/menolak (HRD/Manajer)
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Catatan kenapa ditolak / disetujui
            $table->text('approval_notes')->nullable(); 
            // ----------------------------------------------------

            // Otomatis mencatat kapan diajukan (created_at) dan kapan status diupdate (updated_at)
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
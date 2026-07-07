<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequests extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'approval_notes',
    ];

    /**
     * Relasi ke karyawan yang mengajukan izin
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke HRD/Manajer yang menyetujui izin
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
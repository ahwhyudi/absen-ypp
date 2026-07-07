<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];


    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    /**
     * Relasi Kebalikan: Satu data absen ini dimiliki oleh satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
